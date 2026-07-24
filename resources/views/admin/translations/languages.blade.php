@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Available Languages</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Language</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($languages as $key => $language)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $language }}</td>
                        <td>
                            <a href="{{ route('admin.translations.edit', $language) }}" class="btn btn-primary">Edit Translations</a>
                            <!-- Additional actions (e.g., delete, review) can be added here -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
