<?php

namespace DreamTeam\Base\Supports;

use DreamTeam\Base\Models\BaseModel;
use DreamTeam\Base\Supports\Helper;
use DreamTeam\Base\Models\Setting;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use UnexpectedValueException;

class DatabaseSettingStore extends SettingStore
{
    protected bool $connectedDatabase = false;

    public function forget($key): SettingStore
    {
        parent::forget($key);

        // because the database store cannot store empty arrays, remove empty
        // arrays to keep data consistent before and after saving
        $segments = explode('.', $key);
        array_pop($segments);

        while ($segments) {
            $segment = implode('.', $segments);

            // non-empty array - exit out of the loop
            if ($this->get($segment)) {
                break;
            }

            // remove the empty array and move on to the next segment
            $this->forget($segment);
            array_pop($segments);
        }

        return $this;
    }

    protected function write(array $data): void
    {
        foreach ($data as $key => $value) {
            if (Setting::where('key', $key)->exists()) {
                Setting::where('key', $key)->update([
                    'value'     => $value
                ]);
            } else {
                Setting::insert([
                    'key'       => $key,
                    'locale'    => '',
                    'value'     => $value
                ]);
            }
        }
    }

    protected function read(): array
    {
        if (! $this->connectedDatabase) {
            $this->connectedDatabase = Helper::isConnectedDatabase();
        }

        if (! $this->connectedDatabase) {
            return [];
        }

        $data = $this->parseReadData(Setting::get());

        return $data;
    }

    public function getByKey(string $key, string $default = null): string|null
    {
        return Setting::where('key', $key)->first()->value ?? $default;
    }

    /**
     * Parse data coming from the database.
     */
    public function parseReadData(Collection|array $data): array
    {
        $results = [];

        foreach ($data as $row) {
            if (is_array($row)) {
                $key = $row['key'];
                $value = $row['value'];
                $locale = $row['locale'];
            } elseif (is_object($row)) {
                $key = $row->key;
                $value = $row->value;
                $locale = $row->locale;
            } else {
                $msg = 'Expected array or object, got ' . gettype($row);

                throw new UnexpectedValueException($msg);
            }
            $results[$key][$locale] = $value;
        }

        return $results;
    }
}
