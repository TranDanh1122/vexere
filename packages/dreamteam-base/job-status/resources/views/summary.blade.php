<style>
    .flex {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    .flex .count {
        font-weight: bold;
        color: #000;
        margin-left: 10px;
    }
    .flex p {
        margin-bottom: 0;
    }
</style>
<div class="row" id="status_queued_summary" data-confirm="{{ __('JobStatus::admin.confirm_delete') }}">
    <div class="col-lg-2 col-md-2 col-xs-3 col-sm-3">
        <div class="box flex">
            <p class="name">{{ __('JobStatus::progress.status_queued') }}</p>
            <p class="count" id="status_queued">{{ $countStatus['queued'] ?? '' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-xs-3 col-sm-3">
        <div class="box flex">
            <p class="name">{{ __('JobStatus::progress.status_executing') }}</p>
            <p class="count" id="status_executing">{{ $countStatus['executing'] ?? '' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-xs-3 col-sm-3">
        <div class="box flex">
            <p class="name">{{ __('JobStatus::progress.status_finished') }}</p>
            <p class="count">{{ $countStatus['finished'] ?? '' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-xs-3 col-sm-3">
        <div class="box flex">
            <p class="name">{{  __('JobStatus::progress.status_failed') }}</p>
            <p class="count">{{ $countStatus['failed'] ?? '' }}</p>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-xs-3 col-sm-3">
        <div class="box flex">
            <p class="name">{{ __('JobStatus::progress.status_retrying') }}</p>
            <p class="count">{{ $countStatus['retrying'] ?? '' }}</p>
        </div>
    </div>
</div>
