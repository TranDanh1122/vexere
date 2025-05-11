<?php

namespace DreamTeam\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use DreamTeam\AdminUser\Models\AdminUser;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Base\Enums\SystemLogStatusEnum;

class AdminController extends Controller
{
    public $module_name;
    public $table_name;
    public $has_seo;
    public $has_locale;
    public $breadcrumbs;

    function __construct()
    {
        // khai báo assets cho admin
        loadStyleAdmin();
        // share tên module và tên bảng
        View::share('module_name', $this->module_name);
        View::share('table_name', $this->table_name);
        View::share('has_seo', $this->has_seo);
        View::share('has_locale', $this->has_locale);
        // Breadcrumb tự động
        if (isset($this->table_name)) {
            $this->breadcrumbs[] = [
                'name' => $this->module_name,
                'url' => route('admin.' . $this->table_name . '.index')
            ];
            // Lấy breadcrumbs theo tên route
            $action_method = str_replace('admin.' . $this->table_name . '.', '', Route::currentRouteName());
            switch ($action_method) {
                case 'create':
                    $this->breadcrumbs[] = ['name' => 'Translate::table.create'];
                    break;
                case 'show':
                    $this->breadcrumbs[] = ['name' => 'Translate::table.show'];
                    break;
                case 'edit':
                    $this->breadcrumbs[] = ['name' => 'Translate::table.edit'];
                    break;
            }
            View::share('breadcrumbs', $this->breadcrumbs);
        }
        // Sử dụng middleware để check phân quyền và set ngôn ngữ
        $this->middleware(function ($request, $next) {
            // Đặt lại ngôn ngữ nếu trên url có request setLanguage
            // setLanguage($request->setLanguage);
            if (App::getLocale() != config('app.fallback_locale')) {
                setLanguage(config('app.fallback_locale'));
            }

            // Nếu tồn tại table_name thì mới check quyền
            if (isset($this->table_name)) {
                // Lấy ra action method
                $action_method = request()->route()->getName();
                $action_method = array_last(explode('.', $action_method));
                // Lấy ra toàn bộ phương thức từ config DreamTeamModule
                $module_method = [];
                foreach (config('DreamTeamModule')['modules'] as $key => $module) {
                    // Nếu tồn tại config(DreamTeamModule.modules.{module_name}.permision)
                    if (isset($module['permision']) && !empty($module['permision'])) {
                        foreach ($module['permision'] as $permision) {
                            // Nếu tồn tại config(DreamTeamModule.modules.{module_name}.permision.type)
                            if (isset($permision['type']) && !empty($permision['type'])) {
                                // Thêm phương thức
                                $module_method[] = $permision['type'];
                            }
                        }
                    }
                }
                $module_method = array_unique($module_method);
                // Kiểm tra action_method mới các quyền CURD cơ bản
                if (in_array($action_method, $module_method)) {
                    // Nếu phường thức là store thì sẽ check quyền create
                    if ($action_method == 'store') {
                        $action_method = 'create';
                    }
                    // Nếu phường thức là update thì sẽ check quyền edit
                    if ($action_method == 'update') {
                        $current_action = 'edit';
                    }
                    // Quyền
                    $permission = $this->table_name . '_' . $action_method;
                    // Kiểm tra quyền
                    if (checkRole($permission) == false) {
                        return redirect(route('admin.home'))->with([
                            'type' => 'danger',
                            'message' => 'Core::admin.role.no_permission',
                        ]);
                    }
                }
                // Nếu hợp lệ hoặc action_method không thuộc CURD cơ bản ở trên thì tiếp tục
                // Nếu Muốn check quyền tại trang kế tiếp thì phải viết cho từng route
                return $next($request);
            } else {
                return $next($request);
            }
        });
    }

