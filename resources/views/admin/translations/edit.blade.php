@extends('layouts.admin')

@section('content')
<style>
        .dataTables_info{
            display: none;
        }
        .paging_simple_numbers{
            display: none;
        }
        .pagination.justify-content-end{
            float: right;
        }
        
.dataTables_length{
    display:none;
}
    </style>
<div class="content">
<div class="col-lg-12">
<form action="{{ route('admin.saveTemporaryTranslations', ['locale' => $locale]) }}" method="post">
            <div class="panel panel-default">
                <div class="panel-heading">
                   {{$locale}} Edit
                </div>
                <div class="panel-body">
                    <div class="table-responsive">

                       <table class=" table table-bordered table-striped table-hover datatable datatable-Booking randum">
                            <thead>
                                <tr>
                                 
                                    <th>Current Value</th>
                                    <th>Translated Value</th>
                                </tr>
                            </thead><tbody>
                                    @csrf
                                    @foreach($translations as $key => $translation) 
                                    <tr>
                                        <td width="30%">
                                        <label style='font-size:12px' for="{{ $key }}">{{ strtoupper(str_replace("_"," ",$key)); }}</label></td>  <td>
                                        <input type="text" style="width:100%" id="{{ $key }}" name="translations[{{ $key }}]" value="{{ $translation }}">
                                        </td> <tr>
                                    @endforeach
                                    <tr>   <td width="30%">   </td><td>
                                
                            <input type="hidden" name="currentPage" value="{{ $currentPage }}">
                            <button type="submit">Save Changes</button> 
    </td></tr>
    </tbody>
                 </table>




    <nav aria-label="...">
<ul class="pagination justify-content-end">
<li class="page-item disabled">@if($currentPage > 1)
<span class="page-link">
    <a href="{{ route('admin.translations.edit', ['locale' => $locale, 'page' => $currentPage - 1]) }}">Previous</a>
</span>
</li>@else
<li class="page-item disabled">
<span class="page-link">
    <a href="#">Previous</a>
</span>
</li>
@endif
@if($currentPage < $totalPages)
<li class="page-item">

    <a href="{{ route('admin.translations.edit', ['locale' => $locale, 'page' => $currentPage + 1]) }}">Next</a>

</li>@endif
</ul>
</nav>
</div>
</div>


</div>
</form>
</div>
</div>
@endsection