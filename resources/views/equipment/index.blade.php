<x-app-layout>
    <h1>Equipment List</h1>
    <p class="muted">Click an equipment type to view individual items and check them out.</p>

    @foreach($items as $item)
        <div class="card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
            <h2><a href="/equipment/{{ $item->id }}" style="color: blue; text-decoration: underline;">{{ $item->name }}</a></h2>
            <p><strong>Category:</strong> {{ $item->category }}</p>
            <p><strong>Available Items:</strong> {{ $item->available_count }}</p>
        </div>
    @endforeach
</x-app-layout>