    /** 
     * Kiểm tra slug có tồn tại hay không
     * @param string     $request->table: tên bảng
     * @param string     $request->slug: slug cần check
     */
    public function checkSlug(Request $request, SlugServiceInterface $slugService)
    {
        // Các biến lấy ra từ request
        // chỉ chấp nhận a-z,0-9,-
        $slug = $fullUrl = Str::slug(trim(str_slug($request->slug)), '-', 'en');
        if (empty($slug)) {
            $slug = $fullUrl = time();
        }
        $table = $request->table ?? '';
        $tableId = $request->tableId ?? '';
        $tableSlug = $request->tableSlug ?? '';
        $tableUrl = $request->tableUrl ?? '';
        $showSync = false;
        if ($tableUrl && $tableId) {
            $replaceUrl = str_replace($tableSlug, '{slugName}', $tableUrl);
            $fullUrl = str_replace('{slugName}', $slug, $replaceUrl);
            if ($tableUrl != $fullUrl) {
                $showSync = true;
            }
        }
        if ($tableId && $table) {
            $notCheck = $slugService->findOne([
                'table'    => $table,
                'table_id' => $tableId
            ]);
        }
        $conditions['slug'] = ['=' => $slug];
        if (isset($notCheck) && $notCheck) {
            $conditions['id'] = ['DFF' => $notCheck->id];
        }
        $check = $slugService->findOneWhereFromConditions([], $conditions, 'id', 'desc', false, '*', false);

        if (empty($check)) {
            return response()->json([
                'status' => true,
                'slug' => $slug,
                'showSync' => $showSync,
                'fullUrl' => $fullUrl
            ]);
        } else {
            return response()->json([
                'status' => false,
                'slug' => $slug,
                'showSync' => false,
                'fullUrl' => $fullUrl,
                'link' => __('SyncLink::admin.error_position', ['link' => route('admin.' . $check->table . '.edit', $check->table_id)])
            ]);
        }
    }

    /** 
     * Xóa nhanh
     * @param string     $request->table: tên bảng
     * @param array      $request->id_array: mảng ID cần xóa
     */
    public function quickDelete(Request $request)
    {
        $status = 0;
        $message = '';
        // Các biến lấy ra từ request
        $table = $request->table ?? '';
        $id_array = $request->id_array ?? [];

        // Kiếm tra xem có quyền hay không
        if (checkRole($table . '_delete')) {
            // Kiểm tra có tồn tại bản ghi hay không
            $check = collect(DB::table($table)->whereIn('id', $id_array)->get());
            // Kiểm tra có tồn tại bản ghi hay không
            if (count($check) > 0) {
                // Hành động khi dữ liệu thay đổi
                $this->updateAction($table);
                // Mảng id xóa tạm
                $delete_id = [];
                // Dùng vòng lặp xác định status vả chuyển về trạng thái tương ứng
                // [0,1] => -1 thùng rác
                // [-1] => xóa vĩnh viễn
                foreach ($id_array as $id) {
                    $record = $check->where('id', $id)->first();
                    if (!empty($record)) {
                        if ($record->status == -1) {
                            // Xóa vĩnh viễn ở đây
                        } else {
                            // Đưa vào thùng rác
                            $delete_id[] = $id;
                            // Ghi logs
                            systemLogs(SystemLogStatusEnum::QUICK_DELETE, ['status' => BaseStatusEnum::DELETE], $table, $id);
                        }
                    }
                }
                DB::table($table)->whereIn('id', $delete_id)->update(['status' => BaseStatusEnum::DELETE]);
                event(new ClearCacheEvent());
                $status = 1;
                $message = 'Core::admin.delete_success';
            } else {
                $message = 'Core::admin.no_data_delete';
            }
        } else {
            $message = 'Core::admin.no_permission';
        }
        return [
            'status' => $status,
            'message' => __($message),
        ];
    }

