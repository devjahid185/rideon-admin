<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\AllPackage;
use App\Models\AppUser;
use App\Models\AppUsersBankAccount;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Modern\ItemType;
use App\Models\Modern\RentalItemServiceType;
use App\Models\Module;
use App\Models\Payout;
use App\Models\User;
use App\Models\VendorWallet;
use App\Models\Wallet;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    use EmailTrait, MediaUploadingTrait, NotificationTrait, PushNotificationTrait, ResponseTrait, SMSTrait, UserWalletTrait, VendorWalletTrait;

    public function index()
    {
        abort_if(Gate::denies('booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = request()->input('from');
        $to = request()->input('to');
        $item = request()->input('item');
        $host = request()->input('host');
        $customer = request()->input('customer');
        $status = request()->input('status');
        $serviceType = request()->input('service_type');

        $isTrash = \Route::currentRouteName() === 'admin.bookings.trash';

        // Base query for listing
        $query = Booking::with([
            'host:id,first_name,last_name,phone,phone_country',
            'user:id,first_name,last_name,phone,phone_country',
            'item:id,title,item_type_id,make,model,registration_number',
            'extension.serviceType',
            'item.itemVehicle',
            'item.item_Type',
            'item.vehicleMake',
            'host.media',
            'user.media',
        ]);

        if ($isTrash) {
            $query->onlyTrashed(); // Show only trashed bookings in listing
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $query->where('created_at', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', $to.' 23:59:59');
        }

        if ($host) {
            $query->where('host_id', $host);
        }
        if ($customer) {
            $query->where('userid', $customer);
        }
        if ($item) {
            $query->where('itemid', $item);
        }

        $validStatuses = ['pending', 'confirmed', 'ongoing', 'cancelled', 'declined', 'completed', 'refunded', 'accepted', 'rejected'];
        if ($status && in_array($status, $validStatuses)) {
            $query->where('status', $status);
        }
        if ($serviceType) {
            $query->whereHas('extension', function ($q) use ($serviceType) {
                $q->where('service_type_id', $serviceType);
            });
        }
        $countsQuery = Booking::query();

        if ($serviceType) {
            $countsQuery->whereHas('extension', function ($q) use ($serviceType) {
                $q->where('service_type_id', $serviceType);
            });
        }

        if ($from && $to) {
            $countsQuery->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $countsQuery->where('created_at', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $countsQuery->where('created_at', '<=', $to.' 23:59:59');
        }

        if ($host) {
            $countsQuery->where('host_id', $host);
        }
        if ($customer) {
            $countsQuery->where('userid', $customer);
        }
        if ($item) {
            $countsQuery->where('itemid', $item);
        }

        $statusCountsRaw = (clone $countsQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'all' => array_sum($statusCountsRaw),
            'ongoing' => $statusCountsRaw['Ongoing'] ?? 0,
            'pending' => $statusCountsRaw['Pending'] ?? 0,
            'accepted' => $statusCountsRaw['Accepted'] ?? 0,
            'confirmed' => $statusCountsRaw['Confirmed'] ?? 0,
            'cancelled' => $statusCountsRaw['Cancelled'] ?? 0,
            'completed' => $statusCountsRaw['Completed'] ?? 0,
            'rejected' => $statusCountsRaw['Rejected'] ?? 0,
            'declined' => $statusCountsRaw['Declined'] ?? 0,
            'refunded' => $statusCountsRaw['Refunded'] ?? 0,
            'trash' => Booking::onlyTrashed()
                ->when($serviceType, function ($q) use ($serviceType) {
                    $q->whereHas('extension', function ($sub) use ($serviceType) {
                        $sub->where('service_type_id', $serviceType);
                    });
                })
                ->count(),
        ];

        $query = $query->orderBy('id', 'desc');
        $bookings = $query->paginate(50);

        $queryParameters = array_filter([
            'status' => $status,
            'to' => $to,
            'from' => $from,
            'host' => $host,
            'customer' => $customer,
            'item' => $item,
            'service_type' => $serviceType,
        ]);
        $bookings->appends($queryParameters);

        $hostData = $host ? cache()->remember("host_{$host}", now()->addHours(24), fn () => AppUser::find($host)) : null;
        $searchfield = $hostData ? $hostData->first_name : 'All';
        $searchfieldId = $hostData ? $hostData->id : '';

        $customerData = $customer ? cache()->remember("customer_{$customer}", now()->addHours(24), fn () => AppUser::find($customer)) : null;
        $searchCustomer = $customerData ? $customerData->first_name : 'All';
        $searchCustomerId = $customerData ? $customerData->id : '';

        $itemData = $item ? cache()->remember("item_{$item}", now()->addHours(24), fn () => Item::find($item)) : null;
        $searchfieldItem = $itemData ? $itemData->title : 'All';
        $searchfieldItemId = $itemData ? $itemData->id : '';

        $general_default_currency = cache()->remember('general_default_currency', now()->addHours(24), fn () => View::shared('general_default_currency'));
        $serviceTypes = RentalItemServiceType::where('status', 1)->get();

        return view('admin.bookings.index', compact(
            'bookings',

            'searchCustomer',
            'searchCustomerId',
            'statusCounts',
            'searchfieldItem',
            'searchfieldItemId',
            'searchfield',
            'searchfieldId',
            'general_default_currency',
            'serviceTypes',
            'serviceType'
        ));
    }

    public function create()
    {
        abort_if(Gate::denies('booking_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hosts = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.bookings.create', compact('hosts'));
    }

    public function store(Request $request)
    {

        $booking = Booking::create($request->all());

        return redirect()->route('admin.bookings.index');
    }

    public function edit(Booking $booking)
    {
        abort_if(Gate::denies('booking_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hosts = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $booking->load('host');

        return view('admin.bookings.edit', compact('booking', 'hosts'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->all());

        return redirect()->route('admin.bookings.index');
    }

    public function show(Booking $booking)
    {
        abort_if(Gate::denies('booking_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $bookingId = $booking->id;
        $bookingData = Booking::with(['host:id,first_name,last_name,phone,phone_country,ave_host_rate,email', 'user:id,first_name,last_name,phone,phone_country,email,avr_guest_rate', 'item:id,title,item_type_id,make,model,registration_number'])
            ->where('id', $bookingId)
            ->orderBy('id', 'desc')
            ->first();
        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.bookings.show', compact('bookingData', 'general_default_currency'));
    }

    // customer
    public function customerItem(Request $request)
    {
        $customerName = $request->input('q'); // Retrieve the search term from the request

        $items = Item::with('appUser')
            ->whereHas('appUser', function ($query) use ($customerName) {
                $query->where('first_name', 'like', '%'.$customerName.'%');
            })
            ->distinct()
            ->get();

        // Prepare the response data
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->userid_id,
                'name' => $item->title,
                'customer_name' => $item->appUser->first_name,
            ];
        }

        return response()->json($data);
    }

    public function conditioncheck(Request $request, $booking)
    {

        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');

        $vendor_wallets = VendorWallet::with('appUser')
            ->where('vendor_id', $booking)
            ->orderBy('id', 'desc')
            ->paginate(50);

        $userType = $request->query('user_type');
        $user = AppUser::where('id', $booking)->first();
        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        $hostspendmoney = number_format($this->getVendorWalletBalance($booking), 2);
        $hostpendingmoney = number_format($this->getTotalWithdrawlForVendor($booking, 'Pending'), 2);
        $hostrecivemoney = number_format($this->getTotalWithdrawlForVendor($booking, 'Success'), 2);

        $totalmoney = number_format($this->getTotalEarningsForVendor($booking), 2);
        $refunded = number_format($this->getTotalRefundForVendor($booking, ''), 2);

        return view('admin.overviewcustomer.index', compact('booking', 'user', 'hostspendmoney', 'hostpendingmoney', 'hostrecivemoney', 'totalmoney', 'refunded', 'vendor_wallets', 'general_default_currency'));
    }

    public function items(Request $request, $userID)
    {
        $item = Item::with(['itemVehicle'])->where('userid_id', $userID)->firstOrFail();

        $itemVehicle = $item->itemVehicle;
        $vehicleTypeData = ItemType::where('module', 2)->get();
        $user = AppUser::where('id', $userID)->first();
        $YearData = $itemVehicle->year ?? null;
        $vehicleNumber = $itemVehicle->vehicle_registration_number ?? null;
        $MakeData = $item->category_id;
        $ModelData = $item->subcategory_id;
        $booking = $userID;
        $storeMedia = 'admin.storeMedia';

        return view('admin.overviewcustomer.item', compact(
            'item',
            'user',
            'booking',
            'storeMedia',
            'vehicleTypeData',
            'YearData',
            'vehicleNumber',
            'MakeData',
            'ModelData'
        ));
    }

    public function overviewprofile(Request $request, $booking)
    {
        abort_if(Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appUser = AppUser::findOrFail($booking);
        $user = $appUser;
        $packages = AllPackage::pluck('package_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $appUser->load('package');

        return view('admin.overviewcustomer.profile', compact('booking', 'user', 'appUser', 'packages'));
    }

    public function orders(Request $request, $booking)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $item = $request->input('item');
        $status = $request->input('status');
        $user = AppUser::where('id', $booking)->first();
        $query = Booking::with(['host', 'user', 'item'])
            ->where('host_id', $booking)
            ->where('payment_status', 'paid')
            ->orderByRaw('(CASE WHEN check_in >= CURDATE() THEN 0 ELSE 1 END)')
            ->orderBy('check_in', 'asc');

        if ($from && $to) {
            $query->whereBetween('check_in', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $query->where('check_out', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $query->where('check_out', '<=', $to.' 23:59:59');
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($item) {
            $query->where('itemid', $item);
        }

        // $query->distinct();

        $filteredBookingsQuery = clone $query;

        $totalSum = $filteredBookingsQuery->distinct()->sum('total');
        $totalBookings = $filteredBookingsQuery->distinct()->count('bookings.id');

        $filteredBookingsQuery = clone $query;
        $totalCustomers = $filteredBookingsQuery->distinct('userid')->count('userid');

        $totalEarningsQuery = clone $query;
        $totalEarnings = $totalEarningsQuery->whereIn('status', ['Confirmed'])->sum('total');

        $bookings = $query->paginate(50);

        $fielddataitem = $request->input('item');
        $fieldnameitem = Item::find($fielddataitem);
        $searchfielditem = $fieldnameitem ? $fieldnameitem->title : 'All';
        $searchfielditemId = $fieldnameitem ? $fieldnameitem->id : '';

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.overviewcustomer.orders', compact('booking', 'user', 'bookings', 'searchfielditem', 'searchfielditemId', 'general_default_currency', 'totalSum', 'totalBookings', 'totalCustomers', 'totalEarnings'));
    }

    public function bookings(Request $request, $booking)
    {

        $from = request()->input('from');
        $to = request()->input('to');
        $item = request()->input('item');
        $customer = request()->input('customer');
        $status = request()->input('status');

        $user = AppUser::where('id', $booking)->first();
        $query = Booking::with(['item', 'host', 'user'])
            ->where('userid', $booking)
            ->where('payment_status', 'paid');
        // Apply filters
        if ($from && $to) {
            $query->whereBetween('updated_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $query->where('updated_at', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $query->where('updated_at', '<=', $to.' 23:59:59');
        }

        if ($status == 'pending') {
            $query->where('status', 'pending');
        }
        if ($status == 'confirmed') {
            $query->where('status', 'confirmed');
        }
        if ($status == 'cancelled') {
            $query->where('status', 'cancelled');
        }
        if ($status == 'declined') {
            $query->where('status', 'declined');
        }
        if ($status == 'completed') {
            $query->where('status', 'completed');
        }
        if ($status == 'refunded') {
            $query->where('status', 'refunded');
        }
        if ($customer) {
            $query->where('userid', $customer);
        }
        if ($item) {
            $query->where('itemid', $item);
        }

        // Use DISTINCT to ensure unique records
        // $query->distinct();
        $filteredBookingsQuery = clone $query;

        $totalSum = $filteredBookingsQuery->distinct()->sum('total');
        $totalBookings = $filteredBookingsQuery->distinct()->count('bookings.id');

        $filteredBookingsQuery = clone $query;
        $totalCustomers = $filteredBookingsQuery->distinct('userid')->count('userid');

        $totalEarningsQuery = clone $query;
        $totalEarnings = $totalEarningsQuery->sum('total');

        // Use paginate to fetch records in sets of 50 per page
        $bookings = $query->paginate(50);

        // select2 case data

        $fielddata = request()->input('customer');
        $fieldname = AppUser::find($fielddata);
        $searchfield = $fieldname ? $fieldname->first_name : 'All';

        $fielddataItem = request()->input('item');
        $fieldnameItem = Item::find($fielddataItem);
        $searchfieldItem = $fieldnameItem ? $fieldnameItem->title : 'All';

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.overviewcustomer.bookings', compact('booking', 'user', 'totalEarnings', 'bookings', 'totalCustomers', 'totalBookings', 'totalSum', 'searchfieldItem', 'searchfield', 'general_default_currency'));
    }

    public function payouts(Request $request, $booking)
    {

        abort_if(Gate::denies('payout_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');
        $user = AppUser::where('id', $booking)->first();
        $query = Payout::with('vendor')
            ->where('vendorid', $booking);

        // Check if any search parameter is provided
        $isFiltered = ($from || $to || $status);
        if ($from && $to) {
            $query->where(function ($query) use ($from, $to) {
                $query->whereBetween('payouts.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->orWhereBetween('payouts.updated_at', [$from.' 00:00:00', $to.' 23:59:59']);
            });
        } elseif ($from) {
            $query->where(function ($query) use ($from) {
                $query->where('payouts.created_at', '>=', $from.' 00:00:00')
                    ->orWhere('payouts.updated_at', '>=', $from.' 00:00:00');
            });
        } elseif ($to) {
            $query->where(function ($query) use ($to) {
                $query->where('payouts.created_at', '<=', $to.' 23:59:59')
                    ->orWhere('payouts.updated_at', '<=', $to.' 23:59:59');
            });
        }

        // Apply the status filter if 'status' is provided
        if ($status !== null) {
            $query->where('payout_status', $status);
        }

        $payouts = $isFiltered ? $query->paginate(50) : $query->paginate(50);

        return view('admin.overviewcustomer.payouts', compact('payouts', 'user', 'booking'));
    }

    // Bank Account
    public function bankAccount(Request $request, $booking)
    {

        abort_if(Gate::denies('payout_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');
        $user = AppUser::where('id', $booking)->first();
        // Query to fetch bank account details from 'app_users_bank_accounts'
        $accounts = AppUsersBankAccount::with('user')->where('user_id', $booking)->get();

        return view('admin.overviewcustomer.bank', compact('accounts', 'user', 'booking'));
    }

    public function walletbkp(Request $request, $booking)
    {
        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');

        $query = Wallet::where('user_id', $booking)->join('app_users', 'wallets.user_id', '=', 'app_users.id')->select('wallets.*', 'app_users.first_name', 'app_users.last_name');

        $isFiltered = ($from || $to || $status);
        if ($from && $to) {
            $query->where(function ($query) use ($from, $to) {
                $query->whereBetween('wallets.created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->orWhereBetween('wallets.updated_at', [$from.' 00:00:00', $to.' 23:59:59']);
            });
        } elseif ($from) {
            $query->where(function ($query) use ($from) {
                $query->where('wallets.created_at', '>=', $from.' 00:00:00')
                    ->orWhere('wallets.updated_at', '>=', $from.' 00:00:00');
            });
        } elseif ($to) {
            $query->where(function ($query) use ($to) {
                $query->where('wallets.created_at', '<=', $to.' 23:59:59')
                    ->orWhere('wallets.updated_at', '<=', $to.' 23:59:59');
            });
        }
        if ($status !== null) {
            $query->where('wallets.status', $status);
        }

        $wallets = $isFiltered ? $query->paginate(50) : $query->paginate(50);

        return view('admin.overviewcustomer.wallet', compact('wallets', 'booking'));
    }

    public function wallet(Request $request, $booking)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');
        $user = AppUser::where('id', $booking)->first();
        // Start the query with Eloquent
        $query = Wallet::where('user_id', $booking)->with('appUser'); // 'user' is the relationship method in Wallet model

        // Check if any filters are applied
        if ($from && $to) {
            $query->where(function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
                    ->orWhereBetween('updated_at', [$from.' 00:00:00', $to.' 23:59:59']);
            });
        } elseif ($from) {
            $query->where(function ($query) use ($from) {
                $query->where('created_at', '>=', $from.' 00:00:00')
                    ->orWhere('updated_at', '>=', $from.' 00:00:00');
            });
        } elseif ($to) {
            $query->where(function ($query) use ($to) {
                $query->where('created_at', '<=', $to.' 23:59:59')
                    ->orWhere('updated_at', '<=', $to.' 23:59:59');
            });
        }

        // Apply status filter
        if ($status !== null) {
            $query->where('status', $status);
        }

        // Paginate results
        $wallets = $query->paginate(50);

        return view('admin.overviewcustomer.wallet', compact('wallets', 'user', 'booking'));
    }

    public function getVerificationDocumentsOverView(Request $request, $booking)
    {
        $user = AppUser::find($booking);

        return view('admin.overviewcustomer.document', compact('user', 'booking'));
    }

    public function updateStatus(Request $request)
    {

        $product_status = Wallet::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'ststus' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'ststus' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentModule = Module::where('default_module', '1')->first();

        $booking = Booking::findOrFail($id);

        if ($booking->module == $currentModule->id) {
            $booking->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: Booking cannot be deleted from this module',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function listingsTrash()
    {

        if (Gate::denies('trash_booking_access')) {
            return redirect()->back()->with('error', 'You do not have permission to access this feature in demo mode.');
        }

        $from = request()->input('from');
        $to = request()->input('to');
        $item = request()->input('item');
        $customer = request()->input('customer');
        $status = request()->input('status');
        $module = request()->input('module');
        $currentModule = Module::where('default_module', '1')->first();
        $queryLive = Booking::with(['host:id,first_name,last_name', 'user:id,first_name,last_name', 'item:id,title'])
            ->where('module', $currentModule->id)
            ->orderBy('id', 'desc');

        $statusCounts = [
            'all' => (clone $queryLive)->where('bookings.status', '!=', 'trash')->count(),
            'ongoing' => (clone $queryLive)->where('bookings.status', 'ongoing')->count(),
            'pending' => (clone $queryLive)->where('bookings.status', 'pending')->count(),
            'accepted' => (clone $queryLive)->where('bookings.status', 'accepted')->count(),
            'confirmed' => (clone $queryLive)->where('bookings.status', 'confirmed')->count(),
            'cancelled' => (clone $queryLive)->where('bookings.status', 'cancelled')->count(),
            'completed' => (clone $queryLive)->where('bookings.status', 'completed')->count(),
            'rejected' => (clone $queryLive)->where('bookings.status', 'rejected')->count(),
            'trash' => (clone $queryLive)->onlyTrashed('status', 'trash')->count(),

        ];
        $query = Booking::onlyTrashed()
            ->with(['host:id,first_name,last_name', 'user:id,first_name,last_name', 'item:id,title'])
            ->where('module', $currentModule->id)
            ->orderBy('id', 'desc');
        $statusCounts['trash'] = $query->count();
        // Apply date range filter
        if ($from && $to) {
            $query->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $query->where('created_at', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', $to.' 23:59:59');
        }

        // Apply other filters
        if ($customer) {
            $query->where('userid', $customer);
        }
        if ($item) {
            $query->where('itemid', $item);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $filteredBookingsQuery = clone $query;
        $totalSum = $filteredBookingsQuery->sum('total');
        $totalBookings = $filteredBookingsQuery->count();
        $bookings = $query->paginate(50);
        $queryParameters = array_filter([
            'status' => $status,
            'to' => $to,
            'from' => $from,
            'customer' => $customer,
            'item' => $item,
        ]);

        $bookings->appends($queryParameters);
        $currency_code = Booking::first();
        $fielddata = request()->input('customer');
        $fieldname = AppUser::find($fielddata);
        $searchfield = $fieldname ? $fieldname->first_name : 'All';

        $fielddataitem = request()->input('item');
        $fieldnameitem = Item::find($fielddataitem);
        $searchfieldItem = $fieldnameitem ? $fieldnameitem->title : 'All';
        $searchCustomer = 'All';
        $searchCustomerId = '';
        $totalCustomers = '';
        $totalEarnings = '';
        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.bookings.trash', compact('bookings', 'totalBookings', 'totalSum', 'statusCounts', 'searchfieldItem', 'searchfield', 'general_default_currency', 'currentModule', 'searchCustomer', 'searchCustomerId', 'totalCustomers', 'general_default_currency'));
    }

    public function restoreTrash($id)
    {
        $item = Booking::onlyTrashed()->findOrFail($id);
        $item->restore();
    }

    public function permanentDelete(Request $request, $id)
    {
        abort_if(Gate::denies('booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentModule = Module::where('default_module', '1')->first();

        $booking = Booking::withTrashed()->findOrFail($id);

        if ($booking->module == $currentModule->id) {

            $booking->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking permanently deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: Booking cannot be deleted from this module',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function bookingDeleteAll(Request $request)
    {
        abort_if(Gate::denies('booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                Booking::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }

    public function bookngTrashAll(Request $request)
    {
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {

                Booking::onlyTrashed()->whereIn('id', $ids)->forceDelete();

                return response()->json(['message' => 'Items deleted from trash successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
