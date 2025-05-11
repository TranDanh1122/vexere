## Hướng dẫn sử dụng DreamTeamCms ##

**Giới thiệu:**

**Mô tả:**

---------------------------------------------------------------------------------------------------------

### Các bước khởi tạo dự án từ DreamTeam Core ###

Tạo 1 dự án laravel trắng
	
	composer create-project --prefer-dist laravel/laravel project_name

Thêm cấu hình DB và tạo DB cho dự án tại `.env`

Cấu hình composer.json

	"require": {
		...
		"dreamteam/core": "*@dev"
	}
	...
	"repositories": [
        {
            "type": "vcs",
            "url": "git@bitbucket.org:taitt/dreamteam-base.git"
        }
    ]

Chạy lệnh dưới đây để cập nhật lại package

	composer update

Chạy lệnh dưới đây để tạo database:
	
	php artisan migrate

Chạy lệnh để tạo dữ liệu khởi đầu

	php artisan DreamTeam:seeds

Chạy lệnh dưới đây để sinh config và các file giao diện
	
	php artisan vendor:publish --tag=dreamteam/core

Cập nhật `config('filesystems.disks.local.root')` thành `public_path()`

Cập nhật `config('app.locale')` và `config('app.fallback_locale')` thành vi

Cập nhật `config('app.faker_locale')` thành `vi_VN`

Cập nhật `config('app.timezone')` thành `Asia/Ho_Chi_Minh`

Truy cập đường dẫn `/{admin_dir}/login` để đăng nhập, trong đó admin_dir là đường dẫn admin được đặt tại `config('app.admin_dir')`

---------------------------------------------------------------------------------------------------------

### Các Module mặc định ###

**Auth/Authenticate**: Đăng nhập | Đăng xuất | Quên mật khẩu

**AdminUser**: Quản lý danh sách admin_users

**Menu**: Cấu hình menu

**SystemLog**: Hệ thống tự động ghi logs khi Thêm | Cập nhật | Đăng nhập | Sửa | Xóa | Lấy lại

**Media**: Truy cập đường dẫn media/readme.md để xem chi tiết cách dùng

**Form**: Truy cập đường dẫn form/readme.md để xem chi tiết cách dùng

**Table**: Truy cập đường dẫn table/readme.md để xem chi tiết cách dùng

**Categories**: Truy cập đường dẫn categories/readme.md để xem chi tiết cách dùng

**Metaseo**: Lưu các cấu hình seos cho tất cả các bảng

**LanguageMeta**: Lưu đa ngôn ngữ cho tất cả các bảng