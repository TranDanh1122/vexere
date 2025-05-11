<?php

namespace DreamTeam\Page\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use Form;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DreamTeam\Base\Events\UpdateAttributeImageInContentEvent;
use DreamTeam\Page\Http\Requests\PageRequest;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\BaseServiceInterface;
use DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface;
use DreamTeam\Page\Services\Interfaces\PageServiceInterface;
use DreamTeam\SyncLink\Services\Interfaces\SyncLinkServiceInterface;
use DreamTeam\Page\Models\Page;
use DreamTeam\Base\Enums\SystemLogStatusEnum;

class PageController extends AdminController
{
    protected PageServiceInterface $pageService;
    protected BaseServiceInterface $baseService;
    protected SystemLogServiceInterface $systemLogService;
    protected SyncLinkServiceInterface $syncLinkService;

    function __construct(
        PageServiceInterface $pageService,
        BaseServiceInterface $baseService,
        SystemLogServiceInterface $systemLogService,
        SyncLinkServiceInterface $syncLinkService
    ) {
        $this->table_name = (new Page)->getTable();
        $this->module_name = 'Page::page.name';
        $this->has_seo = true;
        $this->has_locale = true;
        $this->pageService = $pageService;
        $this->baseService = $baseService;
        $this->systemLogService = $systemLogService;
        $this->syncLinkService = $syncLinkService;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $listdata = new ListData(
            $requests,
            $this->pageService,
            $this->table_name,
            'Page::table.index',
            [],
            true,
            $this->has_locale,
            30,
            [$this->table_name . '.id' => 'desc']
        );
        // Build Form tìm kiếm
        $listdata->search('name', __('Core::admin.general.name'), 'string');
        $listdata->search('created_at', __('Core::admin.general.created_at'), 'range');
        $listdata->search('status', __('Core::admin.general.status'), 'array', BaseStatusEnum::tableLabels());
        // Build các hành động
        $listdata->action('status');
        // Build bảng
        $listdata->add('name', __('Core::admin.general.name'), 1);
        $listdata->add('created_at', __('Core::admin.general.time'), 1, 'publish');
        $listdata->add('seo_point', __('Core::admin.general.time'), 1, 'seo_detail');
        $listdata->add('', 'Language', 0, 'lang');

        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Khởi tạo form
        $form = new Form;

        $form->lang($this->table_name);
        $form->text('name', '', 1, __('Core::admin.general.title'), '', false, '', false, 191);
        $form->slug('slug', [
            'value' => '',
        ], 1, __('Core::admin.general.slug'), 'name', true, $this->table_name, true);
        $form->tableOption('FILTER_RENDER_FORM_OPTION_PAGE', $this->table_name);
        $form->ckeditor('detail', '', 0, __('Core::admin.general.content'), false, false, '', true);
        $form->checkbox('status', BaseStatusEnum::ACTIVE, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'));
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $requests)
    {
        $status = BaseStatusEnum::DEACTIVE;
        $hide_sidebar = $hide_breadcrumb = $hide_title = $hide_toc = 0;
        $detail = '';
        extract($requests->all(), EXTR_OVERWRITE);
        if ($redirect == 'save') {
            $status = BaseStatusEnum::DRAFT;
            $redirect = 'edit';
        }
        if ($redirect == 'exit') {
            $redirect = 'index';
        }
        $primary_keyword = $primary_keyword ?? '';
        $secondary_keyword = $secondary_keyword ?? '';
        $seo_point = intval($seo_point ?? 0);
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'slug', 'detail', 'primary_keyword', 'secondary_keyword', 'seo_point', 'status', 'hide_sidebar', 'hide_breadcrumb', 'hide_title', 'hide_toc', 'created_at', 'updated_at');
        DB::beginTransaction();
        try {
            $page = $this->pageService->create($compact);
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $page->id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::CREATE, $compact, true);
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollback();
            return redirect()->back()->with([
                'type' => 'danger',
                'message' => __('Theme::general.error_message_catch') . $e->getMessage()
            ]);
        }
        DB::commit();
        // Cập nhật thuộc tính thẻ img
        event(new UpdateAttributeImageInContentEvent($this->table_name, $page->id, 'detail'));
        // Điều hướng
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $page->id))->with([
            'type' => 'success',
            'message' => __('Translate::admin.create_success')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        // Khởi tạo form
        $form = new Form;
        $page = $page->load('language_metas');
        $recordLangLocale = $page->language_metas->lang_locale ?? getLocale();
        $form->lang($this->table_name);
        $form->text('name', $page->name, 1,  __('Core::admin.general.title'), '', false, '', false, 191);
        $form->slug('slug', [
            'value' => $page->slug,
            'dataTable' => $page
        ], 1, __('Core::admin.general.slug'), '', true, $this->table_name, true);
        $form->tableOption('FILTER_RENDER_FORM_OPTION_PAGE', $this->table_name, $page->id);
        $form->ckeditor('detail', $page->detail, 0, __('Core::admin.general.content'), false, false, '', true);
        $form->checkbox('status', $page->status, BaseStatusEnum::ACTIVE, __('Core::admin.general.status'));
        $form->action('edit', $page->getUrl());
        $id = $page->id;
        $data_edit = $page;
        // Hiển thị form tại view
        return $form->render('edit', compact('id', 'data_edit', 'recordLangLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  DreamTeam\Page\Http\Requests\PageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $requests, $id)
    {
        $page = $this->pageService->read($id);
        $status = BaseStatusEnum::DEACTIVE;
        $detail = '';
        $hide_title = $hide_sidebar = $hide_breadcrumb = $hide_toc = 0;
        $primary_keyword = $page->primary_keyword ?? '';
        $secondary_keyword = $page->secondary_keyword ?? '';
        $seo_point = intval($page->seo_point ?? 0);
        extract($requests->all(), EXTR_OVERWRITE);
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name', 'slug', 'detail', 'hide_sidebar', 'hide_breadcrumb', 'hide_title', 'hide_toc', 'seo_point', 'primary_keyword', 'secondary_keyword', 'status', 'updated_at');
        DB::beginTransaction();
        try {
            $this->baseService->handleRelatedRecord($requests, $this->table_name, $id, $this->has_seo, $this->has_locale, true, SystemLogStatusEnum::UPDATE, $compact, true);
            $result = $this->pageService->update($id, $compact);
            $this->syncLinkService->addLinkToSync($requests, $page->getUrl(), $result->getUrl());
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollback();
            return redirect()->back()->with([
                'type' => 'danger',
                'message' => __('Theme::general.error_message_catch') . $e->getMessage()
            ]);
        }
        DB::commit();
        // Cập nhật thuộc tính thẻ img
        event(new UpdateAttributeImageInContentEvent($this->table_name, $id, 'detail'));
        // Điều hướng
        return redirect(route('admin.' . $this->table_name . '.' . $redirect, $id))->with([
            'type' => 'success',
            'message' => __('Translate::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteForever(Request $requests, $id): JsonResponse
    {
        if (!checkRole($this->table_name . '_delete')) {
            return response()->json([
                'status' => 2,
                'message' => __('Core::admin.no_permission')
            ]);
        }
        DB::beginTransaction();
        $idArray = $requests->idArray ?? [];
        try {
            if (count($idArray) > 0) {
                foreach ($idArray as $id) {
                    $this->handleDeleteForever($id);
                }
            } else {
                $this->handleDeleteForever($id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Delete forever pages error :' . $e->getMessage());
            return response()->json([
                'status' => 2,
                'message' => __('Theme::general.error_message_catch')
            ]);
        }
        DB::commit();
        return response()->json([
            'status' => 1,
            'message' => __('Core::admin.delete_success')
        ]);
    }
    private function handleDeleteForever($id)
    {
        $checkTrashRecord = $this->pageService->findOne([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ], true);
        $data = $checkTrashRecord->toArray();
        // save log
        $this->systemLogService->saveLog(SystemLogStatusEnum::DELETE_FOREVER, $data, $this->table_name, $id);
        // remove
        $this->pageService->deleteFromWhereCondition([
            'id' => $id,
            'status' => BaseStatusEnum::DELETE
        ]);
    }

    public function show($slug)
    {
        getAndSetWithLocale(getLocale());
        $page = $this->pageService->findOne([
            'slug' => $slug,
            'status' => BaseStatusEnum::ACTIVE
        ]);
        if (!$page) {
            return redirect()->route('app.home');
        }
        return view('Page::partials.web.show', compact('page'));
    }
}
