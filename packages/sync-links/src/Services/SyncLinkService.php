<?php

namespace DreamTeam\SyncLink\Services;

use DreamTeam\SyncLink\Repositories\Interfaces\SyncLinkRepositoryInterface;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Services\CrudService;
use Illuminate\Http\Request;

class SyncLinkService extends CrudService implements SyncLinkServiceInterface
{

    public function __construct(
        SyncLinkRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    public function addLinkToSync(Request $requests, string $oldLink, string $newLink): void
    {
        if($requests->get('add_slug_sync', 0)) {
            $appUrl = rtrim(config('app.url'), '/');
            $oldLink = trim(str_replace($appUrl, '', $oldLink));
            $newLink = trim(str_replace($appUrl, '', $newLink));
            if($oldLink != $newLink) {
                $checkExists = $this->repository->findOneFromArray(['old' => $oldLink], false);
                if ($checkExists == null) {
                    $this->repository->createFromArray([
                       'old'        => $oldLink,
                       'new'        => $newLink,
                       'code'       => 301,
                       'status'     => 1,
                    ], false);
                } else {
                    $this->repository->updateByPrimary($checkExists->id, [
                       'new'        => $newLink,
                       'code'       => 301,
                       'status'     => 1,
                    ], false);
                }
            }
        }
    }
}
