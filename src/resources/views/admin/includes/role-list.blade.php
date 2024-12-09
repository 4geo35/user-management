@foreach($roles as $item)
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h5 class="card-title mb-0">
                {{ $item->title }} <small class="text-secondary">({{ $item->name }})</small>
            </h5>
            <div class="flex justify-center">
                <button type="button" class="btn btn-sm btn-dark px-btn-x-ico rounded-e-none"
                        @cannot("update", $item) disabled @endcannot
                        wire:loading.attr="disabled"
                        wire:click="showEdit({{ $item->id }})">
                    <x-tt::ico.edit />
                </button>
                <button type="button" class="btn btn-sm btn-danger px-btn-x-ico rounded-s-none"
                        @can("delete", $item) @if ($item->id === Auth::id()) disabled @endif
                        @else disabled
                        @endcan
                        wire:loading.attr="disabled"
                        wire:click="showDelete({{ $item->id }})">
                    <x-tt::ico.trash />
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="flex items-center justify-start flex-wrap">
                @foreach($permissions as $permission)
                    <button type="button" wire:click="showPermissions({{ $permission->id }}, {{ $item->id }})"
                            @cannot("update", $item) disabled @endcan
                            class="btn btn-outline-dark mr-indent-half my-indent-half">
                        {{ $permission->title }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

<div class="card">
    <div class="card-body">
        <div class="flex justify-between">
            <div>{{ __("Total") }}: {{ $roles->total() }}</div>
            {{ $roles->links("tt::pagination.live") }}
        </div>
    </div>
</div>
