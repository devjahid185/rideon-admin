@extends('layouts.installer')

@section('steps')
    @include('installer.steps')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Server Requirements</h2>
            <p class="card-text">Please make sure your server meets the following requirements:</p>
            <ul class="list-group mb-4">
                @foreach ($requirements as $key => $passed)
                    <li class="list-group-item">
                        @switch($key)
                            @case('php')
                                PHP >= 8.1
                            @break

                            @case('bcmath')
                                BCMath PHP Extension
                            @break

                            @case('ctype')
                                Ctype PHP Extension
                            @break

                            @case('json')
                                JSON PHP Extension
                            @break

                            @case('mbstring')
                                Mbstring PHP Extension
                            @break

                            @case('openssl')
                                OpenSSL PHP Extension
                            @break

                            @case('pdo')
                                PDO PHP Extension
                            @break

                            @case('tokenizer')
                                Tokenizer PHP Extension
                            @break

                            @case('xml')
                                XML PHP Extension
                            @break


                            @case('symlink')
                                Symlink Support
                            @break

                            @case('fileinfo')
                                Fileinfo PHP Extension
                            @break

                            @default
                                {{ ucfirst($key) }} PHP Extension
                        @endswitch

                        @if ($passed)
                            <span class="text-success float-right"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span class="text-danger float-right"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="d-flex justify-content-between">
                <a href="{{ route('installer.index') }}" class="btn btn-secondary">Back</a>

                @php
                    $allRequirementsMet = !in_array(false, $requirements, true);
                @endphp

                <a href="{{ route('installer.permissions') }}"
                    class="btn btn-primary @if (!$allRequirementsMet) disabled @endif">Next</a>
            </div>
        </div>
    </div>
@endsection
