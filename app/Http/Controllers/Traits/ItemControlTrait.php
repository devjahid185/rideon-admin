<?php

namespace App\Http\Controllers\Traits;

use App\Models\Modern\Item;

trait ItemControlTrait
{
    /**
     * Permanently delete a item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permanentDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return redirect()->route('admin.common.trash.trash')->with('success', 'item permanently deleted successfully.');
    }
}
