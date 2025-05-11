<?php

namespace DreamTeam\Base\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use DreamTeam\Base\Models\Setting;

class MailConfigController extends AdminController
{

	function __construct()
    {
        parent::__construct();
        $this->table_name = (new Setting)->getTable();
	}

	public function testMail(Request $requests) {
		// Đưa mảng về các biến có tên là các key của mảng
		extract($requests->all(),EXTR_OVERWRITE);
		// Mở check lỗi trên server
		\Barryvdh\Debugbar\Facade::enable();
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
        }
        // Set lại transport
        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
        
        try {
            if (verifyEmailOrg($email)) {
                // 
                \Mail::to($email)->send(new \DreamTeam\Base\Mail\MailTest($email));
                return [
					'statis' => 1,
					'message' => __('Core::admin.setting.email.success'),
				];
            } return [
                'status' => 2,
                'message' => __('Core::admin.general.not_exists', ['name' => 'Email']),
            ];
        } catch (Exception $e) {
            return [
                'status' => 2,
                'message' => __('Core::admin.setting.email.error'),
                'error' => $e->message(),
            ];
        }
		
	}
}
