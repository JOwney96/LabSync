<?php

use App\Models\Equipment;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public bool $isAdmin = false;
    public string $search = '';
    public string $statusFilter = '';

    // Automatically called by Livewire when $search or $statusFilter changes
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleMaintenance($equipmentId)
    {
        if (!$this->isAdmin) return;

        $equipment = Equipment::findOrFail($equipmentId);
        $newStatus = $equipment->status === 'maintenance' ? 'available' : 'maintenance';
        $equipment->update(['status' => $newStatus]);

        session()->flash('message', "{$equipment->name} status updated.");
    }

    public function initiateCheckout($equipmentId)
    {
        $this->dispatch('open-checkout-modal', equipmentId: $equipmentId);
    }

    // The #[Computed] attribute elegantly handles passing data to the view
    #[Computed]
    public function equipments()
    {
        return Equipment::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('tag_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('name')
            ->paginate(10);
    }
};
?>

<div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft overflow-hidden">

    <div class="p-4 border-b border-surface-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:w-72">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search by name or ID..."
                class="w-full pl-9 pr-4 py-2 bg-surface-50 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
        </div>

        <div class="flex items-center space-x-3">
            <select wire:model.live="statusFilter"
                    class="bg-surface-50 border border-surface-200 text-slate-700 text-sm rounded-md focus:ring-primary-500 block py-2 px-auto">
                <option value="">All Statuses</option>
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="maintenance">Maintenance</option>
            </select>

            @if($isAdmin)
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md shadow-sm transition-colors">
                    + Add Equipment
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('message'))
        <div wire:transition
             class="bg-status-success/10 border-l-4 border-status-success p-4 text-sm text-status-success font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="relative overflow-x-auto">
        <div wire:loading.delay
             class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center">
            <span class="text-primary-600 font-medium animate-pulse">Loading...</span>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="bg-surface-50 border-b border-surface-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                <th class="p-4">Equipment & ID</th>
                <th class="p-4">Category</th>
                <th class="p-4">Status</th>
                @if($isAdmin)
                    <th class="p-4">Next Calibration</th>
                @endif
                <th class="p-4 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-surface-200">
            @forelse ($this->equipments as $item)
                <tr wire:key="equipment-{{ $item->id }}" class="hover:bg-surface-50 transition-colors">

                    <td class="p-4">
                        <div class="font-medium text-slate-800">{{ $item->name }}</div>
                        <div class="text-xs font-mono text-slate-500">{{ $item->tag_id }}</div>
                    </td>

                    <td class="p-4 text-sm text-slate-600">{{ $item->category }}</td>

                    <td class="p-4">
                        @if($item->status === 'available')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-success/10 text-status-success">Available</span>
                        @elseif($item->status === 'in_use')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700">In Use</span>
                        @elseif($item->status === 'maintenance')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-error/10 text-status-error">Maintenance</span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ ucfirst($item->status) }}</span>
                        @endif
                    </td>

                    @if($isAdmin)
                        <td class="p-4 text-sm text-slate-600">
                            {{ $item->calibration_due ? $item->calibration_due->format('M d, Y') : 'N/A' }}
                        </td>
                    @endif

                    <td class="p-4 text-right">
                        @if($isAdmin)
                            <div x-data="{ menuOpen: false }" class="relative inline-block text-left">
                                <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false"
                                        class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-surface-200 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                                <div x-show="menuOpen" x-transition x-cloak
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-surface-200 z-50 overflow-hidden">
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-surface-50">Edit
                                        Details</a>

                                    <button
                                        wire:click="toggleMaintenance({{ $item->id }})"
                                        @click="menuOpen = false"
                                        class="w-full text-left px-4 py-2 text-sm data-[loading]:opacity-50 {{ $item->status === 'maintenance' ? 'text-status-success' : 'text-status-warning' }} hover:bg-surface-50"
                                    >
                                        {{ $item->status === 'maintenance' ? 'Mark Available' : 'Send to Repair' }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <button
                                wire:click="initiateCheckout({{ $item->id }})"
                                @disabled($item->status !== 'available')
                                class="px-3 py-1.5 text-sm font-medium rounded transition-colors data-[loading]:opacity-50 disabled:opacity-50 disabled:cursor-not-allowed
                                    {{ $item->status === 'available' ? 'text-white bg-primary-500 hover:bg-primary-600 shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                                {{ $item->status === 'available' ? 'Request' : 'Unavailable' }}
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isAdmin ? '5' : '4' }}" class="p-8 text-center text-slate-500">
                        No equipment found matching your criteria.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-surface-200">
        {{ $this->equipments->links() }}
    </div>
</div>
