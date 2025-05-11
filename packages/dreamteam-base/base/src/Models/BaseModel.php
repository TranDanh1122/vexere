<?php

namespace DreamTeam\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Media\Facades\RvMedia;

class BaseModel extends Model
{
    /**
     * Query trực tiếp từ models
     * @param models            $show_data: models
     * @param requests          $requests: các giá trị tuyên lên
     */
    public function queryAdmin($show_data, $requests)
    {
        // DEMO: $show_data = $show_data->where('status', 1);
        return $show_data;
    }

    /**
     * Lấy link mặc định cho toàn bộ modules nếu module đó không có hàm getUrl
     */
    public function getUrl()
    {
        return '/';
    }

    public function getName()
    {
        return $this->name ?? '';
    }

    /**
     * Lấy ngày tạo
     * @param string            $format: định dạng ngày
     */
    public function getTime($field = 'created_at', $format = 'H:i:s d/m/Y')
    {
        if (!empty($this->$field)) {
            return formatTime($this->$field, $format);
        } else {
            return '';
        }
    }

    /**
     * Lấy ra ảnh đại diện
     * @param string            $size: Kích thước ảnh quy định tại config('DreamTeamMedia.imageSize')
     * @param string            $fields: tên trường ảnh [Mặc định là image]
     */
    public function getImage($size = '', $fields = 'image')
    {
        return RvMedia::getImageUrl($this->$fields ?? '', null, $size);
    }

    /**
     * Lấy ra mô tả
     * @param string            $fields: tên trường ảnh [Mặc định là detail]
     */
    public function getDesc($number =  170, $fields = 'detail')
    {
        return cutString(removeHTML($this->$fields), $number);
    }

    //  CÁC HÀM THUỘC HỖ TRỢ DANH MỤC

    /**
     * Lấy ra danh mục cha
     * Nếu không có trả về null
     */
    public function getParent()
    {
        if ($this->parent_id != 0) {
            $parent = BaseModel::where('id', $this->parent_id)->first();
            return $parent;
        } else {
            return null;
        }
    }

    /**
     * Lấy ra danh mục con (Chỉ lấy danh mục con cấp 1)
     */
    public function getChild()
    {
        return BaseModel::where('parent_id', $this->id)->orderBy('order', 'ASC')->get();
    }

    /**
     * Lấy các danh mục ông cha ... dùng cho 1 số trường hợp như hiển thì breadcrumb
     * @return mảng danh mục ông cha của danh mục hiện tại theo thứ tự
     */
    public function getParentIds()
    {
        $case = [];
        $parents = $this->getParent();
        while ($parents) {
            array_unshift($case, $parents);
            $parents = $parents->getParent();
        }
        return $case;
    }

    /**
     * Lấy ra danh mục con (Lấy ra mọi cấp danh mục con)
     */
    public function getChildIds()
    {
        $ids = [$this->id];
        $childs = $this->getChild();
        if ($childs->count()) {
            foreach ($childs as $value) {
                $ids = array_merge($ids, $value->getChildIds());
            }
        }
        return $ids;
    }

    public static function determineIfUsingUuidsForId(): bool
    {
        return config('app.using_uuids_for_id', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', BaseStatusEnum::ACTIVE);
    }

    public function scopeActiveShow($query)
    {
        if (Auth::guard('admin')->check()) {
            return $query->whereIn('status', [BaseStatusEnum::ACTIVE, BaseStatusEnum::DRAFT]);
        }
        return $query->where('status', BaseStatusEnum::ACTIVE);
    }

    public function language_metas()
    {
        $table_name = BaseModel::getTable();
        return $this->hasOne('DreamTeam\Base\Models\LanguageMeta', 'lang_table_id', 'id')->where('lang_table', $table_name);
    }

    public function getDetail()
    {
        if($this->detail) {
            if (function_exists('replaceContent')) {
                return html_entity_decode(replaceContent($this->detail));
            }
            return html_entity_decode($this->detail);
        }
        return ' ';
    }

    public function seo()
    {
        $table_name = BaseModel::getTable();
        return $this->hasOne('DreamTeam\Base\Models\Seo', 'type_id', 'id')->where('type', $table_name);
    }

    // Trong mô hình Fit
    public function tableOption()
    {
        return $this->morphOne(TableOption::class, 'table');
    }
}
