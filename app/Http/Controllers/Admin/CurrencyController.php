<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modern\Currency;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $currencies = Currency::all();
        // return view('admin.currency.index', compact('currencies'));

        abort_if(Gate::denies('currency_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Currency::query()->select('currency.*');
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'currency_show';
                $editGate = 'currency_edit';
                $deleteGate = 'currency_delete';
                $crudRoutePart = 'currency';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('currency_name', function ($row) {
                return $row->currency_name ? $row->currency_name : '';
            });
            $table->editColumn('currency_code', function ($row) {
                return $row->currency_code ? $row->currency_code : '';
            });
            $table->editColumn('value_against_default_currency', function ($row) {
                return $row->value_against_default_currency ? $row->value_against_default_currency : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('currency_symbol', function ($row) {
                return $row->currency_symbol ? $row->currency_symbol : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.currency.index');

    }

    public function create()
    {
        return view('admin.currency.create');
    }

    /**
     * Store a newly created currency in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'currency_name' => 'required|string',
            'currency_code' => 'required|string',
            'value_against_default_currency' => 'numeric',
            'currency_symbol' => 'string',
            'status' => 'required',
        ]);

        $currency = Currency::create($validatedData);

        return redirect()->route('admin.currency');
    }

    public function edit(Currency $currency)
    {

        return view('admin.currency.edit', compact('currency'));
    }

    /**
     * Display the specified currency.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        return view('admin.currency.show', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        $validatedData = $request->validate([
            'currency_name' => 'required|string',
            'currency_code' => 'required|string',
            'value_against_default_currency' => 'numeric',
            'status' => 'required',
        ]);

        $currency->update($validatedData);

        return redirect()->route('admin.currency');
    }

    /**
     * Remove the specified currency from storage.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('currency_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currencies = Currency::find(request('ids'));

        foreach ($currencies as $currency) {
            $currency->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('currency_edit')) {
            return response()->json(['status' => 403, 'message' => "You don't have permission to perform this action."]);
        }

        $currency_status = Currency::where('id', $request->pid)->update(['status' => $request->status]);
        if ($currency_status) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'id' => $request->pid,
                'message' => 'something went wrong. Please try again.',
            ]);
        }

    }
}
