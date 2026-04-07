<?php

use App\Models\AdminWhitelist;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Validate('required|email|max:255|unique:admin_whitelist,email')]
    public string $newEmail = '';

    #[Validate('nullable|string|max:500')]
    public string $newNotes = '';

    public function add()
    {
        $this->validate();

        AdminWhitelist::create([
            'email'    => strtolower(trim($this->newEmail)),
            'notes'    => $this->newNotes ?: null,
            'added_by' => auth()->id(),
        ]);

        $this->reset('newEmail', 'newNotes');
        $this->resetPage();

        session()->flash('message', "'{$this->newEmail}' added to the admin whitelist.");
    }

    public function remove($id)
    {
        AdminWhitelist::findOrFail($id)->delete();

        session()->flash('message', 'Entry removed from the admin whitelist.');
    }

    #[Computed]
    public function entries()
    {
        return AdminWhitelist::with('addedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
};
?>

<div class="flex bg-surface-50 min-h-screen">
    <x-admin-aside/>
    <div class="p-8 max-w-5xl mx-auto w-full">

        <div class="mb-8 border-b border-surface-200 pb-4">
            <h1 class="text-2xl font-bold text-slate-800">Admin Whitelist</h1>
            <p class="text-sm text-slate-500 mt-1">
                Emails on this list are automatically granted admin access when they register.
                The registration code remains as a fallback.
            </p>
        </div>

        @if (session()->has('message'))
            <div wire:transition
                 class="mb-6 bg-status-success/10 border-l-4 border-status-success p-4 text-sm text-status-success font-medium rounded-r-md">
                {{ session('message') }}
            </div>
        @endif

        {{-- Add Form --}}
        <div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft p-6 mb-8">
            <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4">Add Email to Whitelist</h2>
            <form wire:submit="add" class="flex flex-col sm:flex-row gap-3 items-start">
                <div class="flex-1">
                    <x-text-input
                        wire:model="newEmail"
                        type="email"
                        placeholder="faculty@tamut.edu"
                        class="w-full"
                    />
                    <x-input-error :messages="$errors->get('newEmail')" class="mt-1" />
                </div>
                <div class="flex-1">
                    <x-text-input
                        wire:model="newNotes"
                        type="text"
                        placeholder="Notes (optional)"
                        class="w-full"
                    />
                    <x-input-error :messages="$errors->get('newNotes')" class="mt-1" />
                </div>
                <x-primary-button type="submit">
                    Add
                </x-primary-button>
            </form>
        </div>

        {{-- Whitelist Table --}}
        <div class="bg-surface-100 rounded-lg border border-surface-200 shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-surface-50 border-b border-surface-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="p-4">Email</th>
                        <th class="p-4">Notes</th>
                        <th class="p-4">Added By</th>
                        <th class="p-4">Added On</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200">
                    @forelse ($this->entries as $entry)
                        <tr wire:key="wl-{{ $entry->id }}" class="hover:bg-surface-50 transition-colors">

                            <td class="p-4">
                                <div class="font-medium text-slate-800">{{ $entry->email }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-sm text-slate-500">{{ $entry->notes ?? '—' }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-sm text-slate-600">{{ $entry->addedBy?->name ?? 'System' }}</div>
                            </td>

                            <td class="p-4">
                                <div class="text-sm text-slate-500">{{ $entry->created_at->format('M d, Y') }}</div>
                            </td>

                            <td class="p-4 text-right">
                                <button
                                    wire:click="remove({{ $entry->id }})"
                                    wire:confirm="Remove '{{ $entry->email }}' from the whitelist?"
                                    class="px-3 py-1 text-sm font-medium text-status-error hover:bg-status-error/10 rounded transition-colors">
                                    Remove
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                No emails on the whitelist yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-surface-200">
                {{ $this->entries->links() }}
            </div>
        </div>

    </div>
</div>
