<?php

namespace DreamTeam\Base\Supports;

use Illuminate\Support\Manager;

class SettingsManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'database';
    }

    public function createJsonDriver(): JsonSettingStore
    {
        return new JsonSettingStore(app('files'));
    }

    public function createDatabaseDriver(): DatabaseSettingStore
    {
        return new DatabaseSettingStore();
    }
}
