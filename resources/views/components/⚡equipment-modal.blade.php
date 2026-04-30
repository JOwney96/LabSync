<?php

use App\Models\Equipment;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $equipmentId = -1;

    // Form fields
    public string $name = '';
    public string $tag_id = '';
    public string $category = '';
    public string $status = 'available';
    public ?string $calibration_due = '';

    protected function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'tag_id'          => 'required|string|max:100|unique:equipment,tag_id,' . ($this->equipmentId !== -1 ? $this->equipmentId : 'NULL'),
            'category'        => 'required|string|max:100',
            'status'          => 'required|in:available,in_use,maintenance,retired',
            'calibration_due' => 'nullable|date',
        ];
    }

    #[On('open-equipment-modal')]
    public function openModal(int $equipmentId = -1): void
    {
        $this->resetErrorBag();
        $this->equipmentId = $equipmentId;

        if ($equipmentId !== -1) {
            $equipment = Equipment::find($equipmentId);

            if ($equipment === null) {
                return;
            }

            $this->name            = $equipment->name;
            $this->tag_id          = $equipment->tag_id;
            $this->category        = $equipment->category;
            $this->status          = $equipment->status;
            $this->calibration_due = $equipment->calibration_due?->format('Y-m-d');
        } else {
            $this->reset(['name', 'tag_id', 'category', 'status', 'calibration_due']);
            $this->status = 'available';
        }
    }

    public function save(): void
    {
        $this->validate();

        Equipment::updateOrCreate(
            ['id' => $this->equipmentId !== -1 ? $this->equipmentId : null],
            [
                'name'            => $this->name,
                'tag_id'          => $this->tag_id,
                'category'        => $this->category,
                'status'          => $this->status,
                'calibration_due' => $this->calibration_due ?: null,
            ]
        );

        session()->flash('message', $this->equipmentId !== -1 ? 'Equipment updated.' : 'Equipment added.');

        $this->dispatch('equipment-saved');
    }
};
?>

<div x-data="{ show: false }"
     x-on:open-equipment-modal.window="show = true"
     x-on:equipment-saved.window="show = false">

    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="show = false"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-slate-800">
                    {{ $equipmentId !== -1 ? 'Edit Equipment' : 'Add Equipment' }}
                </h2>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Equipment Name</label>
                    <input wire:model="name" type="text" placeholder="e.g. Oscilloscope Pro 3000"
                           class="w-full px-3 py-2 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('name') border-status-error @enderror">
                    @error('name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tag / Asset ID</label>
                    <input wire:model="tag_id" type="text" placeholder="e.g. LAB-0042"
                           class="w-full px-3 py-2 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('tag_id') border-status-error @enderror">
                    @error('tag_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <input wire:model="category" type="text" placeholder="e.g. Measurement, Safety..."
                           class="w-full px-3 py-2 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('category') border-status-error @enderror">
                    @error('category') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select wire:model="status"
                            class="w-full px-3 py-2 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Calibration Due Date</label>
                    <input wire:model="calibration_due" type="date"
                           class="w-full px-3 py-2 border border-surface-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('calibration_due') border-status-error @enderror">
                    @error('calibration_due') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-surface-100 hover:bg-surface-200 rounded-md transition-colors">
                    Cancel
                </button>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md shadow-sm transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">{{ $equipmentId !== -1 ? 'Save Changes' : 'Add Equipment' }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
