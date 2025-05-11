<?php

namespace DreamTeam\JobStatus\EventManagers;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use DreamTeam\JobStatus\Models\JobStatus;
use DreamTeam\JobStatus\JobStatusUpdater;

abstract class EventManager
{
    abstract public function before(JobProcessing $event): void;

    abstract public function after(JobProcessed $event): void;

    abstract public function failing(JobFailed $event): void;

    abstract public function exceptionOccurred(JobExceptionOccurred $event): void;

    private $updater;

    private $entity;

    public function __construct(JobStatusUpdater $updater)
    {
        $this->updater = $updater;
        $this->entity = app(JobStatus::class);
    }

    /**
     * @return JobStatusUpdater
     */
    protected function getUpdater()
    {
        return $this->updater;
    }

    /**
     * @return JobStatus
     */
    protected function getEntity()
    {
        return $this->entity;
    }
}
