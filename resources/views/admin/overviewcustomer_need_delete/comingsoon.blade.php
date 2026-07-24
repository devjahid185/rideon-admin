@extends('layouts.admin')

@section('content')
<style>
    .coming-soon-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999; /* Ensure the overlay is on top of all content */
}

.coming-soon-overlay h1 {
    font-size: 3em;
    text-align: center;
}

    </style>
    
<div class="content">
    <div class="coming-soon-overlay">
        <h1>Coming Soon</h1>
    </div>
</div>
@endsection