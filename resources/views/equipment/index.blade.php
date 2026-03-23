@extends('layouts.app')

@section('content')
    <h1>Equipment List</h1>
    <p class="muted">Click an equipment type to view individual items and check them out.</p>

    @foreach($items as $item)
        <div class="card">
            <h2><a href="/equipment/{{ $item->id }}">{{ $item->name }}</a></h2>
            <p><strong>Category:</strong> {{ $item->category }}</p>
            <p><strong>Total Quantity:</strong> {{ $item->total_quantity }}</p>
            <p><strong>Available Items:</strong> {{ $item->available_count }}</p>
        </div>
    @endforeach
@endsection
