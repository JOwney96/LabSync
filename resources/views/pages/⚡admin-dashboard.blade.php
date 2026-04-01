<?php

use App\Models\AdminRoutesEnum;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

new class extends Component {
    //
    public int $pendingRequestsCount = 0;
    public array $pendingRequests = [];
    public string $searchQuery = '';

    public function logout(): void
    {
        auth()->logout();
        session()->flush();

        $this->redirect('/');
    }
};
?>

<div class="min-h-screen bg-surface-50 flex font-sans text-slate-800" x-data="{ sidebarOpen: false }">

    <x-admin-aside :route="AdminRoutesEnum::DASHBOARD"/>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header
            class="h-16 bg-surface-100 border-b border-surface-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-2xl font-semibold text-slate-800">Admin Overview</h1>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                        class="flex items-center space-x-4 focus:outline-none">
                    <span class="text-sm font-medium text-slate-500">{{ auth()->user()->name ?? 'Admin User' }}</span>
                    <div
                        class="h-8 w-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold shadow-sm hover:bg-primary-200 transition-colors">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </button>

                <div x-show="open" x-transition.opacity style="display: none;"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-surface-200 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-surface-50">Settings</a>
                    <button x-on:click="$wire.logout()"
                            class="block w-full text-left px-4 py-2 text-sm text-status-error hover:bg-surface-50">Sign
                        out
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-surface-100 p-6 rounded-lg border border-surface-200 shadow-soft relative overflow-hidden">
                    <div wire:loading wire:target="refreshStats"
                         class="absolute inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center z-10">
                        <span class="animate-pulse text-primary-500 font-medium">Updating...</span>
                    </div>

                    <h3 class="text-sm font-medium text-slate-500 mb-1">Pending Requests</h3>
                    <div class="flex items-end justify-between">
                        <span class="text-3xl font-bold text-slate-800">{{ $pendingRequestsCount }}</span>
                        <span
                            class="text-sm font-medium text-status-warning bg-status-warning/10 px-2 py-1 rounded-full">Action Needed</span>
                    </div>
                </div>
            </div>

            <section class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">Checkout Requests</h2>
                </div>

                <div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft overflow-hidden">
                    @forelse($pendingRequests as $request)
                        <div
                            class="p-4 border-b border-surface-200 flex items-center justify-between hover:bg-surface-50 transition-colors">
                            <div>
                                <p class="font-medium text-slate-800">{{ $request->equipment->name }} <span
                                        class="text-xs font-mono text-slate-500 ml-2">ID: {{ $request->equipment->tag_id }}</span>
                                </p>
                                <p class="text-sm text-slate-500">Requested by: {{ $request->user->name }} •
                                    For: {{ $request->duration_days }} Days</p>
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    wire:click="denyRequest({{ $request->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 text-sm font-medium text-status-error bg-status-error/10 hover:bg-status-error hover:text-white rounded transition-colors disabled:opacity-50">
                                    Deny
                                </button>
                                <button
                                    wire:click="approveRequest({{ $request->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 text-sm font-medium text-white bg-primary-500 hover:bg-primary-600 shadow-sm rounded transition-colors disabled:opacity-50">
                                    Approve
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            No pending checkout requests at this time.
                        </div>
                    @endforelse
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">Equipment Overview</h2>
                </div>

                <div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft overflow-hidden">
                    <div class="p-8 text-center text-slate-400 font-medium">
                        <livewire:equipment-table :is-admin="true"/>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>
