<?php

namespace DreamTeam\JobStatus\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController as BaseController;
use  DreamTeam\JobStatus\Models\JobStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;
use DreamTeam\JobStatus\Services\Interfaces\JobStatusServiceInterface;
use Illuminate\Support\Facades\Artisan;

class ProgressController extends BaseController
{
    protected JobStatusServiceInterface $jobStatusService;
    protected array $listStatus;
    protected array $allTypeJobs;

    function __construct(
        JobStatusServiceInterface $jobStatusService
    )
    {
        $this->table_name = (new JobStatus)->getTable();
        $this->module_name = 'JobStatus::admin.progress';
        $this->has_seo = false;
        $this->has_locale = false;
        $this->jobStatusService = $jobStatusService;
        parent::__construct();
        $this->listStatus = JobStatus::getListStatus();
        $this->allTypeJobs = JobStatus::allTypeJob();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        \Asset::addDirectly([asset('vendor/core/core/base/js/auto-load-job-status.js')], 'scripts', 'bottom');
        $listdata = new ListData(
                $requests,
                $this->jobStatusService,
                $this->table_name,
                'JobStatus::table',
                [],
                false,
                $this->has_locale,
                30,
                [ $this->table_name.'.id' => 'desc' ]
            );

        // Build Form tìm kiếm
        $listdata->search('type', __('JobStatus::admin.type'), 'array', $this->allTypeJobs);
        $listdata->search('status', __('JobStatus::admin.status'), 'array', $this->listStatus);
        if (checkRole($this->table_name.'_delete')) {
            $listdata->searchBtn( __('Core::admin.general.delete'), route('admin.job_statuses.deleteWithRequest'), 'danger delete-job-status', 'fas fa-trash');
        }
        // Build các hành động
        $listdata->no_add();
        $listdata->no_trash();

        // Build bảng
        $listdata->add('type', __('JobStatus::admin.type'), 0);
        $listdata->add('status', __('JobStatus::admin.status'), 0);
        $listdata->add('progress', __('JobStatus::admin.progress'), 0);
        $listdata->add('created_at', __('JobStatus::admin.created_at'), 0);
        $listdata->add('started_at', __('JobStatus::admin.started_at'), 0);
        $listdata->add('finished_at', __('JobStatus::admin.finished_at'), 0);
        $listdata->add('', __('JobStatus::admin.info'), 0);
        $listdata->add('', 'Language', 0, 'lang');
        $listdata->add('', __('Core::admin.general.delete'), 0, 'delete_custom');
        $conditions = [];
        extract($requests->all(), EXTR_OVERWRITE);
        if(isset($type) && !empty($type)) {
            $conditions['type'] = $type;
        }
        if(isset($status) && !empty($status)) {
            $conditions['status'] = $status;
        }
        $showDatas = $this->jobStatusService->search($conditions, false, 'id, job_id, status');
        $queued = $showDatas->where('status', JobStatus::STATUS_QUEUED)->count();
        $executing = $showDatas->where('status', JobStatus::STATUS_EXECUTING)->count();
        $finished = $showDatas->where('status', JobStatus::STATUS_FINISHED)->count();
        $failed = $showDatas->where('status', JobStatus::STATUS_FAILED)->count();
        $retrying = $showDatas->where('status', JobStatus::STATUS_RETRYING)->count();
        $countStatus = compact('queued', 'executing', 'finished', 'failed', 'retrying');
        $include_view_top = ['JobStatus::summary' => compact('countStatus')];
        return $listdata->render(compact('include_view_top'));
    }

    public function destroy($id) {
        $status = $this->jobStatusService->read($id);
        \DB::table('jobs')->where('id', $status->job_id)->delete();
        $this->jobStatusService->deleteFromWhereCondition(compact('id'));
        return [
            'status' => 1,
            'message' => __('Translate::admin.delete_success')
        ];
    }

    public function deleteWithRequest(Request $requests) {
        if (!checkRole($this->table_name.'_delete')) {
            return redirect()->back()->withErrors(__('Core::admin.no_permission'));
        }
        extract($requests->all(), EXTR_OVERWRITE);
        $conditions = [];
        if(isset($type) && !empty($type)) {
            $conditions['type'] = $type;
        }
        if(isset($status) && !empty($status)) {
            $conditions['status'] = $status;
        }
        $listJobID = $this->jobStatusService->search($conditions, false, 'id, job_id, status')
            ->pluck('job_id')->toArray();
        \DB::table('jobs')->whereIn('id', $listJobID)->delete();
        $this->jobStatusService->deleteFromWhereCondition($conditions);
        // Trả về
        return [
            'status' => 1,
            'message' => __('Translate::admin.delete_success')
        ];
    }

    public function reRunjob(Request $request , $jobUuid) {
        if($jobUuid == 0) {
            return [ 
                    'type' => 'error',
                    'message' => trans('JobStatus::admin.re_run_not_support') 
                ];
        }
        try {
            Artisan::call('queue:retry ' . $jobUuid);
            return [ 
                'type' => 'success',
                'message' => trans('JobStatus::admin.re_run_success') 
            ];
        }catch (\Exception $e) {
            \Log::error($e);
		}
        return [ 
            'type' => 'error',
            'message' => trans('JobStatus::admin.re_run_error') 
        ];
    }
}
