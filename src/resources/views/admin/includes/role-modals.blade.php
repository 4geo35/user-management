<x-tt::modal.confirm wire:model="displayDelete">
    <x-slot name="title">{{ __("Delete role") }}</x-slot>
    <x-slot name="text">{{ __("It will be impossible to restore the role!") }}</x-slot>
</x-tt::modal.confirm>

<x-tt::modal.aside wire:model="displayPermissions">
    <x-slot name="title">{{ $permissionTitle ?? __("Edit permissions") }}</x-slot>
    <x-slot name="content">
        <form wire:submit.prevent="updatePermissions" class="space-y-indent-half" id="permissionForm">
            <div class="space-y-indent-half">
                @foreach($permissionList as $key => $value)
                    <div class="form-check">
                        <input type="checkbox" wire:model="rolePermissions"
                               class="form-check-input" id="permission-{{ $key }}"
                               value="{{ $key }}">
                        <label for="permission-{{ $key }}" class="form-check-label">
                            {{ $value }}
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center space-x-indent-half">
                <button type="button" class="btn btn-outline-dark" wire:click="closePermissions">
                    {{ __("Cancel") }}
                </button>
                <button type="submit" form="permissionForm" class="btn btn-primary"
                        wire:loading.attr="disabled">
                    {{ __("Update") }}
                </button>
            </div>
        </form>
    </x-slot>
</x-tt::modal.aside>

<x-tt::modal.aside wire:model="displayData">
    <x-slot name="title">{{ $roleId ? __("Edit role") : __("Add role") }}</x-slot>
    <x-slot name="content">
        <form wire:submit.prevent="{{ $roleId ? 'update' : 'store' }}"
              class="space-y-indent-half" id="dataForm">
            <div>
                <label for="name" class="inline-block mb-2">
                    {{ __("Name") }}<span class="text-danger">*</span>
                </label>
                <input type="text" id="name"
                       class="form-control {{ $errors->has("name") ? "border-danger" : "" }}"
                       required
                       wire:loading.attr="disabled"
                       wire:model="name">
                <x-tt::form.error name="name" />
            </div>

            <div>
                <label for="title" class="inline-block mb-2">
                    {{ __("Title") }}<span class="text-danger">*</span>
                </label>
                <input type="text" id="title"
                       class="form-control {{ $errors->has("title") ? "border-danger" : "" }}"
                       required
                       wire:loading.attr="disabled"
                       wire:model="title">
                <x-tt::form.error name="title" />
            </div>

            <div class="form-check">
                <input type="checkbox" wire:model="management"
                       class="form-check-input" id="app-management">
                <label for="app-management" class="form-check-label">
                    {{ __("App management") }}
                </label>
            </div>

            <div class="flex items-center space-x-indent-half">
                <button type="button" class="btn btn-outline-dark" wire:click="closeData">
                    {{ __("Cancel") }}
                </button>
                <button type="submit" form="dataForm" class="btn btn-primary"
                        wire:loading.attr="disabled">
                    {{ $roleId ? __("Update") : __("Add") }}
                </button>
            </div>
        </form>
    </x-slot>
</x-tt::modal.aside>
