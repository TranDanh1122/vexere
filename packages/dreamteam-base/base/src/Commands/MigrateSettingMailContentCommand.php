<?php

namespace DreamTeam\Base\Commands;

use Illuminate\Console\Command;

class MigrateSettingMailContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:migrate-setting-mail-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chuyển setting mail content cũ sang setting base';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settingPluginUser = \DB::table('settings')->where('key', 'setting_user_base')->get();
        $settings = [
            'vi' => [
                'plugin_user' => [],
                'plugin_ecommerce' => [],
                'service_booking' => [],
            ],
            'en' => [
                'plugin_user' => [],
                'plugin_ecommerce' => [],
                'service_booking' => [],
            ],
            'fr' => [
                'plugin_user' => [],
                'plugin_ecommerce' => [],
                'service_booking' => [],
            ],
            'de' => [
                'plugin_user' => [],
                'plugin_ecommerce' => [],
                'service_booking' => [],
            ]
        ];
        foreach ($settingPluginUser as $setting) {
            if (!$setting->value) {
                continue;
            }

            $valueDecoded = json_decode(base64_decode($setting->value), true);

            if (isset($valueDecoded['title_email_active']) && isset($valueDecoded['content_email_active'])) {
                $settings[$setting->locale]['plugin_user']['send_otp'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_active'],
                    'content' => $valueDecoded['content_email_active']
                ];
            }

            if (isset($valueDecoded['title_email_forgot_password']) && isset($valueDecoded['content_email_forgot_password'])) {
                $settings[$setting->locale]['plugin_user']['forget_password'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_forgot_password'],
                    'content' => $valueDecoded['content_email_forgot_password']
                ];
            }
        }

        $settingPluginEcommerce = \DB::table('settings')->where('key', 'interface_email_ecommerce')->get();
        foreach ($settingPluginEcommerce as $setting) {
            if (!$setting->value) {
                continue;
            }

            $valueDecoded = json_decode(base64_decode($setting->value), true);

            if (isset($valueDecoded['title_email_payment_success']) && isset($valueDecoded['content_email_payment_success'])) {
                $settings[$setting->locale]['plugin_ecommerce']['payment_success'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_payment_success'],
                    'content' => $valueDecoded['content_email_payment_success']
                ];
            }
        }

        $settingPluginServiceBooking = \DB::table('settings')->where('key', 'setting_service_booking_email')->get();
        foreach ($settingPluginServiceBooking as $setting) {
            if (!$setting->value) {
                continue;
            }

            $valueDecoded = json_decode(base64_decode($setting->value), true);

            if (isset($valueDecoded['title_email_payment_success']) && isset($valueDecoded['content_email_payment_success'])) {
                $settings[$setting->locale]['plugin_ecommerce']['payment_success'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_payment_success'],
                    'content' => $valueDecoded['content_email_payment_success']
                ];
            }

            if (isset($valueDecoded['title_email_booking_accept']) && isset($valueDecoded['content_email_booking_accept'])) {
                $settings[$setting->locale]['plugin_ecommerce']['booking_accept'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_booking_accept'],
                    'content' => $valueDecoded['content_email_booking_accept']
                ];
            }

            if (isset($valueDecoded['title_email_booking_success']) && isset($valueDecoded['content_email_booking_success'])) {
                $settings[$setting->locale]['plugin_ecommerce']['booking_success'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_booking_success'],
                    'content' => $valueDecoded['content_email_booking_success']
                ];
            }

            if (isset($valueDecoded['title_email_booking_denied']) && isset($valueDecoded['content_email_booking_denied'])) {
                $settings[$setting->locale]['plugin_ecommerce']['booking_denied'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_booking_denied'],
                    'content' => $valueDecoded['content_email_booking_denied']
                ];
            }

            if (isset($valueDecoded['title_email_reminder_booking']) && isset($valueDecoded['content_email_reminder_booking'])) {
                $settings[$setting->locale]['plugin_ecommerce']['reminder_booking'] = [
                    'enabled' => 1,
                    'title' => $valueDecoded['title_email_reminder_booking'],
                    'content' => $valueDecoded['content_email_reminder_booking']
                ];
            }
        }

        $dataInsert = [];
        foreach ($settings as $lang => $data) {
            foreach ($data as $key => $value) {
                if (!count($value)) {
                    unset($data[$key]);
                }
            }
            if (count($data)) {
                $dataInsert[] = [
                    'key' => 'email_contents',
                    'locale' => $lang,
                    'value' => base64_encode(json_encode($data))
                ];
            }
        }
        \DB::table('settings')->insert($dataInsert);

        $this->info('Done');
    }
}
