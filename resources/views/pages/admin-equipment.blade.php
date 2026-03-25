<div class="flex min-h-screen mx-auto bg-surface-50">
    <x-admin-aside/>

    <div class="p-8 max-w-7xl mx-auto w-full">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Equipment Inventory</h1>
            <p class="text-sm text-slate-500 mt-1">Manage all lab assets, track calibration statuses, and update
                availability.</p>
        </div>

        <livewire:equipment-table :is-admin="true"/>
    </div>
</div>