    /** 
     * Lấy lại nhanh
     * @param string     $request->table: tên bảng
     * @param array      $request->id_array: mảng ID cần lấy lại
     */
    public function quickRestore(Request $request)
    {
        $status = 0;
        $message = '';
        // Các biến lấy ra từ request
        $table = $request->table ?? '';
        $id_array = $request->id_array ?? [];

        // Kiếm tra xem có quyền hay không
        if (checkRole($table . '_restore')) {
            // Kiểm tra có tồn tại bản ghi hay không
            $check = DB::table($table)->whereIn('id', $id_array)->get();
            // Kiểm tra có tồn tại bản ghi hay không
            if (count($check) > 0) {
                // Hành động khi dữ liệu thay đổi
                $this->updateAction($table);
                // Thay đổi dữ liệu
                $save_count = DB::table($table)->whereIn('id', $id_array)->update(['status' => BaseStatusEnum::ACTIVE]);
                // Kiểm tra dữ liệu đã được thay đổi hay chưa
                if ($save_count != 0) {
                    foreach ($id_array as $id) {
                        // Ghi logs
                        systemLogs(SystemLogStatusEnum::QUICK_RESTORE, ['status' => BaseStatusEnum::DELETE], $table, $id);
                    }
                    $status = 1;
                    $message = 'Core::admin.restore_success';
                } else {
                    $message = 'Core::admin.ajax_error';
                }
            } else {
                $message = 'Core::admin.no_data_restore';
            }
        } else {
            $message = 'Core::admin.no_permission';
        }
        event(new ClearCacheEvent());
        return [
            'status' => $status,
            'message' => __($message),
        ];
    }

    /** 
     * Cập nhật nhanh
     * @param string     $request->table: tên bảng
     * @param array      $request->id_array: mảng ID cần sửa
     * @param string     $request->value: giá trị
     * @param string     $request->field: tên trường
     */
    public function quickEdit(Request $request)
    {
        $status = 0;
        $message = '';
        // Các biến lấy ra từ request
        $table = $request->table ?? '';
        $id_array = $request->id_array ?? [];
        $value = $request->value ?? '';
        $field = $request->field ?? '';
        if ($table == (new AdminUser())->getTable() && in_array(Auth::guard('admin')->user()->id, $id_array) && $field == 'status') {
            return [
                'status' => 0,
                'message' => __('Core::admin.update_permistion_admin'),
            ];
        }
        // Kiếm tra xem có quyền hay không
        if (checkRole($table . '_edit')) {
            // Kiểm tra có tồn tại bản ghi hay không
            $check = DB::table($table)->whereIn('id', $id_array)->get();
            // Kiểm tra có tồn tại bản ghi hay không
            if (count($check) > 0) {
                // Hành động khi dữ liệu thay đổi
                $this->updateAction($table);
                // Ghi logs
                foreach ($id_array as $id) {
                    systemLogs(SystemLogStatusEnum::QUICK_UPDATE, [$field => $value], $table, $id);
                }
                // Thay đổi dữ liệu
                $save_count = DB::table($table)->whereIn('id', $id_array)->update([$field => $value]);
                // Kiểm tra dữ liệu đã được thay đổi hay chưa
                if ($save_count != 0) {
                    $status = 1;
                    $message = 'Core::admin.update_success';
                } else {
                    $message = 'Core::admin.ajax_error_edit';
                }
            } else {
                $message = 'Core::admin.no_data_edit';
            }
        } else {
            $message = 'Core::admin.no_permission';
        }
        event(new ClearCacheEvent());
        return [
            'status' => $status,
            'message' => __($message),
        ];
    }

