<?php

namespace DreamTeam\Base\Listeners;

use DreamTeam\Base\Events\SendMailTemplateEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailTemplateListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendMailTemplateEvent $event): void
    {
        $module = $event->module;
        $emailName = $event->emailName;
        $mailTo = $event->mailTo;
        $replaces = $event->replaces;

        $mailConfig = getEmailContent($module, $emailName);
        if ($mailConfig === null) {
            $settings = apply_filters(FILTER_ADD_SETTING_EMAIL_CONTENT, []);

            $mailConfig = $settings[$module]['data'][$emailName] ?? null;
            if ($mailConfig === null) {
                Log::error('Không tìm thấy cấu hình email template');
                return;
            }

            $mailConfig = [
                'enabled' => $mailConfig['default_enabled'] ?? 1,
                'title' => $mailConfig['default_title'],
                'content' => $mailConfig['default_content'],
            ];
        }

        if (($mailConfig['enabled'] ?? 0) == 0) {
            return;
        }

        $settingMail = getOption('email', null, false);
        if(isset($settingMail['smtp_username']) && !empty($settingMail['smtp_username']) && isset($settingMail['smtp_password']) && !empty($settingMail['smtp_password']) && !empty($mailTo)) {
            $title = $mailConfig['title'];
            $content = $mailConfig['content'];

            if (count($replaces)) {
                if (isset($replaces['title'])) {
                    $titleReplaces = $replaces['title'];
                    $title = str_replace($titleReplaces['searches'] ?? [], $titleReplaces['replaces'] ?? [], $title);
                }
                if (isset($replaces['content'])) {
                    $contentReplaces = $replaces['content'];
                    $content = str_replace($contentReplaces['searches'] ?? [], $contentReplaces['replaces'] ?? [], $content);
                }
            }

            try {
                Log::info('Start send mail ' . $module . ' - ' . $emailName . ' for ' . $mailTo);
                Mail::to($mailTo)->send(new \DreamTeam\Base\Mail\SendByTemplate(compact('title', 'content')));
                Log::info('Done send mail ' . $module . ' - ' . $emailName . ' for ' . $mailTo);
            } catch (\Exception $e) {
                Log::error($e);
                Log::error('Error send mail ' . $module . ' - ' . $emailName . ' for ' . $mailTo);
            }
        } else {
            Log::error('Chưa cấu hình server mail');
        }
    }
}
