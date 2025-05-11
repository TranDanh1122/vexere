@extends('Core::layouts.app')
@section('title')
    {{ __('Core::admin.admin_system') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">Xin chào,</h5>
                                <p>{{ $currentUser->getName() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ RvMedia::url($currentUser->avatar ?? RvMedia::getDefaultImage()) }}"
                                    alt="" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $currentUser->getName() }}</h5>
                            <p class="text-muted mb-0 text-truncate">{{ $currentUser->position }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
