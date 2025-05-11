<?php

namespace DreamTeam\Base\Repositories\Eloquent;

use DreamTeam\Base\Repositories\Eloquent\BaseRepository;
use DreamTeam\Base\Models\SystemLog;
use DreamTeam\Base\Repositories\Interfaces\SystemLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SystemLogRepository extends BaseRepository implements SystemLogRepositoryInterface
{
    protected string|null|Model $model = SystemLog::class;

    public function deleteWithRequest(Request $request)
    {
        extract($request->all(), EXTR_OVERWRITE);
        $item = $this->getModel();
        if(isset($admin_id) && !empty($admin_id)) {
            $item = $item->where('admin_id', intval($admin_id));
        }
        if(isset($action) && !empty($action)) {
            $item = $item->where('action', (string)($action));
        }
        if(isset($time_start) && !empty($time_start) && isset($time_end) && !empty($time_end)) {
            $item = $item->whereBetween('time', [$time_start, $time_end]);
        }
        $item->delete();
    }
}