    /**
     * Tìm kiếm tại Form
     * @param string     $request->table: tên bảng
     * @param array      $request->id: Tên cột lấy giá trị
     * @param string     $request->name: Tên cột lấy tên
     * @param string     $request->keyword: tên trường
     * @param array      $request->id_not_where: Không lấy id có tại mảng này
     * @param string     $request->suggest_locale: có search theo đa ngôn ngữ hay không
     */
    public function suggestSearch(Request $request)
    {
        $table = $request->table ?? '';
        $id = $request->id ?? 'id';
        $name = $request->name ?? 'name';
        $keyword = $request->keyword ?? '';
        $id_not_where = $request->id_not_where ?? [];
        $suggest_locale = intval($request->suggest_locale ?? 0);

        $lists = DB::table($table)->where('status', 1);
        // Search theo đa ngôn ngữ
        if ($suggest_locale) {
            $lists = $lists->join('language_metas', 'language_metas.lang_table_id', $table . '.id')
                ->where('language_metas.lang_table', $table)
                ->where('language_metas.lang_locale', Request()->lang_locale ?? App::getLocale())
                ->select($table . '.*');
        }
        // Tiếp tục tìm
        $lists = $lists->whereNotIn('id', $id_not_where)->where($name, 'like', '%' . $keyword . '%')->orderBy($id, 'DESC')->limit(30)->offset(0)->get();
        if ($lists->count()) {
            $result = [];
            foreach ($lists as $item) {
                $result[] = ['id' => $item->$id, 'name' => $item->$name];
            }
            return response()->json(['status' => 1, 'message' => __('Translate::admin.has_found_data'), 'data' => $result]);
        } else {
            return response()->json(['status' => 0, 'message' => __('Translate::admin.not_found_data')]);
        }
    }
    public function sussgetMenu(Request $request)
    {
        $table = $request->table ?? 'posts';
        $name = $request->name ?? 'name';
        $keyword = $request->keyword ?? '';
        $lang_locale = $request->lang_locale ?? '';
        $has_locale = $request->suggest_locale ?? false;
        $menuStores = menu_store()->getAll();
        if (!isset($menuStores[$table])) {
            return [
                'count' => 0,
                'error' => true,
                'message' => __('Translate::form.menu.module_not_found')
            ];
        }
        $tableItem = $menuStores[$table] ?? [];
        $model = new $tableItem['models'];
        $datas = $model->active()
            ->where(function ($query) use ($keyword, $name) {
                $query->where($name, 'LIKE', "%" . str_replace(' ', '%', $keyword) . "%")
                    ->orWhere('slug', 'LIKE', "%" . $keyword . "%");
            });
        $table_name = $model->getTable();
        if ($has_locale == true) {
            $datas = $datas->join('language_metas', 'language_metas.lang_table_id', $table_name . '.id')
                ->where('lang_table', $table_name)
                ->where('lang_locale', $lang_locale ?? App::getLocale());
        }
        if (isset($tableItem['withable'])) {
            $datas = $datas->with($tableItem['withable']);
        }
        $datas = $datas->selectRaw($tableItem['select'] ?? 'id, slug, name')->limit(30)->get();
        $menu = [];
        foreach ($datas as $value) {
            $url = str_replace(config('app.url'), '', $value->getUrl());
            $menu[$url] = [
                'id'        => $value->id,
                'name'      => $value->getName(),
                'url'       => $url
            ];
        }
        return [
            'count' => count($menu),
            'data' => $menu
        ];
    }

    /**
     * Tìm kiếm và trả về dữ liệu tại bảng
     * @param string     $request->table: tên bảng
     * @param string     $request->table_field: Tên cột lấy tên
     * @param string     $request->keyword: tên trường
     * @param string     $request->suggest_locale: có search theo đa ngôn ngữ hay không
     * @param array      $request->id_not_where: Không lấy id có tại mảng này
     */
    public function suggestTable(Request $request)
    {
        $table = $request->table ?? '';
        $table_field = $request->table_field ?? 'name';
        $keyword = $request->keyword ?? '';
        $suggest_locale = intval($request->suggest_locale ?? 0);
        $id_not_where = $request->id_not_where ?? [];

        $lists = DB::table($table)->where('status', 1);
        // Search theo đa ngôn ngữ
        if ($suggest_locale) {
            $lists = $lists->join('language_metas', 'language_metas.lang_table_id', $table . '.id')
                ->where('language_metas.lang_table', $table)
                ->where('language_metas.lang_locale', Request()->lang_locale ?? App::getLocale())
                ->select($table . '.*');
        }
        // Tiếp tục tìm
        $lists = $lists->whereNotIn('id', $id_not_where)->where($table_field, 'like', '%' . $keyword . '%')->orderBy('id', 'DESC')->limit(30)->get();
        if ($lists->count()) {
            return response()->json(['status' => 1, 'message' => __('Translate::admin.has_found_data'), 'data' => $lists]);
        } else {
            return response()->json(['status' => 0, 'message' => __('Translate::admin.not_found_data')]);
        }
    }

    /** 
     * Xóa cache
     */
    public function cacheClear()
    {
        $exitCode = Artisan::call('dreamteam:clear');
        if ($exitCode == 0) {
            return [
                'status' => 1,
                'message' => __('Core::admin.cache_clear_success'),
            ];
        } else {
            return [
                'status' => 2,
                'message' => __('Core::admin.cache_clear_fail'),
            ];
        }
    }

    // Hành động khi dữ liệu thay đổi
    public function updateAction($table)
    {
    }
}
