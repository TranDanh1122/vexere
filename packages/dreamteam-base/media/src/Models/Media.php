<?php

namespace DreamTeam\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use DreamTeam\Media\Facades\RvMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DreamTeam\Base\Facades\BaseHelper;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'medias';

    protected $fillable = [
        'user_id', 'name', 'size', 'title', 'caption', 'type', 'extention', 'url', 'parent_id', 'folder_id', 'mime_type', 'options', 'status'
    ];
    public function getPath()
    {
        $created_at = $this->created_at;
        $year = $created_at->format('Y');
        $month = $created_at->format('m');
        $path = $year . '/' . $month . '/';
        return $path;
    }
    public function getImage($size = 'medium')
    {
        return RvMedia::getImageUrl($this->url, null, $size);
    }
    public function getUrl()
    {
        if (isset($this->name)) {
            $image = getImageMedia($this->getPath() . $this->name, '', $this->url);
            return $image;
        } else {
            return getImageMedia();
        }
    }

    public function getSize()
    {
        return formatSizeUnits($this->size);
    }
    public function getCreatedAt()
    {
        return $this->created_at->format('H:i d/m/Y');
    }
    public function getUpdatedAt()
    {
        return $this->updated_at->format('H:i d/m/Y');
    }
    public function getTitle()
    {
        return $this->title ?? '';
    }
    public function getCaption()
    {
        return $this->caption ?? '';
    }

    protected $casts = [
        'options' => 'json'
    ];

    protected static function booted(): void
    {
        static::forceDeleting(fn (Media $file) => RvMedia::deleteFile($file));
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id')->withDefault();
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $type = 'document';

                foreach (RvMedia::getConfig('mime_types', []) as $key => $value) {
                    if (in_array($attributes['mime_type'], $value)) {
                        $type = $key;

                        break;
                    }
                }

                return $type;
            }
        );
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(fn () => BaseHelper::humanFilesize($this->size));
    }

    protected function icon(): Attribute
    {
        return Attribute::get(function () {
            $type = $this->type;
            if ($type == 'document' && in_array($this->mime_type, ['application/vnd.ms-excel', 'application/excel', 'application/x-excel', 'application/x-msexcel', 'text/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                $type  = 'excel';
            }
            if ($type == 'document' && in_array($this->mime_type, ['application/pdf'])) {
                $type  = 'pdf';
            }
            if ($type == 'document' && in_array($this->mime_type, ['application/zip'])) {
                $type  = 'zip';
            }
            $icon = match ($type) {
                'image' => 'ti ti-photo',
                'video' => 'ti ti-video',
                'pdf' => 'ti ti-file-type-pdf',
                'excel' => 'ti ti-file-spreadsheet',
                'zip' => 'ti ti-file-zip',
                default => 'ti ti-file',
            };
            return view('Core::components.icon', ['name' => $icon])->render();
        });
    }

    protected function previewUrl(): Attribute
    {
        return Attribute::get(function (): string|null {
            $preview = null;

            switch ($this->type) {
                case 'image':
                case 'pdf':
                case 'text':
                case 'video':
                    $preview = RvMedia::url($this->url);

                    break;
                case 'document':
                    if ($this->mime_type === 'application/pdf') {
                        $preview = RvMedia::url($this->url);

                        break;
                    }

                    $config = config('core.media.media.preview.document', []);
                    if (
                        Arr::get($config, 'enabled') &&
                        Request::ip() !== '127.0.0.1' &&
                        in_array($this->mime_type, Arr::get($config, 'mime_types', [])) &&
                        $url = Arr::get($config, 'providers.' . Arr::get($config, 'default'))
                    ) {
                        $preview = Str::replace('{url}', urlencode(RvMedia::url($this->url)), $url);
                    }

                    break;
            }

            return $preview;
        });
    }

    protected function previewType(): Attribute
    {
        return Attribute::get(fn () => Arr::get(config('core.media.media.preview', []), "$this->type.type"));
    }

    public function canGenerateThumbnails(): bool
    {
        return RvMedia::canGenerateThumbnails($this->mime_type);
    }

    public static function createName(string $name, int|string|null $folder): string
    {
        $index = 1;
        $baseName = $name;
        while (self::query()->where('name', $name)->where('folder_id', $folder)->withTrashed()->exists()) {
            $name = $baseName . '-' . $index++;
        }

        return $name;
    }

    public static function createSlug(string $name, string $extension, string|null $folderPath): string
    {
        if (setting('media_use_original_name_for_file_path')) {
            $slug = $name;
        } else {
            $slug = Str::slug($name, '-', !RvMedia::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);
        }

        $index = 1;
        $baseSlug = $slug;

        while (Storage::exists(rtrim($folderPath, '/') . '/' . $slug . '.' . $extension)) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = $slug . '-' . time();
        }

        return $slug . '.' . $extension;
    }
}
