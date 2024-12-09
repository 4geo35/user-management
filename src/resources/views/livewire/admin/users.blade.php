<div class="row">
    <div class="col w-full">
        <div class="card">
            <div class="card-body">
                @include("um::admin.includes.user-search")
                <x-tt::notifications.error />
                <x-tt::notifications.success />
            </div>

            @include("um::admin.includes.user-table")
            @include("um::admin.includes.user-table-modals")
        </div>
    </div>
</div>
