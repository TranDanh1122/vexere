<?php

return [
	// General Module
	'homepage'                 => 'Trang chủ',
    'error_message_catch'      => 'Có lỗi xảy ra trong tuần tự xử lý. Vui lòng thử lại!',
    // Login Page
    'login'                     => [
        'desc'                  => 'Đăng nhập',
        'user_or_email'         => 'Tên đăng nhập hoặc Email',
        'password'              => 'Mật khẩu',
        'remember_me'           => 'Nhớ đăng nhập',
        'login'                 => 'Đăng nhập',
        'forgot_password'       => 'Quên mật khẩu',
        'your_email'            => 'Nhập Email của bạn',
        'comfirm'               => 'Xác nhận',
        'close'                 => 'Đóng',
        'email_not_exist'       => 'Email của bạn không tồn tại trong hệ thống của chúng tôi!',
        'forgot_success'        => 'Hãy thay đổi mật khẩu tại đường dẫn chúng tôi gửi về Email của bạn!',

        'required_info'         => 'Bạn cần nhập đẩy đủ thông tin!',
        'required_name'         => 'Bạn cần nhập "Tên đăng nhập hoặc Email"!',
        'required_password'     => 'Bạn cần nhập "Mật khẩu"!',
        'required_forgot_email' => 'Bạn cần nhập "email"!',

        'login_success'         => 'Đăng nhập thành công!',
        'login_error'           => 'Sai tài khoản hoặc mật khẩu!',
        'login_status_permision'=> 'Tài khoản của bạn hiện không hoạt động!',
    ],

    'forgot_password'           => [
        'comfirm'               => 'Xác nhận',
        'change_password'       => 'Đổi mật khẩu',
        'password_new'          => 'Mật khẩu mới',
        'password_comfirm'      => 'Nhập lại mật khẩu',

        'required_info'         => 'Bạn cần nhập đẩy đủ thông tin!',
        'required_password'     => 'Bạn cần nhập "Mật khẩu"!',
        'required_password_comfirm' => '"Nhập lại mật khẩu" không được để trống!',
        'required_equal'        => '"Nhập lại mật khẩu" không chính xác!',
        'required_strong'       => 'Mật khẩu gồm có chữ hoa, chữ thường, số, ký tự đặc biệt và lớn hơn 6 ký tự!',
    ],

    // Header
    'account_info'              => 'Thông tin tài khoản',
    'change_password'           => 'Đổi mật khẩu',
    'logout'                    => 'Đăng xuất',
    'view_website'              => 'Xem Website',
    'cache_clear'               => 'Xóa cache',

    // Footer
    'footer'            => [
        'copyright'             => 'Bản quyền thuộc về <a href="https://dreamteam.com" target="_blank">DreamTeam</a>, Team: <a href="javascript:;">DreamTeamDevTeam<a>',
        'vesion'                => 'DreamTeamCore v2',
    ],

    // Dashboard Page
    'admin_system'              => 'Hệ thống quản trị',
    'dashboard'         => [
        'dreamteam_intro'            => 'Hệ thống core quản trị của DreamTeam',
    ],
    
    // Phân quyền
    'role'              => [
        'name'                  => 'Phân quyền',
        'select_all'            => 'Chọn tất cả',
        'no_permission'         => 'Bạn không có quyền truy cập tính năng này. Vui lòng liên hệ quản trị viên để cấp quyền!',
    ],

    // create and update
    'create_success'            => 'Thêm mới thành công.',
    'update_success'            => 'Cập nhật thành công.',
    // Ajax Result
    // Thành công
    'delete_success'            => 'Xóa thành công.',
    'restore_success'           => 'Lấy lại thành công.',
    // Không thấy dữ liệu
    'no_data_delete'            => 'Không tìm thấy dữ liệu cần xóa.',
    'no_data_restore'           => 'Không tìm thấy dữ liệu cần lấy lại.',
    'no_data_edit'              => 'Không tìm thấy dữ liệu cần sửa.',
    'not_found_data'            => 'Không tìm thấy dữ liệu.',
    'has_found_data'            => 'Tìm thấy dữ liệu.',
    // Không quyền hoặc lỗi
    'no_permission'             => 'Bạn không có quyền với thao tác này.',
    'ajax_error'                => 'Có lỗi xảy ra với thao tác.',
    'ajax_fail'                 => 'Có lỗi xảy ra. Vui lòng thử lại!',
    'ajax_error_edit'           => 'Bạn chưa thay đổi dữ liệu hoặc có lỗi xảy ra với thao tác.',
    // Xóa cache
    'cache_clear_success'       => 'Xóa cache thành công.',
    'cache_clear_fail'          => 'Xóa cache không thành công.',

    // text chung
    'unknown'                   => 'Không xác định',
    'recore_origin'             => 'Bản ghi gốc',
    'no_select_category'        => '--Chưa chọn danh mục--',
    'can_delete_users'          => 'Không thể xóa tài khoản này!',

    // Action system_logs
    'create'                    => 'Thêm mới',
    'update'                    => 'Cập nhật',
    'quick_delete'              => 'Xóa nhanh',
    'quick_update'              => 'Cập nhật nhanh',
    'quick_restore'             => 'Lấy lại nhanh',

    // Lịch sử hệ thống (logs)
    'logs'              => [
        'title'                 => 'Chi tiết lịch sử',
        'info_title'            => 'Thông tin',
        'detail'                => 'Chi tiết',
        'name'                  => 'Tên người thao tác',
        'ip'                    => 'Địa chỉ IP',
        'action'                => 'Hành động',
        'type'                  => 'Module thao tác',
        'type_id'               => 'ID thuộc module thao tác',
        'time'                  => 'Thời gian',
        'field'                 => 'Trường thay đổi',
        'old'                   => 'Cũ',
        'new'                   => 'Mới',
        'data'                  => 'Dữ liệu',
    ],

    'validate' => [
        'required_password'     => 'Bạn cần nhập "Mật khẩu"!',
        'required_password_comfirm' => '"Nhập lại mật khẩu" không được để trống!',
        'required_equal'        => '"Nhập lại mật khẩu" không chính xác!',
    ],
];