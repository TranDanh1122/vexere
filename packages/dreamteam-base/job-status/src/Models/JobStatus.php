<?php

namespace DreamTeam\JobStatus\Models;

use Illuminate\Database\Eloquent\Model;
use DreamTeam\Base\Models\BaseModel;

/**
 * DreamTeam\JobStatus.
 *
 * @property int    $id
 * @property string $job_id
 * @property string $type
 * @property string $queue
 * @property int    $attempts
 * @property int    $progress_now
 * @property int    $progress_max
 * @property string $status
 * @property string $input
 * @property string $output
 * @property string $created_at
 * @property string $started_at
 * @property string $finished_at
 * @property mixed  $is_ended
 * @property mixed  $is_executing
 * @property mixed  $is_failed
 * @property mixed  $is_finished
 * @property mixed  $is_queued
 * @property mixed  $is_retrying
 * @property mixed  $job_uuid
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereAttempts($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereFinishedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereInput($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereJobId($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereOutput($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereProgressMax($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereProgressNow($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereQueue($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereStartedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereStatus($value)
 * @method   static \Illuminate\Database\Query\Builder|\DreamTeam\JobStatus\Models\JobStatus whereType($value)
 * @mixin \Eloquent
 */
class JobStatus extends BaseModel
{
    const STATUS_QUEUED = 'queued';
    const STATUS_EXECUTING = 'executing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';
    const STATUS_RETRYING = 'retrying';

    // type
    const IMPORT_WP = 'import_wordpress';
    const IMPORT_IMAGE_FAILD = 'import_image_faild';
    const IMPORT_AUTHOR = 'wp_import_author';
    const IMPORT_COMMENT = 'wp_import_comment';
    const IMPORT_PAGE = 'wp_import_page';
    const IMPORT_POST = 'wp_import_post';
    const IMPORT_POST_CATEGORY = 'wp_import_post_category';
    const IMPORT_POST_TYPE = 'wp_import_post_type';
    const IMPORT_PRODUCT = 'wp_import_product';
    const IMPORT_PRODUCT_CATEGORY = 'wp_import_product_category';
    const IMPORT_IMAGE_CRAWL = 'wp_import_image_crawl';
    const IMPORT_DATA_CRAWL = 'wp_import_data_crawl';
    const IMPORT_PRODUCT_EXCEL = 'import_product_excel';
    const REMAKE_WEBP = 'remake_webp';

    protected $table = "job_statuses";

    public $dates = ['started_at', 'finished_at', 'created_at', 'updated_at'];

    protected $guarded = [];

    protected $casts = [
        'input' => 'array',
        'output' => 'array',
    ];

    /* Accessor */
    public function getProgressPercentageAttribute()
    {
        return $this->progress_max !== 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }

    public function getIsEndedAttribute()
    {
        return \in_array($this->status, [self::STATUS_FAILED, self::STATUS_FINISHED], true);
    }

    public function getIsFinishedAttribute()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function getIsFailedAttribute()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getIsExecutingAttribute()
    {
        return $this->status === self::STATUS_EXECUTING;
    }

    public function getIsQueuedAttribute()
    {
        return $this->status === self::STATUS_QUEUED;
    }

    public function getIsRetryingAttribute()
    {
        return $this->status === self::STATUS_RETRYING;
    }

    public static function getListStatus() {
        return [
            self::STATUS_QUEUED => 'JobStatus::progress.status_queued',
            self::STATUS_EXECUTING => 'JobStatus::progress.status_executing',
            self::STATUS_FINISHED => 'JobStatus::progress.status_finished',
            self::STATUS_FAILED => 'JobStatus::progress.status_failed',
            self::STATUS_RETRYING => 'JobStatus::progress.status_retrying',
        ];
    }

    public function getLableStatus() {
        return [
            self::STATUS_QUEUED => 'secondary',
            self::STATUS_EXECUTING => 'primary',
            self::STATUS_FINISHED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_RETRYING => 'info',
        ][$this->status] ?? 'info';
    }

    public function getCurrentStatus() {
        return self::getListStatus()[$this->status] ?? '';
    }

    public static function getAllowedStatuses()
    {
        return [
            self::STATUS_QUEUED,
            self::STATUS_EXECUTING,
            self::STATUS_FINISHED,
            self::STATUS_FAILED,
            self::STATUS_RETRYING,
        ];
    }

    public static function allTypeJob() {
        return [
            self::IMPORT_WP => 'JobStatus::progress.import_wordpress',
            self::REMAKE_WEBP => 'JobStatus::progress.remake_webp',
            self::IMPORT_IMAGE_FAILD => 'JobStatus::progress.import_image_faild',
            self::IMPORT_IMAGE_CRAWL => 'JobStatus::progress.wp_import_image_crawl',
            self::IMPORT_DATA_CRAWL => 'JobStatus::progress.wp_import_data_crawl',
            self::IMPORT_PRODUCT_EXCEL => 'JobStatus::progress.import_product_excel',
            'convert_media' => 'Convert media',
            'generate_thumbnail' => 'media::media.setting.generate_thumbnails',
        ] + (array) apply_filters(FILTER_GET_JOB_STATUS_TYPE, null);
    }

    public function getType() {
        return self::allTypeJob()[$this->type] ?? '';
    }
}
