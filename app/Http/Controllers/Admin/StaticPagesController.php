<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStaticPageRequest;
use App\Http\Requests\StoreStaticPageRequest;
use App\Http\Requests\UpdateStaticPageRequest;
use App\Models\StaticPage;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StaticPagesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('static_page_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StaticPage::query()->select(sprintf('%s.*', (new StaticPage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'static_page_show';
                $editGate = 'static_page_edit';
                $deleteGate = 'static_page_delete';
                $crudRoutePart = 'static-pages';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? StaticPage::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.staticPages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('static_page_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.staticPages.create');
    }

    public function store(StoreStaticPageRequest $request)
    {
        $staticPage = StaticPage::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $staticPage->id]);
        }

        return redirect()->route('admin.static-pages.index');
    }

    public function edit(StaticPage $staticPage)
    {
        abort_if(Gate::denies('static_page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.staticPages.edit', compact('staticPage'));
    }

    public function update(UpdateStaticPageRequest $request, StaticPage $staticPage)
    {
        $staticPage->update($request->all());

        return redirect()->route('admin.static-pages.index');
    }

    public function show(StaticPage $staticPage)
    {
        abort_if(Gate::denies('static_page_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.staticPages.show', compact('staticPage'));
    }

    public function destroy(StaticPage $staticPage)
    {
        abort_if(Gate::denies('static_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $staticPage->delete();

        return back();
    }

    public function massDestroy(MassDestroyStaticPageRequest $request)
    {
        abort_if(Gate::denies('static_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $staticPages = StaticPage::find(request('ids'));

        foreach ($staticPages as $staticPage) {
            $staticPage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('static_page_create') && Gate::denies('static_page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new StaticPage;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('static_page_edit')) {
            return response()->json(['status' => 403, 'message' => "You don't have permission to perform this action."]);
        }

        $product_status = StaticPage::where('id', $request->pid)->update(['status' => $request->status]);
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
}
