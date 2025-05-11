## Hướng dẫn sử dụng DreamTeam Assets ##

**Giới thiệu:** Đây là package dùng để quản lý các file assets, quản lý các phiên bản một cách dễ dàng và thuận tiên nhất.

## Cài đặt để sử dụng ##
- Để có thể sử dụng Package cần require theo lệnh `composer require dreamteam/assets`

## Publish ##

Mặc định khi chạy lệnh `php artisan dreamteam/core` đã sinh luôn cho package này, nhưng có một vài trường hợp chỉ muốn tạo lại riêng cho package này thì sẽ chạy các hàm dưới đây:

* Khởi tạo chung theo core
	- Tạo configs `php artisan vendor:publish --tag=dreamteam/core`
	- Chỉ tạo configs `php artisan vendor:publish --tag=dreamteam/core/config`
* Khởi tạo riêng theo package
	- Tạo configs `php artisan vendor:publish --tag=dreamteam/asset`
	- Chỉ tạo configs `php artisan vendor:publish --tag=dreamteam/asset/config`

## Cách dùng ##

Để sử dụng thì việc đầu tiên chúng ra phải chèn đoạn mã dưới đây vào layouts master:

- Chèn vào thẻ **head**: 
	
		{!! Asset::renderHeader() !!}

- Chèn vào trên thẻ đóng **body**: 

		{!! Asset::renderFooter() !!}

Để quản lý các file css/js thì chúng ta sẽ dễ dàng quản lý tại `config/dreamteam_asset.php`:

	return [
	    // Mặc định sẽ là true, assets sẽ được load từ local, 
	    // Nếu set offline là false và resource có định nghĩa use_cdn là true thì assets sẽ được load từ cdn
	    'offline' => env('ASSETS_OFFLINE', true),
	
	    // Bật hiển thị version, lúc này link tới resource sẽ được nối thêm "?v=1.0" chẳng hạn.
	    'enable_version' => true,
	
	    // Version hiển thị khi enable_vesion là true
	    'vesion' => '1.0',
	
	    // Các thư viện css mặc định được sử dụng, là key được định nghĩa trong phần resource bên dưới.
	    'styles' => [
	        //
	    ],
	
	    // Các thư viện js mặc định được sử dụng, là key được định nghĩa trong phần resource bên dưới.
	    'scripts' => [
	        //
	    ],
	
	    // Định nghĩa tất cả đường dẫn tới assets.
	    'resources' => [
	    	// Định nghĩa các thư viện css
	    	'styles' => [
	            'style' => [
			    // Có cho phép sử dụng cdn hay không, nếu là true thì phải định nghĩa link tới cnd bên dưới
		            'use_cdn' => false,
			    // Vị trí chèn trên header hay dưới footer lần lượt là top, bottom
		            'location' => 'top',
			    // Đường dẫn tới thư viện
		            'src' => [
				// Đường dẫn local
		            	'local' => '/assets/css/style.min.css',
				// Đường dẫn cdn
		            	'cdn' => null,
		            ],
			    // Thuộc tính của thẻ theo dạng [ key => value, key2 => value2 ]
			    // VD: "type" => "text/css" thì thẻ link sẽ có thêm thuộc tính "type=text/css"
		            'attributes' => [
			    	type" => "text/css"
			    ],
		        ],
	        ],
	
	        // Định nghĩa các thư viện js
	        'scripts' => [
	            'jquery' => [
			    // Có cho phép sử dụng cdn hay không, nếu là true thì phải định nghĩa link tới cnd bên dưới
	                   'use_cdn' => false,
			    // Vị trí chèn trên header hay dưới footer lần lượt là top, bottom
	                    'location' => 'top',
			    // Đường dẫn tới thư viện
	                   'src' => [
			    	// Đường dẫn local
	                    	'local' => '/assets/libs/jquery/jquery.min.js',
			    	// Đường dẫn cdn
	                    	'cdn' => null,
	                    ],
			    // Thuộc tính của thẻ theo dạng [ key => value, key2 => value2 ]
			   // VD: "async" => "" thì thẻ link sẽ có thêm thuộc tính "async"
	                   'attributes' => [],
	            ],
	        ],
	    ],
	];

**Một số hàm hỗ trợ sẵn:**

Ngoài việc định nghĩa assets trong file config/assets.php, chúng ta còn có thể thêm/bỏ js, css trực tiếp từ controller:

	public function show()
	{
		\Assets::addStyle(['key-style-su-dung'])
			->addScript(['key-script-su-dung'])
			->removeStyle(['key-style-khong-su-dung'])
			->removeScript(['key-script-khong-su-dung']);
	}

Thêm assets 1 cách trực tiếp mà không cần phải thêm tại config:

	\Asset::addDirectly([
		// đường dẫn đến assets, khuyến khich dùng hàm asset()
	    ], 'styles', 'top')
		->addDirectly([
		// đường dẫn đến assets, khuyến khich dùng hàm asset()
	    ], 'styles', 'bottom')
		->addDirectly([
		// đường dẫn đến assets, khuyến khich dùng hàm asset()
	    ], 'scripts', 'top')
		->addDirectly([
		// đường dẫn đến assets, khuyến khich dùng hàm asset()
	    ], 'scripts', 'bottom')
