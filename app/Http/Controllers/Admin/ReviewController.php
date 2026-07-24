<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\AppUser;
use App\Models\Modern\Item;
use App\Models\Module;
use App\Models\Review;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('review_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $reciver = request()->input('reciver');
        $sender = request()->input('sender');
        $itemdata = request()->input('item');
        $currentModule = Module::where('default_module', '1')->first();

        $query = Review::with(['guest', 'host', 'item', 'booking'])
            ->where('module', $currentModule->id)
            ->orderBy('id', 'desc');

        if ($reciver) {
            $query->where('hostid', $reciver);
        }

        if ($sender) {
            $query->where('guestid', $sender);
        }

        if ($itemdata) {
            $query->where('item_id', $itemdata);
        }

        $ReviveData = $query->paginate(50);

        $ReviveData = $query->paginate(50);

        $fielddata = request()->input('reciver');
        $fieldname = AppUser::find($fielddata);
        $reciverName = $fieldname ? $fieldname->first_name : 'All';
        $reciverId = $fieldname ? $fieldname->id : '';

        $senderdata = request()->input('sender');
        $senderDataInfo = AppUser::find($senderdata);
        $senderName = $senderDataInfo ? $senderDataInfo->first_name : 'All';
        $senderId = $senderDataInfo ? $senderDataInfo->id : '';

        $itemdata = request()->input('item');
        $itemDataInfo = Item::find($itemdata);
        $itemName = $itemDataInfo ? $itemDataInfo->title : 'All';
        $itemId = $itemDataInfo ? $itemDataInfo->id : '';

        return view('admin.reviews.index', compact('ReviveData', 'reciverName', 'reciverId', 'senderName', 'senderId', 'itemName', 'itemId', 'currentModule'));
    }

    public function create()
    {
        abort_if(Gate::denies('review_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reviews.create');
    }

    public function store(StoreReviewRequest $request)
    {

        $review = Review::create($request->all());

        return redirect()->route('admin.reviews.index');
    }

    public function edit(Review $review)
    {
        abort_if(Gate::denies('review_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $reviewId = $review->id;
        $reviewList = Review::find($reviewId);
        \Log::info('Review details:', ['reviewList' => $reviewList]);

        return view('admin.reviews.edit', compact('reviewList'));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update($request->all());

        return redirect()->route('admin.reviews.index');
    }

    public function show(Review $review)
    {
        abort_if(Gate::denies('review_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reviews.show', compact('review'));
    }

    public function delete($id)
    {
        abort_if(Gate::denies('review_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $review = Review::find($id);

        if (! $review) {
            return redirect()->route('admin.reviews.index')->with('error', 'Review not found');
        }
        $review->delete();

        return redirect()->route('admin.reviews.index');
    }
}
