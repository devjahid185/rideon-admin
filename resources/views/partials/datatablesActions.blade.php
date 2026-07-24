@can($viewGate)
    <a class="btn btn-xs btn-primary" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
    <i class="fa fa-eye" aria-hidden="true"></i>
    </a>
@endcan
@can($editGate)
    <a class="btn btn-xs btn-info" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
    <i class="fa fa-pencil" aria-hidden="true"></i>
    </a>
@endcan
@can($deleteGate)
<button type="button" class="btn btn-xs btn-danger" title="{{ trans('global.delete') }}" onclick="confirmDelete({{ $row->id }})">
    <i class="fa fa-trash"></i>
</button>

<form id="delete-form-{{ $row->id }}" action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
    @csrf
    @method('DELETE')

</form>

@endcan


