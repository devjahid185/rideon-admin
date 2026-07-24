<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\AppUser;
use App\Models\Contact;
use App\Models\Module;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $status = request()->input('status');
        $user = request()->input('user');
        $currentModule = Module::where('default_module', '1')->first();
        $query = Contact::with('appUser');
        $isFiltered = ($status || $user);

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($user !== null) {
            $query->where('user', $user);
        }   $query->where('module', $currentModule->id);
        $query->orderBy('contact_us.id', 'desc');
        $contactdata = $isFiltered ? $query->paginate(50) : $query->paginate(50);
        $fieldname = AppUser::find($user);

        if ($fieldname) {
            $userName = $fieldname->first_name.' '.$fieldname->last_name.'('.$fieldname->phone.')';
        } else {
            $userName = 'All';
        }

        return view('admin.contacts.index', compact('contactdata', 'userName'));
    }

    public function create()
    {
        abort_if(Gate::denies('contact_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = AppUser::all();

        return view('admin.contacts.create', compact('userids'));
    }

    public function store(StoreContactRequest $request)
    {

        $contact = Contact::create($request->all());

        return redirect()->route('admin.contacts.index');
    }

    public function edit(Contact $contact)
    {
        abort_if(Gate::denies('contact_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $contact = Contact::where('id', $contact->id)->with('appUser')->first();
        $userids = AppUser::all();

        return view('admin.contacts.edit', compact('contact', 'userids'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->all());

        return redirect()->route('admin.contacts.index');
    }

    public function show(Contact $contact)
    {

        $contact = Contact::where('id', $contact->id)->with('appUser')->first();

        abort_if(Gate::denies('contact_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contacts.show', compact('contact'));
    }

    public function updateStatus(Request $request)
    {

        $product_status = Contact::where('id', $request->pid)->update(['status' => $request->status]);
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

    public function delete($id)
    {

        $contact = Contact::find($id);

        if (! $contact) {
            return redirect()->route('admin.contacts.index')->with('error', 'Contact not found');
        }
        $contact->delete();

        return redirect()->route('admin.contacts.index');
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                Contact::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
