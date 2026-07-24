<?php

namespace App\Http\Controllers\Traits\Vendor;

use App\Models\Modern\Item;
use Symfony\Component\HttpFoundation\Response;

trait VendorMiscellaneousTrait
{
    /**
     * Format the item data with front image.
     *
     * @param  \App\Models\Item  $item
     * @return array
     */
    public function vendorItemAuthentication(int $itemId)
    {
        $item = Item::find($itemId);

        abort_if(! $item || $item->userid_id !== auth()->user()->id, Response::HTTP_FORBIDDEN, '403 Forbidden');
    }
}
