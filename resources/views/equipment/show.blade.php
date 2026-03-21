@extends('layouts.app')

@section('content')
    <h1>{{ $equipment->name }}</h1>
    <p><strong>Category:</strong> {{ $equipment->category }}</p>
    <p><a href="/equipment">← Back to Equipment List</a></p>

    <h2>Individual Items</h2>

    @foreach($items as $item)
        @php
            $statusClass = 'available';
            if ($item->status === 'Checked Out') {
                $statusClass = 'checkedout';
            } elseif ($item->status === 'Maintenance') {
                $statusClass = 'maintenance';
            }
        @endphp

        <div class="card">
            <p><strong>Serial Number:</strong> {{ $item->serial_number }}</p>
            <p><strong>Condition:</strong> {{ $item->condition_status }}</p>
            <p>
                <strong>Status:</strong>
                <span class="status {{ $statusClass }}">{{ $item->status }}</span>
            </p>
            <p><strong>Location:</strong> {{ $item->location }}</p>
            <p><strong>Notes:</strong> {{ $item->notes ?: 'None' }}</p>

            @if($item->status === 'Available')
                <form method="POST" action="/checkout-item/{{ $item->id }}">
                    @csrf
                    <input type="text" name="user_name" placeholder="Enter student name" required>
                    <button class="btn" type="submit">Checkout</button>
                </form>
            @elseif($item->status === 'Checked Out')
                @if(isset($item->checkout_user_name))
                    <p><strong>Checked out by:</strong> {{ $item->checkout_user_name }}</p>
                    <p><strong>Checkout date:</strong> {{ $item->checkout_date }}</p>
                @endif

                <form method="POST" action="/return-item/{{ $item->id }}">
                    @csrf
                    <button class="btn btn-return" type="submit">Return Item</button>
                </form>
            @endif
        </div>
    @endforeach
@endsection
