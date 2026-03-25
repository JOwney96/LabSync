<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
};
?>

<div class="min-h-screen bg-surface-50 dark:bg-blue-200  flex font-sans text-slate-800">
    <x-student-aside/>
    <div class="my-5 mx-auto sm:px-6 lg:px-8 space-y-6">
        <div
            class="p-4 sm:p-8 bg-primary-400 dark:bg-primary-900 shadow sm:rounded-lg border-2 border-solid border-black">
            <div class="max-w-xl">
                <x-update-profile-information-form/>
                {{--@include('profile.partials.update-profile-information-form')--}}
            </div>
        </div>

        <div
            class="p-4 sm:p-8 bg-primary-400  dark:bg-primary-900 shadow sm:rounded-lg border-2 border-solid border-black">
            <div class="max-w-xl">
                <x-update-password-form/>
                {{--@include('components.update-password-form')--}}
            </div>
        </div>

        <div
            class="p-4 sm:p-8 bg-primary-400  dark:bg-primary-900 shadow sm:rounded-lg border-2 border-solid border-black">
            <div class="max-w-xl">
                <x-delete-user-form/>
                {{--@include('components.delete-user-form')--}}
            </div>
        </div>
    </div>
</div>
