<?php

namespace DreamTeam\SyncLink\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use DreamTeam\SyncLink\Enums\SyncLinkEnum;

class SyncLinkImport implements ToModel
{
    public $syncLinkService;

    public function __construct($syncLinkService)
    {
        $this->syncLinkService = $syncLinkService;
    }
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        if (isset($row[0]) && !empty($row[0]) && !empty($row[1] ?? '') && !empty($row[1] ?? '')) {
            $checkExists = $this->syncLinkService->findOne(['old' => $row[0]]);
            $code = intval($row[2] ?? SyncLinkEnum::TEMPORARY);
            if(empty($code)) $code = SyncLinkEnum::TEMPORARY;
            if ($checkExists == null) {
                $this->syncLinkService->create([
                   'old'        => trim(rtrim($row[0] ?? '', '/')),
                   'new'        => trim($row[1] ?? ''),
                   'code'       => $code,
                   'status'     => 1,
                ]);
            } else {
                $this->syncLinkService->update($checkExists->id, [
                   'new'        => trim($row[1] ?? ''),
                   'code'       => $code,
                   'status'     => 1,
                ]);
            }
        }
        return null;
    }
}