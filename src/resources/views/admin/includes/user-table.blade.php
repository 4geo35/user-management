<x-tt::table>
    <x-slot name="head">
        <tr>
            <x-tt::table.heading sortable
                                 wire:click="changeSort('name')"
                                 :direction="$sortBy == 'name' ? $sortDirection : null">
                {{ __("Name") }}
            </x-tt::table.heading>
            <x-tt::table.heading sortable
                                 wire:click="changeSort('email')"
                                 :direction="$sortBy == 'email' ? $sortDirection : null">
                E-mail
            </x-tt::table.heading>
            <x-tt::table.heading class="text-left">{{ __("Roles") }}</x-tt::table.heading>
            <x-tt::table.heading>{{ __("Actions") }}</x-tt::table.heading>
        </tr>
    </x-slot>
    <x-slot name="body">
        @foreach($users as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>
                    <ul>
                        @foreach($item->roles as $role)
                            <li>{{ $role->title }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <div class="flex justify-center">
                        <button type="button" class="btn btn-dark px-btn-x-ico rounded-e-none"
                                @cannot("update", $item) disabled
                                @else wire:loading.attr="disabled"
                                @endcannot
                                wire:click="showEdit({{ $item->id }})">
                            <x-tt::ico.edit />
                        </button>
                        <button type="button" class="btn btn-danger px-btn-x-ico rounded-s-none"
                                @can("delete", $item) @if ($item->id === Auth::id()) disabled @else wire:loading.attr="disabled" @endif
                                @else disabled
                                @endcan
                                wire:click="showDelete({{ $item->id }})">
                            <x-tt::ico.trash />
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-slot>
    <x-slot name="caption">
        <div class="flex justify-between">
            <div>{{ __("Total") }}: {{ $users->total() }}</div>
            {{ $users->links("tt::pagination.live") }}
        </div>
    </x-slot>
</x-tt::table>
