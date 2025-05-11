<?php

namespace DreamTeam\Ecommerce\Http\Controllers;
use DreamTeam\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use DB;
use Form;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;
use DreamTeam\Base\Services\Interfaces\CurrencyServiceInterface;
use DreamTeam\Page\Repositories\Interfaces\PageRepositoryInterface;
use DreamTeam\Base\Supports\SettingStore;
use DreamTeam\Ecommerce\Facades\EcommerceHelper;
use DreamTeam\Base\Models\Setting;
use DreamTeam\Base\Services\Interfaces\SettingServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\PluginManagement\Events\ClearCacheEvent;

class SettingController extends AdminController
{
    protected SettingServiceInterface $settingService;
    protected PageRepositoryInterface $pageRepository;

    function __construct(
        SettingServiceInterface $settingService,
        PageRepositoryInterface $pageRepository
    )
    {
        parent::__construct();
        $this->table_name = (new Setting)->getTable();
        $this->settingService = $settingService;
        $this->pageRepository = $pageRepository;
    }

    public function advanced(Request $requests)
    {
        $setting_name   = 'ec_advanced';
        $module_name    = "Ecommerce::admin.setting_advanced";
        $note           = "Translate::form.require_text";
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, false);
        }
        $defaultLocale = config('app.fallback_locale');
        $pages = $this->pageRepository->getAllPages($defaultLocale)->pluck('name', 'id')->toArray();
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, false);
        $form = new Form;
            $form->row();
                $form->col('col-lg-4');
                    $form->title('Ecommerce::admin.setting_advanced');
                    $form->note(__('Ecommerce::admin.advanced.cart_note'));
                $form->endCol();
                $form->col('col-lg-8');
                    $radioConfirmOptions = [1 => 'Ecommerce::admin.advanced.yes', 0 => 'Ecommerce::admin.advanced.no'];
                    $form->card('', '');
                        $form->radio('shopping_cart_enabled', $data['shopping_cart_enabled'] ?? 1, 'Ecommerce::admin.advanced.enable_cart', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('wishlist_enabled', $data['wishlist_enabled'] ?? 1, 'Ecommerce::admin.advanced.enable_wishlist', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('compare_enabled', $data['compare_enabled'] ?? 1, 'Ecommerce::admin.advanced.enable_compare', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('show_sidebar', $data['show_sidebar'] ?? 0, 'Widget::widget.show_sidebar', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('enable_quick_buy_button', $data['enable_quick_buy_button'] ?? 1, 'Ecommerce::admin.advanced.enable_buy_now', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('quick_buy_target_page', $data['quick_buy_target_page'] ?? 'checkout', 'Ecommerce::admin.advanced.target_buy_now', ['checkout' => 'Ecommerce::admin.advanced.checkout_page', 'cart' => 'Ecommerce::admin.advanced.cart_page'], 'col-lg-12', true);
                        $form->radio('question_answer_enabled', $data['question_answer_enabled'] ?? 0, 'Ecommerce::admin.advanced.question_answer_enabled', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('review_enabled', $data['review_enabled'] ?? 1, 'Ecommerce::admin.advanced.enable_review', $radioConfirmOptions, 'col-lg-12', true);  
                        $form->radio('show_schema', $data['show_schema'] ?? 1, 'Ecommerce::admin.advanced.show_schema', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('advistory_enabled', $data['advistory_enabled'] ?? 0, 'Ecommerce::admin.advanced.advistory_enabled', $radioConfirmOptions, 'col-lg-12', true);
                        if(!is_plugin_active('form-custom')) {
                            $form->note(__('Ecommerce::admin.advanced.advistory_no_form'));
                        } else {
                            $forms = app(\DreamTeam\FormCustom\Services\Interfaces\FormCustomServiceInterface::class)
                                ->getMultipleWithFromConditions([], ['status' => BaseStatusEnum::ACTIVE], 'id', 'desc', true, $defaultLocale);
                            $form->custom('Ecommerce::admin.settings.advanced-advistory', compact('data', 'forms'));
                        }
                        $form->radio('show_address_field_at_the_checkout_optional', $data['show_address_field_at_the_checkout_optional'] ?? 1, 'Ecommerce::admin.advanced.enable_address_payment', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('make_email_field_at_the_checkout_optional', $data['make_email_field_at_the_checkout_optional'] ?? 0, 'Ecommerce::admin.advanced.enable_require_email', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('make_phone_field_at_the_checkout_optional', $data['make_phone_field_at_the_checkout_optional'] ?? 0, 'Ecommerce::admin.advanced.enable_require_phone', $radioConfirmOptions, 'col-lg-12', true);
                        $form->radio('make_captcha_field_at_the_checkout', $data['make_captcha_field_at_the_checkout'] ?? 0, 'Ecommerce::admin.advanced.enable_captcha_payment', $radioConfirmOptions, 'col-lg-12', true);
                        if(!is_plugin_active('form-custom')) {
                            $form->note(__('Ecommerce::admin.advanced.captcha_setup'));
                        } else {
                            $form->note(__('Ecommerce::admin.advanced.captcha_note', ['route' => route('admin.settings.recaptcha')]));
                        }
                        $form->radio('enable_guest_checkout', $data['enable_guest_checkout'] ?? 1, 'Ecommerce::admin.advanced.enable_checkout_guest', $radioConfirmOptions, 'col-lg-12', true);
                        $form->select('shop_page_id', $data['shop_page_id'] ?? '', 0, 'Ecommerce::admin.advanced.select_shop_page', ['' => '---Select---'] + $pages);
                        $form->number('filter_price_max', $data['filter_price_max'] ?? 0, 0, __('Ecommerce::admin.price_max'));
                        $resizes = ['original' => 'Original', 'mediumlarge' => 'Width 420px', 'large' => 'Width 600px', 'extralarge' => 'Width 900px'];
                        $form->select('size_single_mobile', $data['size_single_mobile'] ?? 'mediumlarge', 0, 'Ecommerce::admin.advanced.size_single_mobile', $resizes);
                        $form->select('size_single_desktop', $data['size_single_desktop'] ?? 'large', 0, 'Ecommerce::admin.advanced.size_single_desktop', $resizes);
                    $form->endCard();
                $form->endCol();

            $form->endRow();
            
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note', 'module_name', 'setting_name'
        ), 'Core::admin.settings.form');
    }

    public function summary_product(Request $requests)
    {
        $setting_name   = 'summary_product';
        $module_name    = __("Ecommerce::admin.setting_summary_product");
        $note           = "Translate::form.require_text";
        $hasLocale      = true;
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, true);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, true, $requests->lang_locale ?? getLocale());
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12');
            $tabTitle = [];
            $tabName = [];
            if(!defined('ACTION_DO_NOT_SHOW_PRODUCT_SINGLE_CONFIG')) {
                $tabTitle[] = __('Ecommerce::admin.single_page');
                $tabName[] = 'single_page';
            }
            $form->tab('', $tabTitle, $tabName, true);
                if(!defined('ACTION_DO_NOT_SHOW_PRODUCT_SINGLE_CONFIG')) {
                    $form->contentTab('single_page');
                        $form->title(__('Ecommerce::admin.form_chat_user'));

                            $form->note(__('Ecommerce::admin.preview', ['link' => asset('/vendor/core/core/base/img/product-detail-setting-preview.jpg')]));
                            $form->image('chat_avatar', $data['chat_avatar'] ?? '', 0, 'Ecommerce::admin.image_thumnail', '', __('Ecommerce::admin.choose_image_size', ['size' => '100x100']));
                            $form->text('chat_title', $data['chat_title'] ?? '', 0, 'Ecommerce::admin.title');
                            $form->text('chat_phone', $data['chat_phone'] ?? '', 0, 'Ecommerce::admin.phone');
                            $form->text('chat_desc', $data['chat_desc'] ?? '', 0, __('Ecommerce::admin.description'));
                        $form->title(__('Ecommerce::admin.addition_info'));
                        $form->custom('Form::custom.form_custom', [
                            'has_full' => true,
                            'name' => 'list',
                            'value' => $data['list'] ?? [],
                            'label' => 'Core::admin.general.list',
                            'generate' => [
                                [ 'type' => 'image', 'name' => 'image', 'size' => __('Ecommerce::admin.choose_image_size', ['size' => '80x80']), ],
                                [ 'type' => 'custom', 'generate' => [
                                        [ 'type' => 'text', 'name' => 'title', 'placeholder' => 'Ecommerce::admin.title', ],
                                        [ 'type' => 'text', 'name' => 'desc', 'placeholder' => __('Ecommerce::admin.description'), ],
                                    ]
                                ],
                            ],
                        ]);
                    $form->endContentTab();
                }
            $form->endTab();
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note', 'module_name', 'setting_name', 'hasLocale'
        ), 'Core::admin.settings.form');
    }

    public function shippingMethod(Request $requests)
    {
        \Asset::addDirectly([
            asset('/vendor/core/Ecommerce/build/css/admin/shipping.min.css')
        ], 'styles')
        ->addDirectly([
            asset('/vendor/core/Ecommerce/build/js/admin/shipping.min.js')
        ], 'scripts', 'bottom');
        $setting_name   = 'ec_shipping_method';
        $module_name    = __("Ecommerce::product.payment.shipping_method");
        $note           = "Translate::form.require_text";
        $methodKey = $requests->methodKey ?? '';
        if(!empty($methodKey) && array_key_exists($methodKey, EcommerceHelper::getAllShippingMethods())) {
            return view('Ecommerce::admin.settings.shipping.method-setting', compact('methodKey'));            
        }
        // Hiển thị form tại view
        return view('Ecommerce::admin.settings.shipping.config', compact('setting_name', 'module_name', 'note'));
    }

    public function updateMethods(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        $type = $request->input('type');
        $data = $request->except(['_token', 'type']);
        foreach ($data as $settingKey => $settingValue) {
            $settingStore
                ->set($settingKey, $settingValue);
        }
        $defaultShippingMethod = $request->default_shipping_method ?? 0;
        if($defaultShippingMethod == 1) {
            $settingStore
                ->set('default_shipping_method', $type)
                ->save();
        } elseif($settingStore->get('default_shipping_method') == $type) {
            $settingStore
                ->set('default_shipping_method', '')
                ->save();
        }
        $settingStore
            ->set('shipping_' . $type . '_status', 1)
            ->save();
        event(new ClearCacheEvent());
        return $response->setMessage(trans('Ecommerce::order.shipping.saved_shipping_method_success'));
    }

    public function updateMethodStatus(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        if($settingStore->get('default_shipping_method') == $request->input('type')) {
            $settingStore
                ->set('default_shipping_method', '')
                ->save();
        }
        
        $settingStore
            ->set('shipping_' . $request->input('type') . '_status', 0)
            ->save();

        return $response->setMessage(trans('Ecommerce::order.shipping.turn_off_success'));
    }


    public function interface_email_ecommerce(Request $requests)
    {
        $setting_name   = 'interface_email_ecommerce';
        $module_name    = 'Ecommerce::admin.setting_email_content';
        $note           = "Translate::form.require_text";
        $hasLocale      = true;
        // Thêm hoặc cập nhật dữ liệu
        if (isset($requests->redirect)) {
            $this->settingService->postData($requests, $setting_name, true);
        }
        // Lấy dữ liệu ra
        $data = $this->settingService->getData($setting_name, true, $requests->lang_locale ?? getLocale());
        // Khởi tạo form
        $form = new Form;
        $form->card('col-lg-12', 'Ecommerce::admin.setting_email_content');
            $form->tab('', [__('Ecommerce::admin.email_payment_success')], ['email_payment_success'], true);
                $form->contentTab('email_payment_success');
                    $form->title(__('Ecommerce::admin.usable_variables'));
                    $form->custom('Core::admin.custom.param_email', [
                        'param' => [
                            'name'=>__('Ecommerce::order.customer_name'), 
                            'orderCode' => __('Ecommerce::order.order_code'),
                            'amount' => __('Ecommerce::order.order_total'),
                            'paymentChanel' => __('Ecommerce::order.payment_method'),
                        ]
                    ]);
                    $form->text('title_email_payment_success', $data['title_email_payment_success'] ?? 'Confirmation of successful payment for order {orderCode}', 0, __('Ecommerce::admin.title_mail'));
                    $form->editor('content_email_payment_success', $data['content_email_payment_success']?? EcommerceHelper::getDefaultEmailPaidSuccess(), 0, __('Ecommerce::admin.content_email_active'), '');
                $form->endContentTab();
            $form->endTab(true);
        $form->endCard();
        $form->action('editconfig');
        // Hiển thị form tại view
        return $form->render('custom', compact(
            'note', 'module_name', 'setting_name', 'hasLocale'
        ), 'Core::admin.settings.form');
    }

    private function getTemplateActive() {
        return '
            <table style="background-color: #f4f6f7; border: 1px solid #eee; width: 100%;" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td>
                <div style="background-color: #fff; border: 1px solid #DEE6E9; border-radius: 10px; box-sizing: border-box; font-family: Lato, Helvetica, Arial, sans-serif; margin: auto; max-width: 600px; overflow: hidden; width: 600px;">
                <div style="background-color: #0e273b; padding: 40px; text-align: center; background-repeat: no-repeat; background-position: calc( 100% - 20px ) 20px; background-size: 50px;">
                <h2 style="color: #fff; font-size: 24px; font-weight: normal; margin: 0;" id="mcetoc_1g35cplqh0">'.__('Ecommerce::admin.dreamteam_title_mail').'</h2>
                </div>
                <div style="padding: 40px 50px; background-repeat: no-repeat; background-position: top; background-size: contain;">
                <p style="font-size: 14px; margin: 0; margin-bottom: 25px;">Xin chào,</p>
                <p style="font-size: 16px; margin: 0; margin-bottom: 35px; line-height: 22px;">'.__('Ecommerce::admin.verification_email').'<strong> '.__('Ecommerce::admin.security_code').'</strong></p>
                <div style="text-align: center;">
                <div style="background-color: #25586b0d; border-radius: 6px; color: #0e273b; display: inline-block; font-size: 30px; padding: 20px 30px;">{OTP}</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: center; margin-top: 15px;">
                <div style=" background-repeat: no-repeat; background-size: contain; height: 14px; width: 14px;"></div>
                </div>
                <p style="font-size: 14px; margin: 35px 0; line-height: 22px;">'.__('Ecommerce::admin.skip_email').'</p>
                <p style="font-size: 14px; margin: 35px 0; line-height: 22px;">'.__('Ecommerce::admin.contact_support_dreamteam').'</p>
                <p style="font-size: 14px; margin: 0; line-height: 22px;">'.__('Ecommerce::admin.thanks').'</p>
                <p style="font-size: 14px; margin: 0; line-height: 22px;">'.__('Ecommerce::admin.dreamteam_group').'</p>
                </div>
                </div>
                </td>
                </tr>
                </tbody>
                </table>
                <p></p>
        ';
    }

    private function getTemplateForgotPassword() {
        return '
                <div style="padding: 40px 50px; background-repeat: no-repeat; background-position: top; background-size: contain;">
                <p style="font-size: 14px; margin: 0; margin-bottom: 25px;">'.__('Ecommerce::admin.click_update_password').' <a href="{link}" style="background-color: #002444; border-radius: 6px; color: #fff; display: inline-block; font-size: 18px; padding: 6px 20px;">{link}</a></p>
                </div>
                <p></p>
        ';
    }

}
