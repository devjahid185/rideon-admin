@extends('layouts.installer')

@section('title', 'Import Software Database')

@section('steps')
    @include('installer.steps')
@endsection

@section('content')
    <div class="row justify-content-center">
    @if(session()->has('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{session('error')}}
                                </div>
                            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Import Software Database</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted font-13 text-center">
                        <strong>Database is connected <i class="fas fa-check text-success"></i></strong>. Proceed by clicking <strong>Import Database</strong>. This automated process will configure your database.
                    </p>
                    @if(session()->has('error'))
                        <div class="text-center mt-4 pt-3">
                            <button type="button" class="btn btn-danger" onclick="showLoader()">
                                <i class="fas fa-redo mr-2"></i> Force Import Database
                            </button>
                        </div>
                    @else
                        <div class="text-center mt-4 pt-3">
                         
                    <a href="{{ route('installer.database.migrate') }}" class="btn btn-info" onclick="showLoader()">    <i class="fas fa-database mr-2"></i> Import Database
</a>
                        </div>
                    @endif
                </div>
            </div>
    
    </div>
    <div id="loading">
        <div class="spinner spinner-border"></div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
        function showLoader() {
            document.getElementById('loading').style.display = 'block';
        }
    </script>
@endsection