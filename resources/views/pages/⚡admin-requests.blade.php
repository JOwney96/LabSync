<?php

use App\Models\CheckoutRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AdminRoutesEnum;

new class extends Component {
    use WithPagination;

    public string $filter = 'pending'; // 'pending', 'active', 'history'

    public function setFilter($newFilter)
    {
        $this->filter = $newFilter;
        $this->resetPage();
    }

    // --- Admin Actions ---

    public function approve($id)
    {
        $request = CheckoutRequest::findOrFail($id);
        $request->update(['status' => 'approved']);

        session()->flash('message', "Request for {$request->user->name} approved.");
    }

    public function deny($id)
    {
        $request = CheckoutRequest::findOrFail($id);
        $request->update([
            'status' => 'denied',
            'admin_notes' => 'Denied by administrator.' // You could tie this to a modal for custom notes later!
        ]);

        session()->flash('message', "Request denied.");
    }

    public function markActive($id)
    {
        // Used when the student physically picks up the approved item
        $request = CheckoutRequest::findOrFail($id);
        $request->update(['status' => 'active']);

        session()->flash('message', "Item marked as picked up.");
    }

    public function markReturned($id)
    {
        $request = CheckoutRequest::findOrFail($id);
        $request->update(['status' => 'returned']);

        session()->flash('message', "Item logged as returned.");
    }

    // --- Data Fetching ---

    #[Computed]
    public function requests()
    {
        $query = CheckoutRequest::with(['user', 'equipment'])->orderBy('created_at', 'desc');

        match ($this->filter) {
            'pending' => $query->where('status', 'pending'),
            'active' => $query->whereIn('status', ['approved', 'active', 'overdue']),
            'history' => $query->whereIn('status', ['returned', 'denied']),
        };

        return $query->paginate(15);
    }
};
?>

<div class="flex bg-surface-50 min-h-screen">
    <x-admin-aside :route="AdminRoutesEnum::REQUESTS"/>
    <div class="p-8 max-w-7xl mx-auto w-full">

        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-surface-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Checkout Requests</h1>
                <p class="text-sm text-slate-500 mt-1">Review pending checkouts and track active equipment.</p>
            </div>

            <div class="flex space-x-2 bg-surface-100 p-1 rounded-lg border border-surface-200">
                <button wire:click="setFilter('pending')"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filter === 'pending' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                    Needs Action
                </button>
                <button wire:click="setFilter('active')"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filter === 'active' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                    Active
                </button>
                <button wire:click="setFilter('history')"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ $filter === 'history' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
                    History
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div wire:transition
                 class="mb-6 bg-status-success/10 border-l-4 border-status-success p-4 text-sm text-status-success font-medium rounded-r-md">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft overflow-hidden relative">

            <div wire:loading.delay wire:target="setFilter"
                 class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center">
                <span class="text-primary-600 font-medium animate-pulse">Loading...</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-surface-50 border-b border-surface-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4">Student</th>
                        <th class="p-4">Equipment</th>
                        <th class="p-4">Duration</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200">
                    @forelse ($this->requests as $req)
                        <tr wire:key="req-{{ $req->id }}" class="hover:bg-surface-50 transition-colors">

                            <td class="p-4">
                                <div class="font-medium text-slate-800">{{ $req->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $req->user->email }}</div>
                            </td>

                            <td class="p-4">
                                <div class="font-medium text-slate-800">{{ $req->equipment->name }}</div>
                                <div class="text-xs font-mono text-slate-500">{{ $req->equipment->tag_id }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-sm text-slate-800">{{ $req->start_date->format('M d') }}
                                    - {{ $req->end_date->format('M d') }}</div>
                                <div class="text-xs text-slate-500">{{ $req->duration_days }} days</div>
                            </td>

                            <td class="p-4">
                                @if($req->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-warning/10 text-status-warning border border-status-warning/20">Pending</span>
                                @elseif($req->status === 'approved')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700 border border-primary-200">Awaiting Pickup</span>
                                @elseif($req->status === 'active')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-success/10 text-status-success border border-status-success/20">Checked Out</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">{{ ucfirst($req->status) }}</span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                @if($req->status === 'pending')
                                    <div class="flex justify-end space-x-2">
                                        <button wire:click="deny({{ $req->id }})" wire:confirm="Deny this request?"
                                                class="px-3 py-1 text-sm font-medium text-status-error hover:bg-status-error/10 rounded transition-colors data-[loading]:opacity-50">
                                            Deny
                                        </button>
                                        <button wire:click="approve({{ $req->id }})"
                                                class="px-3 py-1 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded shadow-sm transition-colors data-[loading]:opacity-50">
                                            Approve
                                        </button>
                                    </div>
                                @elseif($req->status === 'approved')
                                    <button wire:click="markActive({{ $req->id }})"
                                            class="px-3 py-1 text-sm font-medium text-primary-700 bg-primary-100 hover:bg-primary-200 rounded transition-colors data-[loading]:opacity-50">
                                        Log Pickup
                                    </button>
                                @elseif($req->status === 'active' || $req->status === 'overdue')
                                    <button wire:click="markReturned({{ $req->id }})"
                                            class="px-3 py-1 text-sm font-medium text-slate-700 bg-surface-200 hover:bg-surface-300 rounded transition-colors data-[loading]:opacity-50">
                                        Log Return
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                No requests found for this filter.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-surface-200">
                {{ $this->requests->links() }}
            </div>
        </div>
    </div>
</div>
