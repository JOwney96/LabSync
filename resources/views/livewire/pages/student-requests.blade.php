<?php

use App\Models\CheckoutRequest;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Controls the tab state: 'active' or 'history'
    public string $activeTab = 'active';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function cancelRequest(int $id): void
    {
        // Ensure the student actually owns this request before touching it
        $request = CheckoutRequest::where('user_id', auth()->id())->findOrFail($id);

        if ($request->status === 'pending') {
            // This safely "hides" it if you have SoftDeletes enabled
            $request->delete();
            session()->flash('message', 'Checkout request cancelled.');
        }
    }

    /**
     * Fetch the data based on the active tab using Computed properties
     */
    #[Computed]
    public function requests()
    {
        $query = CheckoutRequest::with('equipment')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($this->activeTab === 'active') {
            // Logic for what "Active" means in LabSync
            $query->whereIn('status', ['pending', 'approved', 'active', 'overdue']);
        } else {
            // Show only finished business
            $query->whereIn('status', ['returned', 'denied']);
        }

        return $query->paginate(10);
    }
}; ?>

<div class="min-h-screen bg-surface-50 flex font-sans text-slate-800">

    {{-- Sidebar component --}}
    <x-student-aside/>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        {{-- Top Header --}}
        <header class="h-16 bg-surface-100 border-b border-surface-200 flex items-center px-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-slate-800">My Requests & History</h1>
        </header>

        <div class="flex-1 overflow-y-auto p-8 max-w-5xl mx-auto w-full">

            {{-- Flash Messages for Cancellations --}}
            @if (session()->has('message'))
                <div wire:transition
                     class="mb-6 bg-status-success/10 border-l-4 border-status-success p-4 text-sm text-status-success font-medium rounded-r-md">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="border-b border-surface-200 mb-6 flex space-x-8">
                <button wire:click="setTab('active')"
                        class="py-4 text-sm font-medium border-b-2 transition-all relative {{ $activeTab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-surface-200' }}">
                    Active & Pending
                </button>

                <button wire:click="setTab('history')"
                        class="py-4 text-sm font-medium border-b-2 transition-all relative {{ $activeTab === 'history' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-surface-200' }}">
                    Past History
                </button>
            </div>

            {{-- Requests List --}}
            <div class="space-y-4 relative">
                {{-- Loading Overlay --}}
                <div wire:loading.delay class="absolute inset-0 bg-surface-50/50 backdrop-blur-sm z-10 rounded-lg"></div>

                @forelse($this->requests as $request)
                    <div wire:key="request-{{ $request->id }}"
                         class="bg-white border border-surface-200 rounded-lg p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:shadow-md">

                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-slate-800">{{ $request->equipment->name }}</h3>
                                <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                    {{ $request->equipment->tag_id }}
                                </span>
                            </div>

                            <div class="text-sm text-slate-600 flex items-center space-x-4">
                                <span><strong class="font-medium">Requested:</strong> {{ $request->start_date?->format('M d, Y') ?? 'N/A' }}</span>
                                <span><strong class="font-medium">To:</strong> {{ $request->end_date?->format('M d, Y') ?? 'N/A' }}</span>
                                <span class="text-slate-400">({{ $request->duration_days }} days)</span>
                            </div>

                            {{-- Show Denial Reason if applicable --}}
                            @if($request->status === 'denied' && $request->admin_notes)
                                <div class="mt-3 text-sm text-status-error bg-status-error/5 p-3 rounded border border-status-error/20 italic">
                                    "{{ $request->admin_notes }}"
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col items-end space-y-3">
                            {{-- Status Badges --}}
                            @if($request->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-700 border border-amber-200">Pending Approval</span>
                            @elseif($request->status === 'approved')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-primary-100 text-primary-700 border border-primary-200">Ready for Pickup</span>
                            @elseif($request->status === 'active')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-100 text-green-700 border border-green-200">Borrowed</span>
                            @elseif($request->status === 'overdue')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-600 text-white animate-pulse">Overdue</span>
                            @elseif($request->status === 'returned')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-slate-100 text-slate-600">Returned</span>
                            @elseif($request->status === 'denied')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-100 text-red-700 border border-red-200">Denied</span>
                            @endif

                            {{-- Actions --}}
                            @if($request->status === 'pending')
                                <button wire:click="cancelRequest({{ $request->id }})"
                                        wire:confirm="Withdraw this equipment request?"
                                        class="text-sm font-medium text-slate-400 hover:text-red-600 transition-colors">
                                    Cancel Request
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-surface-200 rounded-lg p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-slate-900">No records found</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $activeTab === 'active' ? "You don't have any current requests." : "No past checkout history available." }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $this->requests->links() }}
            </div>

        </div>
    </main>
</div>
