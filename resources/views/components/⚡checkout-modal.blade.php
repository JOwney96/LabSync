<?php

use App\Models\Equipment;
use App\Models\CheckoutRequest;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public bool $isOpen = false;
    public ?Equipment $equipment = null;

    #[Validate('required|date|after_or_equal:today')]
    public $start_date;

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date;

    #[Validate('required|string|min:10|max:500')]
    public $purpose;

    #[On('open-checkout-modal')]
    public function loadModal($equipmentId)
    {
        $this->equipment = Equipment::findOrFail($equipmentId);
        $this->reset(['start_date', 'end_date', 'purpose']);
        $this->isOpen = true;
    }

    public function submitRequest()
    {
        $this->validate();

        CheckoutRequest::create([
            'user_id' => auth()->id(),
            'equipment_id' => $this->equipment->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'purpose' => $this->purpose,
            'status' => 'pending',
        ]);

        $this->isOpen = false;
        session()->flash('message', "Checkout request for {$this->equipment->name} submitted successfully!");
        $this->dispatch('request-submitted');
    }
};
?>

<div
    dusk="checkout-modal"
    x-data="{ show: @entangle('isOpen') }"
    x-show="show"
    x-cloak
    class="relative z-50"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div wire:transition class="fixed inset-0 bg-surface-900/75 backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

            <div
                wire:transition
                @click.away="show = false"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg border border-surface-200"
            >
                @if($equipment)
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-semibold leading-6 text-slate-800" id="modal-title">
                                    Request Equipment
                                </h3>
                                <div class="mt-2 bg-surface-50 p-3 rounded-md border border-surface-200 mb-4">
                                    <p class="font-medium text-slate-800">{{ $equipment->name }}</p>
                                    <p class="text-sm font-mono text-slate-500">ID: {{ $equipment->tag_id }} |
                                        Category: {{ $equipment->category }}</p>
                                </div>

                                <form wire:submit="submitRequest" class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="start_date" class="block text-sm font-medium text-slate-700">Start
                                                Date</label>
                                            <input dusk="start-date" type="date" wire:model="start_date" id="start_date"
                                                   class="mt-1 block w-full rounded-md border-surface-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-surface-50 p-2 border">
                                            @error('start_date') <span
                                                class="text-xs text-status-error mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="end_date" class="block text-sm font-medium text-slate-700">End
                                                Date</label>
                                            <input dusk="end-date" type="date" wire:model="end_date" id="end_date"
                                                   class="mt-1 block w-full rounded-md border-surface-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-surface-50 p-2 border">
                                            @error('end_date') <span
                                                class="text-xs text-status-error mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="purpose" class="block text-sm font-medium text-slate-700">Purpose of
                                            Checkout</label>
                                        <textarea dusk="purpose" wire:model="purpose" id="purpose" rows="3"
                                                  class="mt-1 block w-full rounded-md border-surface-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-surface-50 p-2 border"
                                                  placeholder="Briefly describe what experiment or project this is for..."></textarea>
                                        @error('purpose') <span
                                            class="text-xs text-status-error mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-surface-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-surface-200">
                        <button
                            dusk="submit-request"
                            type="button"
                            wire:click="submitRequest"
                            class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto data-[loading]:opacity-50 transition-colors"
                        >
                            <span wire:loading.remove wire:target="submitRequest">Submit Request</span>
                            <span wire:loading wire:target="submitRequest">Processing...</span>
                        </button>
                        <button
                            type="button"
                            @click="show = false"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-medium text-slate-900 shadow-sm ring-1 ring-inset ring-surface-200 hover:bg-surface-50 sm:mt-0 sm:w-auto transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
