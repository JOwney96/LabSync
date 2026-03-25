<?php

use App\Models\CheckoutRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component {

    // We use Computed properties to grab the student's specific stats efficiently
    #[Computed]
    public function activeItemsCount()
    {
        return CheckoutRequest::where('user_id', auth()->id())
            ->where('status', 'active')
            ->count();
    }

    #[Computed]
    public function pendingRequestsCount()
    {
        return CheckoutRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function overdueItemsCount()
    {
        return CheckoutRequest::where('user_id', auth()->id())
            ->where('status', 'overdue')
            ->count();
    }
};
?>

<div class="min-h-screen bg-surface-50 flex font-sans text-slate-800" x-data="{ sidebarOpen: false }">

    <aside class="w-64 bg-surface-900 text-slate-300 flex-col hidden md:flex transition-all duration-300">
        <div class="h-16 flex items-center px-6 border-b border-slate-700">
            <span class="text-xl font-bold text-white tracking-wide">Lab<span class="text-accent">Sync</span></span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('student.dashboard') }}"
               class="block px-4 py-2 bg-primary-900 text-white rounded-md font-medium" wire:navigate>Lab Equipment</a>
            <a href="{{route('student.requests')}}"
               class="block px-4 py-2 hover:bg-slate-800 hover:text-white rounded-md transition-colors"
               wire:navigate>My Requests & History</a>
            <a href="#" class="block px-4 py-2 hover:bg-slate-800 hover:text-white rounded-md transition-colors">Lab
                Guidelines</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header
            class="h-16 bg-surface-100 border-b border-surface-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-slate-800">Equipment Portal</h1>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                        class="flex items-center space-x-4 focus:outline-none">
                    <span class="text-sm font-medium text-slate-500">{{ auth()->user()->name ?? 'Student' }}</span>
                    <div
                        class="h-8 w-8 bg-accent-light text-accent-hover rounded-full flex items-center justify-center font-bold shadow-sm hover:bg-accent-light/80 transition-colors">
                        {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                </button>

                <div x-show="open" x-transition.opacity style="display: none;"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-surface-200 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-surface-50">Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-status-error hover:bg-surface-50">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-surface-100 p-6 rounded-lg border border-surface-200 shadow-soft">
                    <h3 class="text-sm font-medium text-slate-500 mb-1">Currently Borrowed</h3>
                    <div class="flex items-end justify-between">
                        <span class="text-3xl font-bold text-primary-600">{{ $this->activeItemsCount }}</span>
                        <a href="#" class="text-sm font-medium text-primary-500 hover:text-primary-700">View items
                            &rarr;</a>
                    </div>
                </div>

                <div class="bg-surface-100 p-6 rounded-lg border border-surface-200 shadow-soft">
                    <h3 class="text-sm font-medium text-slate-500 mb-1">Pending Approvals</h3>
                    <div class="flex items-end justify-between">
                        <span class="text-3xl font-bold text-slate-800">{{ $this->pendingRequestsCount }}</span>
                        @if($this->pendingRequestsCount > 0)
                            <span
                                class="text-sm font-medium text-status-warning bg-status-warning/10 px-2 py-1 rounded-full">Awaiting Admin</span>
                        @endif
                    </div>
                </div>

                <div
                    class="bg-surface-100 p-6 rounded-lg border {{ $this->overdueItemsCount > 0 ? 'border-status-error bg-status-error/5' : 'border-surface-200' }} shadow-soft">
                    <h3 class="text-sm font-medium text-slate-500 mb-1">Overdue Items</h3>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-3xl font-bold {{ $this->overdueItemsCount > 0 ? 'text-status-error' : 'text-status-success' }}">{{ $this->overdueItemsCount }}</span>
                        @if($this->overdueItemsCount > 0)
                            <span
                                class="text-sm font-medium text-status-error bg-status-error/10 px-2 py-1 rounded-full animate-pulse">Return immediately</span>
                        @else
                            <span class="text-sm font-medium text-status-success">All good!</span>
                        @endif
                    </div>
                </div>
            </div>

            <section>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">Lab Equipment Directory</h2>
                    <p class="text-sm text-slate-500">Search and request equipment for your experiments.</p>
                </div>

                <livewire:equipment-table/>
            </section>

        </div>
    </main>

    <livewire:checkout-modal/>

</div>
