<?php

namespace DreamTeam\Base\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use Form;
use Illuminate\Support\Facades\File;
use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Models\Setting;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Base\Services\Interfaces\CurrencyServiceInterface;
use DreamTeam\Page\Models\Page;
use DreamTeam\Page\Repositories\Interfaces\PageRepositoryInterface;

class SettingController extends AdminController
{
    protected SettingServiceInterface $settingService;
    protected PageRepositoryInterface $pageRepository;

    function __construct(
        SettingServiceInterface $settingService,
        PageRepositoryInterface $pageRepository
    ) {
        parent::__construct();
        $this->table_name = (new Setting)->getTable();
        $this->settingService = $settingService;
        $this->pageRepository = $pageRepository;
    }

    // Cấu hình tổng quan
    public function overview(Request $requests)
    {
        $setting_name   = 'overview';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.admin_menu.summary')]);
        $note           = "Translate::form.require_text";

        $nation = [0 => 'Core::admin.setting.overview.select', 1 => 'Việt Nam'];
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', __('Core::admin.admin_menu.summary'));
        $form->row();
        $form->text('name_company', $data['name_company'] ?? '', 0, __('Core::admin.setting.overview.name'), '', false, 'col-lg-6');
        $form->text('domain', $data['domain'] ?? '', 0, __('Core::admin.setting.overview.domain'), __('Core::admin.setting.overview.not_exist'), false, 'col-lg-6');
        $form->endRow();
        $form->row();
        // $form->email('email', $data['email']??'', 0, 'Email (nhận thông báo từ website)', '', false, 'col-lg-4');
        $form->text('phone', $data['phone'] ?? '', 0, __('Core::admin.setting.overview.phone'), '', false, 'col-lg-6');
        $form->text('hotline', $data['hotline'] ?? '', 0, __('Core::admin.setting.overview.hotline'), '', false, 'col-lg-6');
        $form->endRow();
        $form->row();
        $form->text('address', $data['address'] ?? '', 0, __('Core::admin.setting.overview.address'), '', false, 'col-lg-4');
        $form->select('nation', $data['nation'] ?? '', 0, __('Core::admin.setting.overview.nation'), $nation, 0, [], false, 'col-lg-4');
        $form->text('zip_code', $data['zip_code'] ?? '', 0, __('Core::admin.setting.overview.zip_code'), '', false, 'col-lg-4');
        $form->endRow();
        $form->text('description', $data['description'] ?? '', 0, __('Core::admin.setting.overview.description'), '', false);
        $form->actionInline('editconfig');
        $form->endCard();
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    // Cấu hình email
    public function email(Request $requests)
    {
        $setting_name   = 'email';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Email')]);
        $note           = "Translate::form.require_text";
        $protocol = [
            'smtp'      => 'SMTP',
        ];
        $smtp_encryption = [
            'tls' => 'TLS',
        ];
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', 'Email');
        $form->tab('', [__('Core::admin.setting.email.setting')], ['setting_email'], true);
        $form->contentTab('setting_email');
        $form->select('protocol', $data['protocol'] ?? '', 0, __('Core::admin.setting.email.protocol'), $protocol, 0);
        $form->row();
        $form->text('smtp_host', $data['smtp_host'] ?? '', 0, __('Core::admin.setting.email.smtp_host'), '', false, 'col-lg-4');
        $form->text('smtp_port', $data['smtp_port'] ?? '', 0, __('Core::admin.setting.email.smtp_port'), '', false, 'col-lg-4');
        $form->select('smtp_encryption', $data['smtp_encryption'] ?? '', 0, __('Core::admin.setting.email.smtp_encryption'), $smtp_encryption, 0, [], false, 'col-lg-4');
        $form->endRow();
        $form->row();
        $form->text('smtp_username', $data['smtp_username'] ?? '', 0, __('Core::admin.setting.email.smtp_username'), '', false, 'col-lg-4');
        $form->password('smtp_password', $data['smtp_password'] ?? '', 0, __('Core::admin.setting.email.smtp_password'), '', '', false, 'col-lg-4');
        $form->text('smtp_charset', $data['smtp_charset'] ?? 'utf-8', 0, __('Core::admin.setting.email.smtp_charset'), '', false, 'col-lg-4');
        $form->endRow();
        $form->row();
        $form->text('from_address', $data['from_address'] ?? '', 0, __('Core::admin.setting.email.from_address'), __('Core::admin.setting.email.from_address_desc'), false, 'col-lg-4');
        $form->text('from_name', $data['from_name'] ?? '', 0, __('Core::admin.setting.email.from_name'), 'VD: DreamTeam', false, 'col-lg-4');
        $form->text('smtp_email_reply_to', $data['smtp_email_reply_to'] ?? '', 0, __('Core::admin.setting.email.smtp_email_reply_to'), 'VD: info@dreamteam', false, 'col-lg-4');
        $form->endRow();
        $form->custom('Core::admin.custom.note');
        $form->title(__('Core::admin.setting.email.test'));
        $form->email('test_mail', '', 0, __('Core::admin.setting.email.test_mail'), '', false, 'col-lg-3');
        $form->custom('Core::admin.custom.btn_check_email');
        $form->endContentTab();
        $form->endTab(true);
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    public function emailContents(Request $requests)
    {
        \Asset::addDirectly([asset('vendor/core/core/base/css/setting-email.css')], 'styles', 'top')
            ->addDirectly([asset('vendor/core/core/base/js/setting-email.js')], 'scripts', 'bottom');

        $setting_name   = 'email_contents';
        $module_name    = 'Core::admin.setting.email.setting_email_content';
        $note           = "Translate::form.require_text";
        $hasLocale      = true;
        $lang = \App::getLocale();

        $settings = [];
        if (defined('FILTER_ADD_SETTING_EMAIL_CONTENT')) {
            $settings = apply_filters(FILTER_ADD_SETTING_EMAIL_CONTENT, $settings);
        }

        if (isset($requests->redirect)) {
            foreach ($settings as $module => $_) {
                $newValue = $requests->$module;
                foreach ($requests->$module as $emailName => $value) {
                    if (($value['enabled'] ?? 0) != 1) {
                        $newValue[$emailName]['enabled'] = 0;
                    }
                }
                $requests->merge([
                    $module => $newValue
                ]);
            }
            $this->settingService->postData($requests, $setting_name, true);
        }

        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, true, $requests->lang_locale ?? getLocale());

        $titles = [];
        $modules = [];
        foreach ($settings as $module => $value) {
            $modules[] = $module;
            $titles[] = $value['name'];
        }

        $form = new Form;
        $form->tab('', $titles, $modules, true);
            foreach ($settings as $module => $value) {
                $form->contentTab($module . ' hidden-tab');

                    $emailSettings = $data[$module] ?? [];
                    $form->card('col-lg-12');
                        $form->custom('Core::admin.custom.setting_email_tab', ['data' => $value['data'], 'module' => $module, 'value' => $emailSettings]);
                    $form->endCard();

                $form->endContentTab();
            }
        $form->endTab(true);
        $form->action('editconfig');

        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name',
            'hasLocale'
        ), 'Core::admin.settings.form');
    }

    // Cấu hình mã chuyển đổi
    public function code(Request $requests)
    {
        $setting_name   = 'code';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.admin_menu.code')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu
        if (!$requests->on_off_delay) {
            $requests->merge(['on_off_delay' => 0]);
        }
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', __('Core::admin.admin_menu.code'));
        $form->textarea('html_head_no_script', $data['html_head_no_script'] ?? '', 0, 'Core::admin.html_head_no_script');
        $form->textarea('html_head', $data['html_head'] ?? '', 0, 'Core::admin.html_head');
        $form->textarea('html_body', $data['html_body'] ?? '', 0, 'Core::admin.html_foot');
        $form->card('toggle-on-off-checkbox');
        $form->checkbox('on_off_delay', $data['on_off_delay'] ?? 1, 1, __('Core::admin.on_off_delay'));
        $form->note('Core::admin.note_delay');
        $form->endCard();
        $classCheck = ($data['on_off_delay'] ?? 1) == 1 ? 'on-of-checkbox-config show' : 'on-of-checkbox-config hide';
        $form->card($classCheck);
        $form->number('time_delay', $data['time_delay'] ?? '10', 0, 'Core::admin.time_delay');
        $form->endCard();
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    // 2FA
    public function googleAuthenticate(Request $requests)
    {
        $setting_name   = 'googleAuthenticate';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.admin_menu.googleAuthenticate')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
        $form->checkbox('enabled', $data['enabled'] ?? '', 1, __('Core::admin.general.turn_on'));
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    //Cấu hình link custom
    public function link_custom(Request $requests)
    {
        $setting_name = 'link_custom';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.setting.link_custom.name')]);
        $note = "Translate::form.require_text";
        // Thêm hoặc cập nhật dữ liệu
        if ($requests->isMethod('post')) {
            $timeout = 3600;
            @set_time_limit($timeout);
            @ini_set('max_execution_time', $timeout);
            @ini_set('default_socket_timeout', $timeout);
            @ini_set('memory_limit', '-1');
            $this->settingService->postData($requests, $setting_name, false);
            replaceMenuLink($requests->lang_locale ?? getLocale());
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
        $form->title('Core::admin.setting.link_custom.config_link');
        $form->custom('Core::admin.custom.custom_link', ['data' => $data]);
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'module_name',
            'note'
        ), 'Core::admin.settings.form');
    }


    // Cấu hình mã chuyển đổi
    public function custom_css(Request $requests)
    {
        \Asset::addDirectly([
            asset('vendor/core/core/base/plugins/codemirror/lib/codemirror.css'),
            asset('vendor/core/core/base/plugins/codemirror/addon/hint/show-hint.css'),
            asset('vendor/core/core/base/plugins/codemirror/custom-css.css'),
        ], 'styles', 'bottom')
            ->addDirectly([
                asset('vendor/core/core/base/plugins/codemirror/lib/codemirror.js'),
                asset('vendor/core/core/base/plugins/codemirror/lib/css.js'),
                asset('vendor/core/core/base/plugins/codemirror/addon/hint/show-hint.js'),
                asset('vendor/core/core/base/plugins/codemirror/addon/hint/anyword-hint.js'),
                asset('vendor/core/core/base/plugins/codemirror/addon/hint/css-hint.js'),
                asset('vendor/core/core/base/plugins/codemirror/custom-css.js'),
            ], 'scripts', 'bottom');

        $setting_name   = 'custom_css';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.custom_css')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu

        $files = [
            'custom_css'            => public_path('/assets/style.integration.css'),
            'custom_css_desktop'    => public_path('/assets/style.integration_desktop.css'),
            'custom_css_mobile'     => public_path('/assets/style.integration_mobile.css'),
        ];
        if (isset($requests->redirect)) {

            try {
                foreach ($files as $key => $file) {
                    File::delete($file);
                    $css = $requests->input($key);
                    $css = strip_tags((string)$css);

                    if (empty($css)) {
                        File::delete($file);
                    } else {
                        $saved = BaseHelper::saveFileData($file, $css, false);

                        if (! $saved) {
                            return redirect()->back()->withErrors(
                                __('PluginManagement::plugin.folder_is_not_writeable', ['name' => File::dirname($file)])
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
        $form = new Form;
        $form->card('col-lg-12');

        foreach ($files as $key => $file) {
            $data = '';
            if (File::exists($file)) {
                $data = BaseHelper::getFileData($file, false);
            }

            $form->custom('Core::admin.custom.custom_css', ['data' => $data, 'label' => __('Core::admin.' . $key), 'name' => $key]);
            $form->note('Using Ctrl + Space to autocomplete.');
        }

        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    // toc config
    public function toc(Request $requests)
    {
        $setting_name   = 'dreamteam_toc';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.setting.toc.title')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
        $form->card('toggle-on-off-checkbox');
        $form->checkbox('on_off', $data['on_off'] ?? 0, 1, __('Core::admin.setting.toc.on_off'));
        $form->endCard();
        $classCheck = ($data['on_off'] ?? 0) == 1 ? 'on-of-checkbox-config show' : 'on-of-checkbox-config hide';
        $form->card($classCheck);
        $form->select('position', $data['position'] ?? 1, 1, 'Core::admin.setting.toc.select_position', [
            1 => 'Core::admin.setting.toc.position_before_heading',
            2 => 'Core::admin.setting.toc.position_after_heading',
            3 => 'Core::admin.setting.toc.position_before_content',
            4 => 'Core::admin.setting.toc.position_after_content',
        ], false, [], true);
        $dataShow = [
            'blog' => 'Core::admin.setting.toc.blog',
            'page' => 'Core::admin.setting.toc.page',
        ];
        if (is_plugin_active('ecommerce')) {
            $dataShow['product'] = 'Core::admin.setting.toc.products';
        }
        if (is_plugin_active('project')) {
            $dataShow['project'] = 'Core::admin.setting.toc.project';
        }
        $form->multiCheckbox('show', $data['show'] ?? '', 1, 'Core::admin.setting.toc.select_show', $dataShow, true);
        $form->endCard();
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name'
        ), 'Core::admin.settings.form');
    }
    // reading config
    public function reading(Request $requests)
    {
        $setting_name   = 'reading';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.setting.reading.page_title')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);

        $defaultLocale = config('app.fallback_locale');
        $pages = $this->pageRepository->getAllPages($defaultLocale)->pluck('name', 'id')->toArray();
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
        $form->number('pagination_number', $data['pagination_number'] ?? 10, 0, 'Số sản phẩm hiển thị trên 1 trang', 'Số sản phẩm hiển thị trên 1 trang', true, '', false, false, "max-width: 150px");
        $form->number('asset_version', $data['asset_version'] ?? config('dreamteam_asset.version'), 0, 'Version asset', 'Version asset', true, '', false, false, "max-width: 150px");
        $form->checkbox('no_index', $data['no_index'] ?? '', 1, 'Core::admin.setting.reading.index_title');
        $form->note('Core::admin.setting.reading.index_note', true);
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name'
        ), 'Core::admin.settings.form');
    }

    // Cấu hình khác
    public function other(Request $requests)
    {
        $setting_name = 'other';
        $module_name = "Theme::admin.setting.other";
        $note = "Translate::form.require_text";
        $hasLocale = true;
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, true);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, true, $requests->lang_locale ?? getLocale());
        // Khởi tạo form
        $form = new Form;
        $form->card();
        $form->title('Theme::admin.setting.other_banner_search');
        $form->image('banner_search', $data['banner_search'] ?? '', 0, __('Core::admin.general.image'), __('Core::admin.general.pick_image'), __('Theme::admin.setting.size_image', ['size' => '1920x215']), true);
        $form->title('Theme::admin.setting.other_404');
        $form->image('404_image', $data['404_image'] ?? '', 0, __('Core::admin.general.image'), __('Core::admin.general.pick_image'), __('Theme::admin.setting.size_image', ['size' => '500x300']), true);
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'module_name',
            'note',
            'hasLocale'
        ), 'Core::admin.settings.form');
    }

    // Cấu hình ads
    public function ads(Request $requests)
    {
        $setting_name   = 'ads';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::admin.setting.ads.config')]);
        $note           = "Translate::form.require_text";

        // Thêm hoặc cập nhật dữ liệu
        $file = public_path('/ads.txt');
        if (isset($requests->redirect)) {
            try {
                File::delete(public_path('/ads.txt'));
                $ads = $requests->input('ads_content');
                $ads = strip_tags((string)$ads);

                if (empty($ads)) {
                    File::delete($file);
                } else {
                    $saved = BaseHelper::saveFileData($file, $ads, false);

                    if (! $saved) {
                        return redirect()->back()->withErrors(
                            __('PluginManagement::plugin.folder_is_not_writeable', ['name' => File::dirname($file)])
                        );
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
        // Lấy dữ liệu ra
        if (File::exists($file)) {
            $data = BaseHelper::getFileData($file, false);
        } else {
            $data = '';
        }
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
        $form->textarea('ads_content', $data, 0, 'Core::admin.setting.ads.content', 'Core::admin.setting.ads.content', 15, false);
        $form->note(__('Core::admin.setting.ads.note'));
        $form->endCard();
        $form->action('editconfig', '/ads.txt');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    // Cấu hình hiển thị icon
    public function callToAction(Request $requests)
    {
        $setting_name   = 'call_to_action';
        $module_name    = "Theme::admin.setting.call-to-action";
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Theme::admin.setting.call-to-action')]);
        $note           = "Translate::form.require_text";
        $hasLocale      = true;

        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, true);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, true, $requests->lang_locale ?? getLocale());
        $listContacts = [
            'facebook'  =>  __('Facebook'),
            'zalo'      =>  __('Zalo'),
            'phone'     =>  __('Core::admin.setting.overview.phone'),
            'whatsApp'  =>  __('WhatsApp'),
            'viber'     =>  __('Viber'),
            'instagram' =>  __('Instagram'),
            'printest'  =>  __('Printest'),
            'linked'    =>  __('Linked'),
            'messenger'  => __('Messenger'),
        ];
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', '');
        foreach ($listContacts as $key => $value) {
            $form->card();
            $form->custom('Form::custom.form_custom', [
                'has_full' => true,
                'name' => 'list_' . $key,
                'value' => $data['list_' . $key] ?? [],
                'label' => $value ?? '',
                'generate' => [
                    ['type' => 'text', 'name' => 'link', 'placeholder' => __('Core::admin.general.slug')],
                    ['type' => 'text', 'name' => 'desc', 'placeholder' => __('Core::admin.general.description')],
                    ['type' => 'text', 'name' => 'gtag', 'placeholder' => __('Core::google_conversion.cta')],
                ],
            ]);
            $form->endCard();
        }
        $form->card();
        $form->radio('toggle_icon', $data['toggle_icon'] ?? 1, 'Core::admin.general.turn_on', [1 => __('Core::admin.general.yes'), 2 => __('Core::admin.general.no')], 'col-lg-12', true);
        $form->radio('type_show', $data['type_show'] ?? 1, 'Core::admin.general.display_type', [1 => 'icon', 2 => __('Core::admin.general.icon_and_text'), 3 => __('Core::admin.general.icon_general')], 'col-lg-12', true);
        $form->radio('position_icon', $data['position_icon'] ?? 1, 'Core::admin.general.position', [1 => __('Core::admin.general.left'), 2 => __('Core::admin.general.right')], 'col-lg-12', true);
        $form->text('support_title', $data['support_title'] ?? '', 0, __('Theme::general.support_title'), '', false);
        $form->endCard();
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name',
            'hasLocale',
            'data'
        ), 'Core::admin.settings.form');
    }
    public function currency(Request $requests, CurrencyServiceInterface $service)
    {
        \Asset::addDirectly([
            asset('vendor/core/core/base/js/currency.js'),
        ], 'scripts', 'bottom', ['defer' => ''])
            ->addDirectly([
                asset('vendor/core/core/base/css/currency.css'),
            ], 'styles', 'top');
        $setting_name   = 'currency';
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::currency.currencies')]);
        $note           = "Translate::form.require_text";

        if (isset($requests->redirect)) {
            $currencies = json_decode($requests->input('currencies'), true) ?: [];
            $deletedCurrencies = json_decode($requests->input('deleted_currencies', []), true) ?: [];
            $requests->request->remove('currencies');
            $requests->request->remove('deleted_currencies');
            $this->settingService->postData($requests, $setting_name, false);

            if (! $currencies) {
                return redirect()->back()->withErrors(trans('Base::currency.require_at_least_one_currency'));
            }

            $storedCurrencies = $service->execute($currencies, $deletedCurrencies);

            if ($storedCurrencies['error']) {
                return redirect()->back()->withErrors($storedCurrencies['message']);
            }
        }
        $currencies = $service->search([])->toArray();
        $defaultLocale = config('app.fallback_locale');
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        $form = new Form;
        $form->row();
        $form->col('col-lg-4');
        $form->title('Core::currency.currencies');
        $form->note(__('Core::currency.setting_description'));
        $form->endCol();
        $form->col('col-lg-8');
        $radioConfirmOptions = [1 => 'Core::currency.yes', 0 => 'Core::currency.no'];
        $form->card('', '');
        $form->text('currency_default_text', $data['currency_default_text'] ?? '', 0, __('Core::currency.currency_default_text'), __('Core::currency.currency_default_placeholder'));
        $form->radio('enable_auto_detect_visitor_currency', $data['enable_auto_detect_visitor_currency'] ?? 1, 'Core::currency.enable_auto_detect_visitor_currency', $radioConfirmOptions, 'col-lg-12', true);
        $form->radio('add_space_between_price_and_currency', $data['add_space_between_price_and_currency'] ?? 1, 'Core::currency.add_space_between_price_and_currency', $radioConfirmOptions, 'col-lg-12', true);
        $form->select('thousands_separator', $data['thousands_separator'] ?? ',', 1, __('Core::currency.thousands_separator'), [',' => __('Core::currency.separator_comma'), '.' => __('Core::currency.separator_period'), 'space' => __('Core::currency.separator_space')]);
        $form->select('decimal_separator', $data['decimal_separator'] ?? ',', 1, __('Core::currency.decimal_separator'), ['.' => __('Core::currency.separator_period'), ',' => __('Core::currency.separator_comma'), 'space' => __('Core::currency.separator_space')]);
        $form->custom('Core::admin.settings.currency', compact('currencies'));
        $form->note(__('Core::currency.instruction'));
        $form->endCard();
        $form->endCol();
        $form->endRow();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note',
            'module_name',
            'setting_name'
        ), 'Core::admin.settings.form');
    }

    public function tracking(Request $requests)
    {
        $module_name    = trans('Core::tables.title_setting', ['name' => trans('Core::google_conversion.cta_conversion')]);
        $note           = "Translate::form.require_text";
        $breadcrumbs= [
            [
                'name' => $module_name
            ]];
        // Hiển thị form tại view
        if ($requests->get('google_conversion', null)) {
            $this->settingService->postData($requests, $requests->get('google_conversion'), false);
        }
        return view('Core::admin.settings.google-conversion', compact('module_name', 'note', 'breadcrumbs'));
    }

    public function groupInterface(Request $requests)
    {
        \Asset::addDirectly([
            asset('/vendor/core/core/base/css/setting-group.css')
        ], 'styles');
        $menuConfigs = admin_menu()->getMenuSetting('group_interface');
        $module_name    = trans('Core::admin.admin_menu.interface_2');
        $breadcrumbs= [
            [
                'name' => $module_name
            ]];
        return view('Core::admin.settings.group-setting', compact('menuConfigs', 'breadcrumbs'));
    }


    public function groupConfig(Request $requests)
    {
        \Asset::addDirectly([
            asset('/vendor/core/core/base/css/setting-group.css')
        ], 'styles');
        $menuConfigs = admin_menu()->getMenuSetting('group_setting');
        $module_name    = trans('Core::admin.admin_menu.config');
        $breadcrumbs= [
            [
                'name' => $module_name
            ]];
        return view('Core::admin.settings.group-setting', compact('menuConfigs', 'breadcrumbs'));
    }

    // Cấu hình chung
    public function general(Request $requests)
    {
        $settingName = 'general';
        $module_name = 'Cài đặt chung';
        $note = "Translate::form.require_text";
        $hasLocale = true;
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            if(!isset($requests->toggle_header_top)) {
                $requests->merge(["toggle_header_top"=> 0]);  
            }
            $this->settingService->postData($requests, $settingName, true);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($settingName, true, $requests->lang_locale ?? getLocale());
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
            $form->tab('', ['Cài đặt trang tìm kiếm', __('Footer'), __('Mạng xã hội'),], ['search_page', 'config_footer', 'config_social'], true);
                $form->contentTab('config_footer');
                    $form->textarea('copyright', $data['copyright'] ?? '', 0, 'Copyright', '', 5, true);
                $form->endContentTab();
                $form->contentTab('search_page');
                    $form->title('Cài đặt trang tìm kiếm chiều Sài gòn - Vũng tàu');
                    $form->text('sg_vt_title', $data['sg_vt_title'] ?? '', 0, 'Meta title','', true);
                    $form->textarea('sg_vt_description', $data['sg_vt_description'] ?? '', 0, 'Meta description', '', 5, true);
                    $form->title('Cài đặt trang tìm kiếm chiều Vũng tàu - Sài gòn');
                    $form->text('vt_sg_title', $data['vt_sg_title'] ?? '', 0, 'Meta title','', true);
                    $form->textarea('vt_sg_description', $data['vt_sg_description'] ?? '', 0, 'Meta description', '', 5, true);
                $form->endContentTab();
                $form->contentTab('config_social');
                    $form->text('phone', $data['phone'] ?? '', 0, 'Hotline','', true);
                    $form->text('email', $data['email'] ?? '', 0, __('Email'),'', true);
                    $form->text('facebook', $data['facebook'] ?? '', 0, 'Facebook','', true);
                    $form->text('twitter', $data['twitter'] ?? '', 0, 'Twitter','', true);
                    $form->text('pinterest', $data['pinterest'] ?? '', 0, 'Pinterest','', true);
                    $form->text('instagram', $data['instagram'] ?? '', 0, 'Instagram','', true);
                $form->endContentTab();
            $form->endTab();
                
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note','module_name', 'hasLocale'
        ), 'Core::admin.settings.form');
    }
    // setting style
    public function themeConfig(Request $requests)
    {
        $settingName = 'theme_config';
        $module_name = "Core::admin.theme_config";
        $note = "Translate::form.require_text";
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $settingName, false);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($settingName, false);
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
            $form->tab('', [__('Header'), __('Footer')], ['config_header', 'config_footer'], true);
                $form->contentTab('config_header');
                    $form->title(__('Logo'));
                        $form->image('favicon', $data['favicon']??'', 0, 'Favicon', __('Core::admin.general.pick_image'),'', true);
                        $form->image('logo_header_desktop', $data['logo_header_desktop']??'', 0, __('Logo header desktop'), __('Core::admin.general.pick_image'), __('Core::admin.choose_image_size', ['size' => __('150x50')]), true);
                        $form->image('logo_header_mobile', $data['logo_header_mobile']??'', 0, __('Logo header mobile'), __('Core::admin.general.pick_image'), '', true);
                $form->endContentTab();
                $form->contentTab('config_footer');
                    $form->image('logo_footer', $data['logo_footer']??'', 0, __('Logo Footer'), __('Core::admin.general.pick_image'), '', true);
                $form->endContentTab();
            $form->endTab();
                
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note','module_name'
        ), 'Core::admin.settings.form');
    }

        // Cấu hình trang chủ
        public function home(Request $requests)
        {
            $settingName = 'home';
            $module_name = "Cài đặt trang chủ";
            $note = "Translate::form.require_text";
            $hasLocale = true;
            // Thêm hoặc cập nhật dữ liệu
            if (isset($requests->redirect)) {
                $this->settingService->postData($requests, $settingName, true);
            }
            // Lấy dữ liệu ra
            $data = $this->settingService->getData($settingName, true, $requests->lang_locale ?? getLocale());
            // Khởi tạo form
            $form = new Form;
            $form->card();
                $form->image('banner', $data['banner'] ?? '', 0, __('Hình ảnh bannrer'), __('Core::admin.general.pick_image'), __('Core::admin.choose_image_size', ['size' => '1920x640']), true);
            $form->endCard();
            $form->custom('Form::metaseo', ['homePageSeo' => $data]);
            $lang = $requests->lang_locale ?? getLocale();
            $form->action('editconfig', route('app.home.' . $lang));
            // Hiển thị form tại view
            return $form->render('custom', compact(
                'module_name', 'note', 'hasLocale'
            ), 'Core::admin.settings.form');
        }

}
