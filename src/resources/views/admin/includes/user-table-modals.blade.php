<x-tt::modal.confirm wire:model="displayDelete">
    <x-slot name="title">{{ __("Delete user") }}</x-slot>
    <x-slot name="text">{{ __("It will be impossible to restore the user!") }}</x-slot>
</x-tt::modal.confirm>

<x-tt::modal.aside wire:model="displayData">
    <x-slot name="title">{{ $userId ? __("Edit user") : __("Add user") }}</x-slot>
    <x-slot name="content">
        <form wire:submit.prevent="{{ $userId ? 'update' : 'store' }}" class="space-y-indent-half" id="dataForm">
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
                <label for="email" class="inline-block mb-2">
                    E-mail<span class="text-danger">*</span>
                </label>
                <input type="email" id="email"
                       class="form-control {{ $errors->has('email') ? 'border-danger': '' }}"
                       required
                       wire:loading.attr="disabled"
                       wire:model="email">
                <x-tt::form.error name="email" />
            </div>

            <div>
                <div class="inline-block mb-2">
                    {{ __("Roles") }}
                </div>
                @isset($rolesList)
                    <div class="space-y-indent-half">
                        @foreach($rolesList as $item)
                            <div class="form-check">
                                <input type="checkbox" wire:model="roles"
                                       class="form-check-input" id="role-{{ $item->id }}"
                                       value="{{ $item->id }}">
                                <label for="role-{{ $item->id }}" class="form-check-label">
                                    {{ $item->title }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endisset
            </div>

            <div class="flex items-center space-x-indent-half">
                <button type="button" class="btn btn-outline-dark" wire:click="closeData">
                    {{ __("Cancel") }}
                </button>
                <button type="submit" form="dataForm" class="btn btn-primary"
                        wire:loading.attr="disabled">
                    {{ $userId ? __("Update") : __("Add") }}
                </button>
            </div>
        </form>
    </x-slot>
</x-tt::modal.aside>
