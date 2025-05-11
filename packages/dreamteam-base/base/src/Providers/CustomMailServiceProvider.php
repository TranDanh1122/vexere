<?php

namespace DreamTeam\Base\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CustomMailServiceProvider extends ServiceProvider
{
    function boot()
    {
        $this->app->booted(function () {
            // Kiểm tra bảng đã tồn tại hay chưa
            if (Schema::hasTable('settings')) {
                // Lấy dữ liệu cấu hình mail ra
                $option = Cache::rememberForever('setting_mail_config', function () {
                    return DB::table('settings')->select('value')->where('key', 'email')->first();
                });
            }
            $data = [];
            if (isset($option) && !empty($option)) {
                $data = json_decode(base64_decode($option->value), true);
            }
            // Đưa mảng về các biến có tên là các key của mảng
            extract($data, EXTR_OVERWRITE);

            // Nếu có giá trị protocol
            if (isset($protocol) && !empty($protocol)) {
                // Đặt lại config
                config([
                    'mail.from.address'     => $from_address ?? config('mail.form.address') ?? '',
                    'mail.from.name'        => $from_name ?? config('mail.form.name') ?? '',
                ]);
                switch ($protocol) {
                    case 'smtp':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.smtp.transport'       => $protocol ?? config('mail.mailers.smtp.transport') ?? '',
                            'mail.mailers.smtp.host'            => $smtp_host ?? config('mail.mailers.smtp.host') ?? '',
                            'mail.mailers.smtp.port'            => $smtp_port ?? config('mail.mailers.smtp.port') ?? '',
                            'mail.mailers.smtp.encryption'      => $smtp_encryption ?? config('mail.mailers.smtp.encryption') ?? '',
                            'mail.mailers.smtp.username'        => $smtp_username ?? config('mail.mailers.smtp.username') ?? '',
                            'mail.mailers.smtp.password'        => $smtp_password ?? config('mail.mailers.smtp.password') ?? '',
                        ]);
                        break;
                    case 'ses':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.ses.transport'        => $protocol ?? config('mail.mailers.ses.transport') ?? '',
                            'mail.services.ses.key'             => $ses_key ?? config('mail.services.ses.key') ?? '',
                            'mail.services.ses.secret'          => $ses_secret ?? config('mail.services.ses.secret') ?? '',
                            'mail.services.ses.region'          => $ses_region ?? config('mail.services.ses.region') ?? '',
                        ]);
                        break;
                    case 'mailgun':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.mailgun.transport'    => $protocol ?? config('mail.mailers.mailgun.transport') ?? '',
                            'mail.services.mailgun.domain'      => $mailgun_domain ?? config('mail.services.mailgun.domain') ?? '',
                            'mail.services.mailgun.secret'      => $mailgun_secret ?? config('mail.services.mailgun.secret') ?? '',
                            'mail.services.mailgun.endpoint'    => $mailgun_endpoint ?? config('mail.services.mailgun.endpoint') ?? '',
                        ]);
                        break;
                    case 'postmark':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.postmark.transport'   => $protocol ?? config('mail.mailers.postmark.transport') ?? '',
                            'mail.services.postmark.token'      => $postmark_token ?? config('mail.services.postmark.token') ?? '',
                        ]);
                        break;
                    case 'sendmail':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.sendmail.transport'   => $protocol ?? config('mail.mailers.sendmail.transport') ?? '',
                            'mail.mailers.sendmail.path'        => $sendmail_path ?? config('mail.mailers.sendmail.path') ?? '',
                        ]);
                        break;
                    case 'log':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.log.transport'        => $protocol ?? config('mail.mailers.log.transport') ?? '',
                            'mail.mailers.log.channel'          => $log_channel ?? config('mail.mailers.log.channel') ?? '',
                        ]);
                        break;
                    case 'array':
                        config([
                            'mail.default'                      => $protocol ?? config('mail.default') ?? '',
                            'mail.mailers.array.transport'      => $protocol ?? config('mail.mailers.array.transport') ?? '',
                        ]);
                        break;
                }
                // Set lại transport
                (new \Illuminate\Mail\MailServiceProvider(app()))->register();
            }
        });
    }
}
