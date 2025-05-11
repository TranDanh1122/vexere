# Hướng dẫn sử dụng DreamTeamCore
* Tạo file .env, nội dung copy từ file .env.example
* Cấu hình và tạo DB
* Kiểm tra file /root/composer.json và gọi các module cần thiết cho dự án
* Chạy composer install
* Chạy php artisan key:generate để tạo APP_KEY
* Chạy php artisan migrate để tạo bảng CSDL theo từng package
* Chạy php artisan admin_users:seeds để tạo tài khoản quản trị
* Chạy php artisan license:seeds để khởi tạo dữ liệu license (Tính năng này dùng để giảm thiểu khả năng người ngoài có thể sử dụng cms của DreamTeam)
* Chạy lệnh `php artisan vendor:publish --tag=dreamteam/core/assets --force` để publish và ghi đè các config , assets thay đổi từ package

# Chạy lệnh `npm run tailwind` để build css tailwind khi dev
# Chạy lệnh `npm run watch` để build assets khi dev
# Chạy lệnh `npm run prod` để build assets khi lên prod
dreamteam - I%h1*Nk8eTU!