<?php

use App\Models\CheckoutRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Controls the tab state
    public string $activeTab = 'active';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset pagination when switching tabs
    }

    public function cancelRequest($id)
    {
        // Ensure the student actually owns this request before touching it
        $request = CheckoutRequest::where('user_id', auth()->id())->findOrFail($id);

        if ($request->status === 'pending') {
            // Because we use SoftDeletes on the model, this safely hides it
            // from the UI while preserving the database history.
            $request->delete();

            session()->flash('message', 'Checkout request cancelled.');
        }
    }

    // Fetch the data based on the active tab
    #[Computed]
    public function requests()
    {
        $query = CheckoutRequest::with('equipment') // Eager load the equipment data
        ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($this->activeTab === 'active') {
            // Things the student currently has, or is waiting for
            $query->whereIn('status', ['pending', 'approved', 'active', 'overdue']);
        } else {
            // Things that are finished
            $query->whereIn('status', ['returned', 'denied']);
        }

        return $query->paginate(10);
    }
}

?>

<div class="min-h-screen bg-surface-50 flex font-sans text-slate-800">

    <x-student-aside/>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-surface-100 border-b border-surface-200 flex items-center px-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-slate-800">My Requests & History</h1>
        </header>

        <div class="flex-1 overflow-y-auto p-8 max-w-5xl mx-auto w-full">

            @if (session()->has('message'))
                <div wire:transition
                     class="mb-6 bg-status-success/10 border-l-4 border-status-success p-4 text-sm text-status-success font-medium rounded-r-md">
                    {{ session('message') }}
                </div>
            @endif

            <div class="border-b border-surface-200 mb-6 flex space-x-8">
                <button
                    wire:click="setTab('active')"
                    class="py-4 text-sm font-medium border-b-2 transition-colors relative {{ $activeTab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-surface-200' }}"
                >
                    Active & Pending
                    <span wire:loading wire:target="setTab('active')" class="absolute -right-4 top-4 flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                </button>

                <button
                    wire:click="setTab('history')"
                    class="py-4 text-sm font-medium border-b-2 transition-colors relative {{ $activeTab === 'history' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-surface-200' }}"
                >
                    Past History
                    <span wire:loading wire:target="setTab('history')" class="absolute -right-4 top-4 flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                </button>
            </div>

            <div class="space-y-4 relative">

                <div wire:loading.delay
                     class="absolute inset-0 bg-surface-50/50 backdrop-blur-sm z-10 rounded-lg"></div>

                @forelse($this->requests as $request)
                    <div wire:key="request-{{ $request->id }}"
                         class="bg-surface-100 border border-surface-200 rounded-lg p-6 shadow-soft flex flex-col md:flex-row md:items-center justify-between gap-4">

                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-slate-800">{{ $request->equipment->name }}</h3>
                                <span
                                    class="text-xs font-mono text-slate-500 bg-surface-200 px-2 py-1 rounded">ID: {{ $request->equipment->tag_id }}</span>
                            </div>

                            <div class="text-sm text-slate-600 flex items-center space-x-4">
                                <span><strong class="font-medium">From:</strong> {{ $request->start_date->format('M d, Y') }}</span>
                                <span><strong class="font-medium">To:</strong> {{ $request->end_date->format('M d, Y') }}</span>
                                <span class="text-slate-400">({{ $request->duration_days }} days)</span>
                            </div>

                            @if($request->status === 'denied' && $request->admin_notes)
                                <div
                                    class="mt-3 text-sm text-status-error bg-status-error/5 p-3 rounded border border-status-error/20">
                                    <strong class="font-medium">Admin Note:</strong> {{ $request->admin_notes }}
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col items-end space-y-3">

                            @if($request->status === 'pending')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-status-warning/10 text-status-warning border border-status-warning/20">Pending Approval</span>
                            @elseif($request->status === 'approved')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-700 border border-primary-200">Ready for Pickup</span>
                            @elseif($request->status === 'active')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-status-success/10 text-status-success border border-status-success/20">Currently Borrowed</span>
                            @elseif($request->status === 'overdue')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-status-error text-white animate-pulse">Overdue</span>
                            @elseif($request->status === 'returned')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-600 border border-slate-200">Returned</span>
                            @elseif($request->status === 'denied')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-status-error/10 text-status-error border border-status-error/20">Request Denied</span>
                            @endif

                            @if($request->status === 'pending')
                                <button
                                    wire:click="cancelRequest({{ $request->id }})"
                                    wire:confirm="Are you sure you want to cancel this request?"
                                    class="text-sm font-medium text-slate-500 hover:text-status-error underline decoration-slate-300 hover:decoration-status-error transition-colors data-[loading]:opacity-50"
                                >
                                    Cancel Request
                                </button>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="bg-surface-100 border border-surface-200 rounded-lg p-12 text-center shadow-soft">
                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-slate-900">No requests found</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $activeTab === 'active' ? "You don't have any active or pending equipment checkouts." : "You don't have any past checkout history." }}
                        </p>
                        @if($activeTab === 'active')
                            <div class="mt-6">
                                <a href="{{ route('student.dashboard') }}" wire:navigate
                                   class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                                    Browse Equipment Directory
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse

            </div>

            <div class="mt-6">
                {{ $this->requests->links() }}
            </div>

        </div>
    </main>
</div>
