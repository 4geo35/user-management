<div class="row">
    <div class="col w-full space-y-indent">
        <div class="card">
            <div class="card-body">
                @include("um::admin.includes.role-search")
                <x-tt::notifications.error />
                <x-tt::notifications.success />
            </div>
        </div>

        @include("um::admin.includes.role-list")
    </div>

    @include("um::admin.includes.role-modals")
</div>
