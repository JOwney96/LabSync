<?php

use App\Models\Equipment;
use App\Models\CheckoutRequest;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    public bool $showModal = false;
    public array $selectedIds = [];
    public Collection|array $items = [];

    // Form Fields
    public string $startDate = '';
    public string $endDate = '';
    public string $purpose = '';

    /**
     * Set default dates when the modal opens
     */
    #[On('open-bulk-checkout-modal')]
    public function loadSelectedEquipment(array $selectedIds): void
    {
        $this->selectedIds = $selectedIds;
        $this->items = Equipment::whereIn('id', $selectedIds)->get();


        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addDays(7)->format('Y-m-d');
        $this->purpose = '';

        $this->showModal = true;
    }

    public function processBulkCheckout(): void
    {

        if (empty($this->purpose)) {
            $this->addError('purpose', 'Please provide a reason for this request.');
            return;
        }

        foreach ($this->selectedIds as $id) {
            CheckoutRequest::create([
                'user_id' => auth()->id(),
                'equipment_id' => $id,
                'status' => 'pending',
                'request_date' => now(),
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'purpose' => $this->purpose,
            ]);

            Equipment::find($id)->update(['status' => 'in_use']);
        }

        session()->flash('message', 'Bulk checkout request submitted successfully!');
        $this->showModal = false;
        $this->dispatch('bulk-checkout-complete');
    }
}; ?>

<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-transition x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500/75 transition-opacity" wire:click="$set('showModal', false)"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Confirm Bulk Request</h3>
                        <p class="text-sm text-slate-500 mb-6">Review the items and provide checkout details.</p>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Start Date</label>
                                <input type="date" wire:model="startDate"
                                       class="w-full rounded-md border-slate-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Return Date</label>
                                <input type="date" wire:model="endDate"
                                       class="w-full rounded-md border-slate-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Reason for Requesting</label>
                            <textarea wire:model="purpose" rows="3" placeholder="e.g., Lab work for CS Senior Capstone..."
                                      class="w-full rounded-md border-slate-300 text-sm focus:ring-primary-500 focus:border-primary-500 @error('purpose') border-red-500 @enderror"></textarea>
                            @error('purpose') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1 text-slate-500">Selected Items ({{ count($items) }})</label>
                        <ul class="divide-y divide-slate-100 border rounded-md max-h-40 overflow-y-auto bg-slate-50">
                            @foreach($items as $item)
                                <li class="p-3 flex justify-between items-center text-slate-800">
                                    <span class="font-medium text-sm">{{ $item->name }}</span>
                                    <span class="text-xs font-mono text-slate-400">{{ $item->tag_id }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="processBulkCheckout" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-sm font-bold text-white hover:bg-green-700 focus:outline-none sm:w-auto transition-colors">
                            Confirm Request
                        </button>

                        <button wire:click="$set('showModal', false)" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:w-auto transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
