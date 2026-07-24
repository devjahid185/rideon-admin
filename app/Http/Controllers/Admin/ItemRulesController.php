<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreItemRulesRequest;
use App\Http\Requests\UpdateItemRulesRequest;
use App\Models\Module;
use App\Models\RentalItemRule;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemRulesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        $module = Module::where('default_module', '1')->first();
        $itemRolesData = RentalItemRule::with('moduleGet')->where('module', $module->id)->orderby('id', 'desc')->get();

        return view('admin.item-roles.index', compact('itemRolesData'));

    }

    public function create()
    {
        $moduleData = Module::All();

        return view('admin.item-roles.create', compact('moduleData'));
    }

    public function store(StoreItemRulesRequest $request)
    {
        $itemRule = RentalItemRule::create($request->all());

        return redirect()->route('admin.item-rule.index');
    }

    public function edit($id)
    {
        $moduleData = Module::All();
        $itemRoledata = RentalItemRule::where('id', $id)->first();

        return view('admin.item-roles.edit', compact('itemRoledata', 'moduleData'));
    }

    public function update(UpdateItemRulesRequest $request, RentalItemRule $itemRule)
    {
        $itemRule->update($request->all());

        return redirect()->route('admin.item-rule.index');
    }

    public function show($id)
    {
        $itemRoledata = RentalItemRule::where('id', $id)->first();

        return view('admin.item-roles.show', compact('itemRoledata'));
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('item_rule_edit')) {
            return response()->json(['message' => "You don't have permission to perform this action."]);
        }
        $product_status = RentalItemRule::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }

    }

    public function delete($id)
    {

        $iteamRules = RentalItemRule::find($id);

        if (! $iteamRules) {
            return redirect()->route('admin.item-rule.index')->with('error', 'Iteam Rules not found');
        }
        $iteamRules->delete();

        return redirect()->route('admin.item-rule.index');
    }

    public function allDelete(Request $request)
    {
        abort_if(Gate::denies('item_rule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                RentalItemRule::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
