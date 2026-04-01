<?php

use App\Models\CheckoutRequest;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {

    #[Computed]
    public function borrowedItems()
    {
        return CheckoutRequest::with('equipment')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->orderBy('end_date', 'asc')
            ->get();
    }
};
?>

<div class="min-h-screen bg-surface-50 flex font-sans text-slate-800">

    <x-student-aside/>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-surface-100 border-b border-surface-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-slate-800">Currently Borrowed</h1>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                        class="flex items-center space-x-4 focus:outline-none">
                    <span class="text-sm font-medium text-slate-500">{{ auth()->user()->name ?? 'Student' }}</span>
                    <div class="h-8 w-8 bg-accent-light text-accent-hover rounded-full flex items-center justify-center font-bold shadow-sm hover:bg-accent-light/80 transition-colors">
                        {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                </button>

                <div x-show="open" x-transition.opacity style="display: none;"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-surface-200 z-50">
                    <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-surface-50">Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-status-error hover:bg-surface-50">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">

            <div class="mb-6">
                <a href="{{ route('student.dashboard') }}" wire:navigate
                   class="inline-flex items-center text-sm text-slate-500 hover:text-primary-600 transition-colors">
                    &larr; Back to Dashboard
                </a>
            </div>

            @if($this->borrowedItems->isEmpty())
            <div class="bg-surface-100 border border-surface-200 rounded-lg p-16 text-center shadow-soft">
                <div class="text-5xl mb-4">📦</div>
                <h3 class="text-lg font-semibold text-slate-800 mb-1">No items borrowed</h3>
                <p class="text-sm text-slate-500 mb-6">You don't have any equipment checked out right now.</p>
                <a href="{{ route('student.dashboard') }}" wire:navigate
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 transition-colors">
                    Browse Equipment
                </a>
            </div>
            @else
            <div class="mb-6">
                <p class="text-sm text-slate-500">
                    You have <span class="font-semibold text-slate-800">{{ $this->borrowedItems->count() }}</span> item(s) checked out.
                </p>
            </div>

            <div class="bg-surface-100 border border-surface-200 rounded-lg shadow-soft overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-surface-200 bg-surface-50">
                        <th class="text-left px-6 py-3 font-medium text-slate-500">Equipment</th>
                        <th class="text-left px-6 py-3 font-medium text-slate-500">Checked Out</th>
                        <th class="text-left px-6 py-3 font-medium text-slate-500">Due Date</th>
                        <th class="text-left px-6 py-3 font-medium text-slate-500">Status</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200">
                    @foreach($this->borrowedItems as $item)
                    @php
                    $isOverdue = $item->end_date && $item->end_date->isPast();
                    $isDueSoon = !$isOverdue && $item->end_date && $item->end_date->diffInDays(now()) <= 2;
                    @endphp
                    <tr class="hover:bg-surface-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">{{ $item->equipment->name ?? 'Unknown Equipment' }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">Tag: {{ $item->equipment->tag_id ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $item->start_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->end_date)
                            <span class="{{ $isOverdue ? 'text-status-error font-semibold' : ($isDueSoon ? 'text-status-warning font-medium' : 'text-slate-500') }}">
                    {{ $item->end_date->format('M d, Y') }}
                </span>
                            @if($isOverdue)
                            <div class="text-xs text-status-error mt-0.5">{{ $item->end_date->diffForHumans() }}</div>
                            @elseif($isDueSoon)
                            <div class="text-xs text-status-warning mt-0.5">Due {{ $item->end_date->diffForHumans() }}</div>
                            @endif
                            @else
                            <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($isOverdue)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-error/10 text-status-error animate-pulse">
                    Overdue
                </span>
                            @elseif($isDueSoon)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-warning/10 text-status-warning">
                    Due Soon
                </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-status-success/10 text-status-success">
                    Active
                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </main>
</div>
