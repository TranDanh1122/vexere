<?php

namespace DreamTeam\AdminUser\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class BaseController extends Controller
{
    public $tableName;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Đặt lại ngôn ngữ nếu trên url có request setLanguage
            if (App::getLocale() != config('app.fallback_locale')) {
                setLanguage(config('app.fallback_locale'));
            }

            // Nếu tồn tại table_name thì mới check quyền
            if (isset($this->tableName)) {
                // Lấy ra action method
                $actionMethod = request()->route()->getName();
                $actionMethod = array_last(explode('.', $actionMethod));
                // Lấy ra toàn bộ phương thức từ config DreamTeamModule
                $moduleMethod = [];
                foreach (config('DreamTeamModule')['modules'] as $key => $module) {
                    // Nếu tồn tại config(DreamTeamModule.modules.{module_name}.permision)
                    if (isset($module['permision']) && !empty($module['permision'])) {
                        foreach ($module['permision'] as $permision) {
                            // Nếu tồn tại config(DreamTeamModule.modules.{module_name}.permision.type)
                            if (isset($permision['type']) && !empty($permision['type'])) {
                                // Thêm phương thức
                                $moduleMethod[] = $permision['type'];
                            }
                        }
                    }
                }
                $moduleMethod = array_unique($moduleMethod);
                // Kiểm tra actionMethod mới các quyền CURD cơ bản
                if (in_array($actionMethod, $moduleMethod)) {
                    // Nếu phường thức là store thì sẽ check quyền create
                    if ($actionMethod == 'store') {
                        $actionMethod = 'create';
                    }
                    // Quyền
                    $permission = $this->tableName . '_' . $actionMethod;
                    // Kiểm tra quyền
                    if (request()->user()->hasRole($permission) == false) {
                        return response()->json([
                            'error' => true,
                            'message' => __('Core::admin.role.no_permission'),
                        ]);
                    }
                }
                // Nếu hợp lệ hoặc actionMethod không thuộc CURD cơ bản ở trên thì tiếp tục
                // Nếu Muốn check quyền tại trang kế tiếp thì phải viết cho từng route
                return $next($request);
            } else {
                return $next($request);
            }
        });
    }
}
