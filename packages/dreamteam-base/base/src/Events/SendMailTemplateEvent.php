<?php

namespace DreamTeam\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SendMailTemplateEvent
{
    use Dispatchable;

    /**
     * Gửi email bằng template với nội dung được cài đặt
     * @param string $module Tên module chứa template email.
     * @param string $emailName Tên của email template sẽ được sử dụng.
     * @param string $mailTo Địa chỉ email người nhận.
     * @param array $replaces Danh sách các giá trị thay thế được dùng trong template
     *  Cấu trúc:
     *  [
     *      'title' => [
     *          'searches' => ['{name}', '{orderCode}', '{amount}'],
     *          'replaces' => ['Example', 'AOC4D2AQF2', '200.000đ']
     *      ],
     *      'content' => [
     *          'searches' => ['{name}', '{orderCode}', '{amount}'],
     *          'replaces' => ['Example', 'AOC4D2AQF2', '200.000đ']
     *      ],
     *  ]
     */
    public function __construct(
        public string $module,
        public string $emailName,
        public string $mailTo,
        public array $replaces = []
    )
    {
    }
}
