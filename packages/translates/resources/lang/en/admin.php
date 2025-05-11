<?php

return [
	// General Module
	'homepage'                 => 'Home',

    // Login Page
    'login'                     => [
        'desc'                  => '<b>Login</b>',
        'user_or_email'         => 'Username or Email',
        'password'              => 'Password',
        'remember_me'           => 'Remember me',
        'login'                 => 'Login',
        'forgot_password'       => 'Forgot Password',
        'your_email'            => 'Your Email here',
        'comfirm'               => 'Comfirm',
        'close'                 => 'Close',
        'email_not_exist'       => 'Your email is not exist in system!',
        'forgot_success'        => 'Please change the password at the link we sent to your Email!',

        'required_info'         => 'You need to enter enough information!',
        'required_name'         => '"Username or Email" is required!',
        'required_password'     => '"Password" is required!',
        'required_forgot_email' => '"Email" is required!',

        'login_success'         => 'Login Success',
        'login_error'           => 'Username or Password is not exactly!',
        'login_status_permision'=> 'Your account is not active!',
    ],

    'forgot_password'           => [
        'comfirm'               => 'Comfirm',
        'change_password'       => 'Change Password',
        'password_new'          => 'Password',
        'password_comfirm'      => 'Password Comfirm',

        'required_info'         => 'You need to enter enough information!',
        'required_password'     => '"Password" is required!',
        'required_password_comfirm' => '"Password Comfirm" is required!',
        'required_equal'        => '"Password Comfirm" is not exactly!',
        'required_strong'       => 'Password includes uppercase, lowercase, numbers, special characters and greater than 6 characters!',
    ],

    // Header
    'account_info'              => 'Account Info',
    'change_password'           => 'Change password',
    'logout'                    => 'Logout',
    'view_website'              => 'View Website',
    'cache_clear'               => 'Cache Clear',

    // Footer
    'footer'            => [
        'copyright'             => 'Bản quyền thuộc về <a href="https://dreamteam.com" target="_blank">DreamTeam</a>, Team: <a href="javascript:;">DreamTeamDevTeam<a>',
        'vesion'                => 'DreamTeamCore v2',
    ],

    // Dashboard Page
    'admin_system'              => 'Administration System',
    'dashboard'         => [
        'dreamteam_intro'            => 'Administration System of DreamTeam',
    ],
    
    // Phân quyền
    'role'              => [
        'name'                  => 'Role',
        'select_all'            => 'Check All',
    ],

    // create and update
    'create_success'            => 'Create success.',
    'update_success'            => 'Update success.',
    // Ajax Result
    // Thành công
    'delete_success'            => 'Delete success.',
    'restore_success'           => 'Restore success.',
    // Không thấy dữ liệu
    'no_data_delete'            => 'No data delete.',
    'no_data_restore'           => 'No data restore.',
    'no_data_edit'              => 'No data edit.',
    'not_found_data'            => 'Not found data.',
    'has_found_data'            => 'Has found data.',
    // Không quyền hoặc lỗi
    'no_permission'             => 'Permission denined.',
    'ajax_error'                => 'An error occurred with the operation.',
    'ajax_fail'                 => 'An error occurred. Please try again!',
    'ajax_error_edit'           => 'You have not changed data or there was an error with the operation.',
    // Xóa cache
    'cache_clear_success'       => 'Cache clear success.',
    'cache_clear_fail'          => 'Cache clear fail.',

    // text chung
    'unknown'                   => 'Unknown',
    'recore_origin'             => 'Record Origin',
    'no_select_category'        => '--No Select Category--',
    'can_delete_users'          => 'Can not delete this users!',

    // Action system_logs
    'create'                    => 'Create',
    'update'                    => 'Update',
    'quick_delete'              => 'Quick delete',
    'quick_update'              => 'Quick update',
    'quick_restore'             => 'Quick restore',

    // Lịch sử hệ thống (logs)
    'logs'              => [
        'title'                 => 'Logs Detail',
        'info_title'            => 'Info',
        'detail'                => 'Detail',
        'name'                  => 'Admin Name',
        'ip'                    => 'IP Address',
        'action'                => 'Action',
        'type'                  => 'Module name',
        'type_id'               => 'Module ID',
        'time'                  => 'Time',
        'field'                 => 'Field change',
        'old'                   => 'Old',
        'new'                   => 'New',
        'data'                  => 'Data',
    ],

    'validate' => [
        'required_password'     => '"Password" is required!',
        'required_password_comfirm' => '"Password Comfirm" is required!',
        'required_equal'        => '"Password Comfirm" is not exactly!',
    ],
];