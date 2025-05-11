<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Ecommerce\Enums\LocationEnum;

class HomeController extends Controller
{
    public function index()
    {
        $settingHome = getOption('home');
        $meta_seo = metaSeo('', '', [
			...$settingHome,
			'title' => $settingHome['meta_title'] ?? __('Đặt vé xe rẻ Sai Gòn - Vũng Tàu'),
			'description' => $settingHome['meta_description'] ?? __('Đặt vé xe rẻ Sai Gòn - Vũng Tàu'),
			'social_title' => $settingHome['social_title'] ?? $settingHome['meta_title'] ?? __('Đặt vé xe rẻ Sai Gòn - Vũng Tàu'),
			'social_description' => $settingHome['social_description'] ?? $settingHome['meta_description'] ?? __('Đặt vé xe rẻ Sai Gòn - Vũng Tàu'),
			'social_image' => $settingHome['social_image'] ?? getImage(),
			'image' => $settingHome['social_image'] ?? getImage(),
		]);
        return view('home.index', compact(
            'meta_seo',
            'settingHome'
        )); 
    }
    public function findSlot()
    {

        $settingHome = getOption('home');
        $meta_seo = metaSeo('', '', [
            ...$settingHome,
            'title' => "Tra cứu vé xe",
            'description' => "Tra cứu vé xe",
            'social_title' => "Tra cứu vé xe của bạn",
            'social_description' => "Tra cứu vé xe",
            'social_image' => $settingHome['social_image'] ?? getImage(),
            'image' => $settingHome['social_image'] ?? getImage(),
        ]);
        return view('home.tracuu', compact(
            'meta_seo',
            'settingHome'
        ));
    }
    public function test()
    {
        dd('Lock');
        DB::table('brands')->truncate();
        DB::table('locations')->truncate();
        DB::table('product_locations')->truncate();
        DB::table('language_metas')->whereIn('lang_table', ['brands', 'locations'])->delete();
        $sgLocation = '
            [
                {
                    "title": "Quận 2",
                    "children": [
                    {
                        "title": "67 Mai Chí Thọ (Volkswagen An Phú)"
                    },
                    {
                        "title": "Chung cư Cantavil"
                    },
                    {
                        "title": "Chung cư Estella"
                    },
                    {
                        "title": "Chung cư Centana"
                    },
                    {
                        "title": "Khu Đô Thị Sala"
                    },
                    {
                        "title": "Nhà chờ Phương Trang (Đường Mai Chí Thọ)"
                    },
                    {
                        "title": "Đối diện chung cư New City"
                    },
                    {
                        "title": "Nhà chờ Phương Trang (Mai Chí Thọ)"
                    },
                    {
                        "title": "Chung Cư Sala"
                    },
                    {
                        "title": "Chung cư Centana Thủ Thiêm"
                    },
                    {
                        "title": "Chung cư The Sun Avenue"
                    },
                    {
                        "title": "Nhà chờ xe Toàn Thắng"
                    },
                    {
                        "title": "Trạm xe Buýt cầu Đen, Quận 2"
                    },
                    {
                        "title": "28 Mai Chí Thọ"
                    },
                    {
                        "title": "67 Mai Chí Thọ"
                    },
                    {
                        "title": "10 Mai Chí Thọ"
                    },
                    {
                        "title": "Chung cư Lexington Residence"
                    },
                    {
                        "title": "Quận 2"
                    },
                    {
                        "title": "Metro Quận 2"
                    },
                    {
                        "title": "Parkson Cantavil Quận 2"
                    },
                    {
                        "title": "Đầu đường cao tốc Long Thành Dầu Giây"
                    },
                    {
                        "title": "Nhà chờ Phương Trang - Mai Chí Thọ"
                    },
                    {
                        "title": "36 Mai Chí Thọ"
                    },
                    {
                        "title": "Cafe Highland Cantavil"
                    },
                    {
                        "title": "Qua Cầu SG - Cầu vượt Metro Thảo điền"
                    },
                    {
                        "title": "Parkson Cantavil"
                    },
                    {
                        "title": "Trạm Buýt Cầu Đen"
                    }
                    ]
                },
                {
                    "title": "Quận 3",
                    "children": [
                    {
                        "title": "Nhà thờ Tân Định"
                    },
                    {
                        "title": "Chùa Vĩnh Nghiêm"
                    },
                    {
                        "title": "Cầu Công Lý"
                    },
                    {
                        "title": "Khách Sạn Lavela Lý Chính Thắng"
                    },
                    {
                        "title": "Nhà Thờ Tân Định"
                    },
                    {
                        "title": "49 Lý Chính Thắng"
                    },
                    {
                        "title": "Quận 3"
                    },
                    {
                        "title": "83 Lý Chính Thắng"
                    },
                    {
                        "title": "323 Nam Kỳ Khởi Nghĩa"
                    }
                    ]
                },
                {
                    "title": "Tân Bình",
                    "children": [
                    {
                        "title": "Sân Bay Tân Sơn Nhất - Ga Quốc Tế Cột 14"
                    },
                    {
                        "title": "Văn phòng Nguyễn Văn Trỗi"
                    },
                    {
                        "title": "Sân Bay Tân Sơn Nhất"
                    },
                    {
                        "title": "Sân Bay Tân Sơn Nhất - Ga Quốc Nội"
                    },
                    {
                        "title": "Trạm TC 8 Phạm Văn Hai, p2, Q. Tân Bình"
                    },
                    {
                        "title": "Văn Phòng Phạm Văn Hai"
                    },
                    {
                        "title": "VP Sài Gòn"
                    },
                    {
                        "title": "Bệnh viện Phụ sản Mê Kông"
                    },
                    {
                        "title": "Khách sạn Parkroyal Sài Gòn"
                    },
                    {
                        "title": "307 Nguyễn Văn Trỗi"
                    },
                    {
                        "title": "Sân bay Tân Sơn Nhất - Ga Quốc Nội"
                    },
                    {
                        "title": "Sân bay Tân Sơn Nhất - Ga Quốc Tế"
                    },
                    {
                        "title": "Công viên Hoàng Văn Thụ"
                    },
                    {
                        "title": "Văn Phòng Tân Bình"
                    }
                    ]
                },
                {
                    "title": "Bình Thạnh",
                    "children": [
                    {
                        "title": "Văn phòng Hàng Xanh"
                    },
                    {
                        "title": "Khu du lịch Văn Thánh"
                    },
                    {
                        "title": "602/1 Điện Biên Phủ"
                    },
                    {
                        "title": "Trạm TC 450K Điện Biên Phủ, Q. Bình Thạnh"
                    },
                    {
                        "title": "Ngã 4 Hàng Xanh"
                    },
                    {
                        "title": "Trạm Xe Buýt Cầu Sài Gòn"
                    },
                    {
                        "title": "Văn Phòng Toàn Thắng"
                    },
                    {
                        "title": "Bến Xe Miền Đông"
                    },
                    {
                        "title": "Hàng Xanh Khu Du Lịch Văn Thánh"
                    },
                    {
                        "title": "Trạm TC 153/62 quốc lộ 13"
                    },
                    {
                        "title": "Trạm TC 450K Điện Biên Phủ"
                    },
                    {
                        "title": "Trạm xe buýt cầu Sài Gòn"
                    },
                    {
                        "title": "VP Miền Đông"
                    },
                    {
                        "title": "Ngã Tư Hàng Xanh"
                    },
                    {
                        "title": "49 Bạch Đằng"
                    },
                    {
                        "title": "Cà phê Thủy Trúc"
                    },
                    {
                        "title": "Cây xăng Comeco (Cổng 1 - BXMĐ)"
                    },
                    {
                        "title": "Văn phòng Bình Thạnh"
                    }
                    ]
                },
                {
                    "title": "Quận 1",
                    "children": [
                    {
                        "title": "Văn phòng Quận 1"
                    },
                    {
                        "title": "34 Mai Thị Lựu"
                    },
                    {
                        "title": "Đối diện chợ Tân Định"
                    },
                    {
                        "title": "Văn phòng quận 1."
                    },
                    {
                        "title": "Quận 1"
                    },
                    {
                        "title": "Văn Phòng Sài Gòn"
                    },
                    {
                        "title": "Công Viên Lê Văn Tám"
                    },
                    {
                        "title": "Vòng xoay Điện Biên Phủ"
                    },
                    {
                        "title": "Cầu vượt Ngã 4 Ga"
                    },
                    {
                        "title": "Cầu vượt Quang Trung"
                    },
                    {
                        "title": "Cầu vượt Tân Thới Hiệp"
                    },
                    {
                        "title": "Ngã 4 An Sương"
                    },
                    {
                        "title": "Ngã 4 Vườn Lài"
                    },
                    {
                        "title": "Tu viện Khánh An"
                    },
                    {
                        "title": "Văn phòng quận 1"
                    },
                    {
                        "title": "Nội thành Sài Gòn"
                    },
                    {
                        "title": "VP Phạm Ngũ Lão"
                    },
                    {
                        "title": "127 Điện Biên Phủ"
                    },
                    {
                        "title": "Macdonal Vòng Xoay Điên Biên Phủ"
                    }
                    ]
                },
                {
                    "title": ""
                },
                {
                    "title": "Long Thành",
                    "children": [
                    {
                        "title": "Chợ Tân Mai"
                    },
                    {
                        "title": "Cổng 11"
                    },
                    {
                        "title": "Ngã 3 Tam An"
                    },
                    {
                        "title": "Ngã 4 Lộc An"
                    },
                    {
                        "title": "Cổng vào Sân Golf Long Thành"
                    },
                    {
                        "title": "Sân Golf Long Thành"
                    }
                    ]
                },
                {
                    "title": "Phú Nhuận",
                    "children": [
                    {
                        "title": "Văn Phòng Nguyễn Văn Trỗi"
                    },
                    {
                        "title": "307 Nguyễn Văn Trỗi"
                    },
                    {
                        "title": "Viện Y Dược học Dân tộc Tp.HCM"
                    },
                    {
                        "title": "Cây xăng Nguyễn Văn Trỗi"
                    },
                    {
                        "title": "Cổng xe lửa số 7 (Đường Nguyễn Văn Trỗi)"
                    },
                    {
                        "title": "Yamaha Town"
                    },
                    {
                        "title": "Văn Phòng Sân Bay"
                    },
                    {
                        "title": "Phú Nhuận"
                    },
                    {
                        "title": "Công viên Gia Định"
                    },
                    {
                        "title": "Quận Phú Nhuận"
                    }
                    ]
                },
                {
                    "title": "Thủ Đức",
                    "children": [
                    {
                        "title": "Cầu vượt Linh Xuân"
                    },
                    {
                        "title": "KCN Linh Trung"
                    },
                    {
                        "title": "Ngã 3 - 621"
                    },
                    {
                        "title": "Ngã 4 Bình Phước"
                    },
                    {
                        "title": "Ngã 4 Gò Dưa"
                    },
                    {
                        "title": "Siêu thị Coop Extra"
                    },
                    {
                        "title": "Đại học Kinh Tế Luật"
                    },
                    {
                        "title": "Đại học Nông Lâm"
                    },
                    {
                        "title": "Bến xe Miền Đông mới"
                    },
                    {
                        "title": "Cây xăng Quốc Phong"
                    },
                    {
                        "title": "Cây xăng Tam Bình 2"
                    },
                    {
                        "title": "Cầu Vượt Sóng Thần"
                    },
                    {
                        "title": "Ngã tư Gò Dưa"
                    },
                    {
                        "title": "Trạm xe Bus đối diện đại học Nông Lâm"
                    },
                    {
                        "title": "Ngã Tư Bình Phước"
                    }
                    ]
                },
                {
                    "title": "Sân bay Tân Sơn Nhất",
                    "children": [
                    {
                        "title": "Sân bay Tân Sơn Nhất - Ga Quốc Nội"
                    },
                    {
                        "title": "Sân bay Tân Sơn Nhất - Ga Quốc Tế"
                    },
                    {
                        "title": "Sân Bay Tân Sơn Nhất - Ga Quốc Nội"
                    },
                    {
                        "title": "Sân Bay Tân Sơn Nhất - Ga Quốc Tế"
                    }
                    ]
                },
                {
                    "title": "Dĩ An",
                    "children": [
                    {
                        "title": "Ngã ba Tân Vạn"
                    },
                    {
                        "title": "Cầu vượt Sóng Thần"
                    },
                    {
                        "title": "Giày da Thái Bình"
                    },
                    {
                        "title": "Ngã 3 Tân Vạn"
                    },
                    {
                        "title": "Bến xe Lam Hồng"
                    }
                    ]
                },
                {
                    "title": "Quận 8",
                    "children": [
                    {
                        "title": "Chung Cư Carina"
                    },
                    {
                        "title": "Chung Cư City Gate"
                    },
                    {
                        "title": "Quận 8"
                    }
                    ]
                },
                {
                    "title": "Quận 10",
                    "children": [
                    {
                        "title": "Quận 10"
                    },
                    {
                        "title": "Quận 5"
                    },
                    {
                        "title": "Quận 6"
                    },
                    {
                        "title": "Quận Bình Thạnh"
                    },
                    {
                        "title": "Quận Thủ Đức"
                    },
                    {
                        "title": "Quận Tân Bình"
                    },
                    {
                        "title": "Quận Tân Phú"
                    },
                    {
                        "title": "Trạm Quận 10"
                    },
                    {
                        "title": "VPSG"
                    }
                    ]
                },
                {
                    "title": "Quận 5",
                    "children": [
                    {
                        "title": "Quận 5"
                    },
                    {
                        "title": "Văn phòng quận 5"
                    },
                    {
                        "title": "VP Sài Gòn"
                    }
                    ]
                },
                {
                    "title": "Đồng Nai",
                    "children": [
                    {
                        "title": "Ngã 4 Vũng Tàu"
                    }
                    ]
                },
                {
                    "title": "Quận 9",
                    "children": [
                    {
                        "title": "Bệnh viện ung bướu 2"
                    },
                    {
                        "title": "Quận 9"
                    },
                    {
                        "title": "Khu du lịch Suối Tiên"
                    }
                    ]
                },
                {
                    "title": "Bình Tân",
                    "children": [
                    {
                        "title": "Bến xe Miền Tây"
                    },
                    {
                        "title": "Bến Xe Miền Tây"
                    }
                    ]
                },
                {
                    "title": "Bến xe Miền Tây",
                    "children": [
                    {
                        "title": "Bến Xe Miền Tây"
                    }
                    ]
                },
                {
                    "title": "Bến xe Miền Đông Mới",
                    "children": [
                    {
                        "title": "Bến xe miền đông mới"
                    }
                    ]
                },
                {
                    "title": "Quận 4"
                },
                {
                    "title": "Quận 6"
                },
                {
                    "title": "Bến xe An Sương"
                },
                {
                    "title": "Quận 12",
                    "children": [
                    {
                        "title": "Quận 12"
                    },
                    {
                        "title": "Cầu vượt An Sương"
                    }
                    ]
                },
                {
                    "title": "Củ Chi"
                },
                {
                    "title": "Nhà Bè"
                },
                {
                    "title": "Quận 11"
                },
                {
                    "title": "Quận 7"
                },
                {
                    "title": "Tân Phú"
                },
                {
                    "title": "Gò Vấp",
                    "children": [
                    {
                        "title": "Bệnh Viện 175"
                    }
                    ]
                },
                {
                    "title": "Hóc Môn"
                }
                ]
        ';
        $jsonsg = json_decode($sgLocation, true);
        $locations = [];
        foreach ($jsonsg as $item) {
            $location = [
                'name' => $item['title'],
                'from' => LocationEnum::SG,
                'status' => BaseStatusEnum::ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $parentId = DB::table('locations')->insertGetId($location);
            if (isset($item['children'])) {
                foreach ($item['children'] as $child) {
                    $name = ucfirst(strtolower($child['title']));
                    $locations[$name] = [
                        'name' => $name,
                        'from' => LocationEnum::SG,
                        'parent_id' => $parentId,
                        'status' => BaseStatusEnum::ACTIVE,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        DB::table('locations')->insert($locations);

        $vtLocation = '
            [
                {
                    "title": "Tân Thành",
                    "children": [
                    {
                        "title": "Ngã 3 Cái Mép",
                        "children": []
                    },
                    {
                        "title": "Cổng vòm Phú Mỹ",
                        "children": []
                    },
                    {
                        "title": "Siêu thị Coop Mart Tân Thành",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Long Sơn",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Hội Bài",
                        "children": []
                    },
                    {
                        "title": "Chợ Ông Trịnh",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Hải Sơn",
                        "children": []
                    },
                    {
                        "title": "Chợ Phước Lộc",
                        "children": []
                    },
                    {
                        "title": "Ngã 4 Chinfon",
                        "children": []
                    },
                    {
                        "title": "Ngã ba Mỹ Xuân",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Chu Hải",
                        "children": []
                    },
                    {
                        "title": "Giáo xứ Chu Hải",
                        "children": []
                    },
                    {
                        "title": "Chợ Phước Hòa",
                        "children": []
                    },
                    {
                        "title": "Chùa Vạn Thông",
                        "children": []
                    },
                    {
                        "title": "Chùa Vạn Phật Quang - Đại Tòng Lâm Tự",
                        "children": []
                    },
                    {
                        "title": "Chợ Chu Hải",
                        "children": []
                    },
                    {
                        "title": "Ủy ban nhân dân xã Tân Hải",
                        "children": []
                    },
                    {
                        "title": "Đối diện Nhà thờ Láng Cát",
                        "children": []
                    },
                    {
                        "title": "Khu CN Gò Dầu",
                        "children": []
                    },
                    {
                        "title": "KCN Mỹ Xuân A",
                        "children": []
                    },
                    {
                        "title": "Cây xăng Láng Cát",
                        "children": []
                    },
                    {
                        "title": "Cây xăng Phú Mỹ",
                        "children": []
                    },
                    {
                        "title": "Thị Trấn Phú Mỹ (Dọc Quốc lộ 51)",
                        "children": []
                    },
                    {
                        "title": "Chợ Châu Pha",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Giáo xứ Châu Pha",
                        "children": []
                    },
                    {
                        "title": "Trường THCS Trương Công Định",
                        "children": []
                    },
                    {
                        "title": "Trạm Y tế Xã Châu Pha",
                        "children": []
                    },
                    {
                        "title": "Ủy ban Nhân dân Xã Châu Pha",
                        "children": []
                    },
                    {
                        "title": "QL 51, Tân Thành",
                        "children": []
                    },
                    {
                        "title": "Nhà hàng Trần Long",
                        "children": []
                    },
                    {
                        "title": "Ngã Tư Trần Long",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Lam Sơn",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Mỹ Xuân",
                        "children": []
                    },
                    {
                        "title": "Chợ Mỹ Xuân",
                        "children": []
                    },
                    {
                        "title": "Chung Cư 18 tầng Phú Mỹ",
                        "children": []
                    },
                    {
                        "title": "Chợ Lam Sơn",
                        "children": []
                    },
                    {
                        "title": "Co.op Mart Tân Thành",
                        "children": []
                    },
                    {
                        "title": "Cầu Ngọc Hà",
                        "children": []
                    },
                    {
                        "title": "Giáo Xứ Chu Hải",
                        "children": []
                    },
                    {
                        "title": "Giáo xứ Thanh Phong",
                        "children": []
                    },
                    {
                        "title": "KCN Mỹ Xuân",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Láng Cát",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Phước Lộc",
                        "children": []
                    },
                    {
                        "title": "Tân Thành (QL51)",
                        "children": []
                    },
                    {
                        "title": "Đối diện Nhà thờ Song Vĩnh",
                        "children": []
                    },
                    {
                        "title": "Chợ Ngọc Hà",
                        "children": []
                    },
                    {
                        "title": "KCN Phú Mỹ",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Song Vĩnh",
                        "children": []
                    },
                    {
                        "title": "Thị Trấn Phú Mỹ",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Vũng Tàu",
                    "children": [
                    {
                        "title": "Ẹo Ông Từ",
                        "children": []
                    },
                    {
                        "title": "Cảng Cát Lở",
                        "children": []
                    },
                    {
                        "title": "Nội thành Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Lottemart Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Bến Xe Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "VP Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Bùng binh Cầu Cửa Lấp",
                        "children": []
                    },
                    {
                        "title": "Bùng binh Metro",
                        "children": []
                    },
                    {
                        "title": "Bùng binh Ngã 5 Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Bùng binh Ngã ba Lê Hồng Phong",
                        "children": []
                    },
                    {
                        "title": "Chung cư Vũng Tàu Melody",
                        "children": []
                    },
                    {
                        "title": "Chùa thích ca phật đài",
                        "children": []
                    },
                    {
                        "title": "Cảng Dầu khí Vietsovpetro",
                        "children": []
                    },
                    {
                        "title": "Kim Minh Plaza",
                        "children": []
                    },
                    {
                        "title": "Tượng đài Trần Hưng Đạo",
                        "children": []
                    },
                    {
                        "title": "Viettel Vũng Tàu - Ngã tư Giếng Nước",
                        "children": []
                    },
                    {
                        "title": "Vòng xoay Tượng đài Dầu khí",
                        "children": []
                    },
                    {
                        "title": "Điện Máy Xanh (Ba Mươi Tháng Tư)",
                        "children": []
                    },
                    {
                        "title": "Văn Phòng Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Cây Xăng Petrolimex số 10",
                        "children": []
                    },
                    {
                        "title": "Hải đoàn 128",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Phước Thành",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ giáo xứ Nam Bình",
                        "children": []
                    },
                    {
                        "title": "Bến xe Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Nội Thành Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Bùng Binh Chợ Hòa Long",
                        "children": []
                    },
                    {
                        "title": "Văn phòng Hoa Mai Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Trạm TC 16 Trưng Trắc",
                        "children": []
                    },
                    {
                        "title": "Trạm TC 439 Bình Giã",
                        "children": []
                    },
                    {
                        "title": "Trạm TC 508 đường 2/9",
                        "children": []
                    },
                    {
                        "title": "Trạm TC 54B đường 30/4",
                        "children": []
                    },
                    {
                        "title": "Trạm TC 848 Bình Giã",
                        "children": []
                    },
                    {
                        "title": "Văn phòng Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Trung tâm Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "456 Bình Giã",
                        "children": []
                    },
                    {
                        "title": "468 Bình Giã",
                        "children": []
                    },
                    {
                        "title": "Bùng Binh Cửa Lấp",
                        "children": []
                    },
                    {
                        "title": "Cổng Bệnh Viện Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Khu đô thị Chí Linh",
                        "children": []
                    },
                    {
                        "title": "Ngã 4 Chí Linh",
                        "children": []
                    },
                    {
                        "title": "Ngã 4 Đô Lương (Tòa án)",
                        "children": []
                    },
                    {
                        "title": "Vòng xoay Metro",
                        "children": []
                    },
                    {
                        "title": "Nội ô TP Vũng Tàu (đón/trả đầu hẻm)",
                        "children": []
                    },
                    {
                        "title": "350 Nguyễn An Ninh",
                        "children": []
                    },
                    {
                        "title": "Trụ sở ngân hàng ACB (Lê Hồng Phong)",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Bà Rịa",
                    "children": [
                    {
                        "title": "Cổng chào Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Bến xe Bà Rịa.",
                        "children": []
                    },
                    {
                        "title": "Bánh canh Trảng Bàng Năm Dung",
                        "children": []
                    },
                    {
                        "title": "Cây xăng Kim Hải",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Kim Hải",
                        "children": []
                    },
                    {
                        "title": "Co.opmart Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Chợ Hòa Long",
                        "children": []
                    },
                    {
                        "title": "Bến xe Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Nội thành Thành phố Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Phường Kim Dinh - Dọc Quốc Lộ 51",
                        "children": []
                    },
                    {
                        "title": "UBND Xã Hòa Long",
                        "children": []
                    },
                    {
                        "title": "Văn Phòng Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Xã Hòa Long",
                        "children": []
                    },
                    {
                        "title": "Xã Long Phước",
                        "children": []
                    },
                    {
                        "title": "Xã Tân Hưng",
                        "children": []
                    },
                    {
                        "title": "Văn phòng Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Bến Xe Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Coop Mart Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Trung Tâm Thương Mại Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Nội thành thành phố Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Chợ mới Kim Hải",
                        "children": []
                    },
                    {
                        "title": "Co.opMart Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "GO! Bà Rịa",
                        "children": []
                    },
                    {
                        "title": "Ngã Ba Chùa Phật Quang",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Long Thành",
                    "children": [
                    {
                        "title": "Chợ mới Phước Thái",
                        "children": []
                    },
                    {
                        "title": "KCN Vedan",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Hiền Hoà",
                        "children": []
                    },
                    {
                        "title": "Thiền Viện Thường Chiếu",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Nhơn Trạch",
                        "children": []
                    },
                    {
                        "title": "Trường CĐ Công Nghệ Quốc Tế Lilama",
                        "children": []
                    },
                    {
                        "title": "Khu CN Gò Dầu",
                        "children": []
                    },
                    {
                        "title": "Trạm dừng chân Bánh Việt 70",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Xuyên Mộc",
                    "children": [
                    {
                        "title": "Le Palmier Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Melia Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Novaworld Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Vietsovpetro Resort",
                        "children": []
                    },
                    {
                        "title": "Angsana Ho Tram",
                        "children": []
                    },
                    {
                        "title": "Carmelina Beach Resort",
                        "children": []
                    },
                    {
                        "title": "Dhawa Ho Tram",
                        "children": []
                    },
                    {
                        "title": "Emerald Hồ Tràm Resort",
                        "children": []
                    },
                    {
                        "title": "Hamptons Plaza Ho Tram",
                        "children": []
                    },
                    {
                        "title": "Ho Tram Beach Boutique Resort",
                        "children": []
                    },
                    {
                        "title": "Holiday Inn Reosrt Ho Tram Beach",
                        "children": []
                    },
                    {
                        "title": "Hyatt Regency Ho Tram Resort and Residences",
                        "children": []
                    },
                    {
                        "title": "Ixora Ho Tram",
                        "children": []
                    },
                    {
                        "title": "Sanctuary Resort",
                        "children": []
                    },
                    {
                        "title": "The Beach House Resort Ho Tram",
                        "children": []
                    },
                    {
                        "title": "Tropicana Beach Resort & Spa",
                        "children": []
                    },
                    {
                        "title": "Villa Coaster Ho tram Resort",
                        "children": []
                    },
                    {
                        "title": "Dhawa Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Emerald Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Meliá Ho Tram Beach Resort",
                        "children": []
                    },
                    {
                        "title": "The Grand Ho Tram Ship",
                        "children": []
                    },
                    {
                        "title": "Vietsovpetro Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Cầu Sông Chùa",
                        "children": []
                    },
                    {
                        "title": "Cầu suối Nước Mặn",
                        "children": []
                    },
                    {
                        "title": "Bình Châu (Vũng Tàu)",
                        "children": []
                    },
                    {
                        "title": "Bến xe khách huyện Xuyên Mộc",
                        "children": []
                    },
                    {
                        "title": "Chợ Bình Châu",
                        "children": []
                    },
                    {
                        "title": "Seava Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Bãi biển Bình Châu",
                        "children": []
                    },
                    {
                        "title": "Bến Xe Xuyên Mộc",
                        "children": []
                    },
                    {
                        "title": "Chợ Bông Trang",
                        "children": []
                    },
                    {
                        "title": "Chợ Bưng Riềng",
                        "children": []
                    },
                    {
                        "title": "Chợ Xuyên Mộc",
                        "children": []
                    },
                    {
                        "title": "Hồ Cốc",
                        "children": []
                    },
                    {
                        "title": "Hồ Cốc (Dọc quốc lộ 55)",
                        "children": []
                    },
                    {
                        "title": "Novaland Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Suối nước nóng Bình Châu",
                        "children": []
                    },
                    {
                        "title": "Tropicana Park",
                        "children": []
                    },
                    {
                        "title": "Trung chuyển Hồ Tràm",
                        "children": []
                    },
                    {
                        "title": "Vòng Xoay Bờ Hồ (Xuyên Mộc)",
                        "children": []
                    },
                    {
                        "title": "Vòng xoay bệnh viện Xuyên Mộc",
                        "children": []
                    },
                    {
                        "title": "Xuyên Mộc",
                        "children": []
                    },
                    {
                        "title": "Đường Trần Vĩnh Lộc",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Đất Đỏ",
                    "children": [
                    {
                        "title": "Lan Rừng Resort",
                        "children": []
                    },
                    {
                        "title": "Chợ Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "Oceanami Villa",
                        "children": []
                    },
                    {
                        "title": "Chợ Long Tân",
                        "children": []
                    },
                    {
                        "title": "Chợ Phước Hải",
                        "children": []
                    },
                    {
                        "title": "Cross Long Hải - Hotel & Resort",
                        "children": []
                    },
                    {
                        "title": "Công An Thị Trấn Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "Cầu Khánh Vân",
                        "children": []
                    },
                    {
                        "title": "Giáo Xứ Long Tân",
                        "children": []
                    },
                    {
                        "title": "Quảng Trường Thả Diều",
                        "children": []
                    },
                    {
                        "title": "Thị Trấn Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "Trung Tâm Y Tế Huyện Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "Trân Châu Beach & Resort",
                        "children": []
                    },
                    {
                        "title": "UBND Huyện Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "UBND Xã Long Tân",
                        "children": []
                    },
                    {
                        "title": "Xã Long Tân",
                        "children": []
                    },
                    {
                        "title": "Đồn Biên Phòng Phước Hải",
                        "children": []
                    },
                    {
                        "title": "Chợ đất đỏ",
                        "children": []
                    },
                    {
                        "title": "Dọc đường Ql55",
                        "children": []
                    },
                    {
                        "title": "Trung tâm hành chính Đất Đỏ",
                        "children": []
                    },
                    {
                        "title": "Vòng xoay Võ Thị Sáu",
                        "children": []
                    },
                    {
                        "title": "Nhà lưu niệm Võ Thị Sáu",
                        "children": []
                    },
                    {
                        "title": "Chợ Láng Dài",
                        "children": []
                    },
                    {
                        "title": "Hồ Tràm Sky Ocean",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Láng Dài",
                        "children": []
                    },
                    {
                        "title": "Trung tâm văn hoá Lộc An",
                        "children": []
                    },
                    {
                        "title": "Vòng Xoay Võ Thị Sáu",
                        "children": []
                    },
                    {
                        "title": "Đường Võ Văn Kiệt",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Châu Đức",
                    "children": [
                    {
                        "title": "Châu Đức",
                        "children": []
                    },
                    {
                        "title": "Chợ Nghĩa Thành",
                        "children": []
                    },
                    {
                        "title": "Chợ Suối Nghệ",
                        "children": []
                    },
                    {
                        "title": "Chợ Đức Mỹ",
                        "children": []
                    },
                    {
                        "title": "Cây xăng Petrolimex Số 16 (QL56)",
                        "children": []
                    },
                    {
                        "title": "Cây xăng Đường số 6 + 11 Nghĩa Thành",
                        "children": []
                    },
                    {
                        "title": "Công viên nước AquaPark",
                        "children": []
                    },
                    {
                        "title": "Cổng Bệnh Viện Tâm Thần Tỉnh Bà Rịa - Vũng Tàu",
                        "children": []
                    },
                    {
                        "title": "Hội Thánh Tin lành Chi hội Suối Nghệ",
                        "children": []
                    },
                    {
                        "title": "Nhà Thờ Đức Mỹ",
                        "children": []
                    },
                    {
                        "title": "Trường THCS Quang Trung",
                        "children": []
                    },
                    {
                        "title": "Trường THPT Ngô Quyền",
                        "children": []
                    },
                    {
                        "title": "Trường Tiểu học Nghĩa Thành",
                        "children": []
                    },
                    {
                        "title": "Trạm y tế Nghĩa Thành",
                        "children": []
                    },
                    {
                        "title": "Đồi cừu Suối Nghệ",
                        "children": []
                    },
                    {
                        "title": "Ủy ban Nhân dân Nghĩa Thành",
                        "children": []
                    },
                    {
                        "title": "Ủy ban Nhân dân Suối Nghệ",
                        "children": []
                    },
                    {
                        "title": "VPVT Phước Hiển",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "Long Điền",
                    "children": [
                    {
                        "title": "Chợ Long Điền",
                        "children": []
                    },
                    {
                        "title": "Thị Trấn Long Hải",
                        "children": []
                    },
                    {
                        "title": "Thị Trấn Long Điền",
                        "children": []
                    },
                    {
                        "title": "Xã An Ngãi",
                        "children": []
                    },
                    {
                        "title": "Xã Phước Hưng",
                        "children": []
                    },
                    {
                        "title": "Xã Phước Tỉnh",
                        "children": []
                    },
                    {
                        "title": "Huyện Long Điền",
                        "children": []
                    },
                    {
                        "title": "Chung cư Kim Tơ",
                        "children": []
                    },
                    {
                        "title": "Chợ Long Hải",
                        "children": []
                    },
                    {
                        "title": "Fleur de Lys Resort & Spa Long Hai",
                        "children": []
                    },
                    {
                        "title": "Lan Rừng Phước Hải Resort",
                        "children": []
                    },
                    {
                        "title": "Long Hai Channel Beach Resort",
                        "children": []
                    },
                    {
                        "title": "Nhà thờ Giáo xứ Long Hải",
                        "children": []
                    },
                    {
                        "title": "Ocenami Long Hải",
                        "children": []
                    },
                    {
                        "title": "Trân Châu Beach & Resort",
                        "children": []
                    },
                    {
                        "title": "UBND xã Phước Tỉnh",
                        "children": []
                    },
                    {
                        "title": "Văn Phòng Phước Hải",
                        "children": []
                    },
                    {
                        "title": "Văn phòng Long Hải",
                        "children": []
                    },
                    {
                        "title": "Đèo Nước Ngọt",
                        "children": []
                    },
                    {
                        "title": "Trung chuyển Phước Tỉnh",
                        "children": []
                    },
                    {
                        "title": "Chợ An Ngãi",
                        "children": []
                    },
                    {
                        "title": "Bến xe Long Điền",
                        "children": []
                    },
                    {
                        "title": "Chợ Phước Tỉnh",
                        "children": []
                    },
                    {
                        "title": "Ngã 3 Lò Vôi",
                        "children": []
                    },
                    {
                        "title": "An Nhứt",
                        "children": []
                    },
                    {
                        "title": "Bánh Hỏi An Nhứt",
                        "children": []
                    },
                    {
                        "title": "Cây Xăng Đông Nam",
                        "children": []
                    }
                    ]
                },
                {
                    "title": "",
                    "children": []
                },
                {
                    "title": "Bến xe Bà Rịa",
                    "children": []
                },
                {
                    "title": "Long Hải",
                    "children": []
                },
                {
                    "title": "La Gi",
                    "children": []
                },
                {
                    "title": "Hàm Tân",
                    "children": []
                },
                {
                    "title": "Bến xe Vũng Tàu",
                    "children": []
                }
                ]
        ';
        $jsonvt = json_decode($vtLocation, true);
        $locations = [];
        foreach ($jsonvt as $item) {
            $location = [
                'name' => $item['title'],
                'from' => LocationEnum::VT,
                'status' => BaseStatusEnum::ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $parentId = DB::table('locations')->insertGetId($location);
            if (isset($item['children'])) {
                foreach ($item['children'] as $child) {
                    $name = ucfirst(strtolower($child['title']));
                    $locations[$name] = [
                        'name' => $name,
                        'from' => LocationEnum::VT,
                        'parent_id' => $parentId,
                        'status' => BaseStatusEnum::ACTIVE,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        DB::table('locations')->insert($locations);

        $brands = [
            "Anh Quốc Limousine",
            "AVIGO",
            "Bến Thành Travel",
            "Huy Hoàng",
            "Hoa Mai",
            "Hưng Phát Xanh",
            "Hồng Sơn (Phú Yên)",
            "Kumho Samco",
            "Lê Hải",
            "Phước Hiếu (Vũng Tàu)",
            "Quốc Ngọc",
            "Toàn Thắng - Vũng Tàu",
            "Thành Vinh (Vũng Tàu)",
            "Thuê xe 5 chỗ",
            "Thanh Phong (Xuyên Mộc)",
            "Vie Limousine"
        ];
        $brands = array_map(function ($brand) {
            return [
                'name' => $brand,
                'status' => BaseStatusEnum::ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $brands);
        DB::table('brands')->insert($brands);          

        //init insert lang meta from list 
        $locations = DB::table('locations')->get();
        foreach ($locations as $location) {
            DB::table('language_metas')->insert([
                'lang_table' => 'locations',
                'lang_table_id' => $location->id,
                'lang_locale' => 'vi',
                'lang_code' => getCodeLangMeta(),
            ]);
        }
        $brands = DB::table('brands')->get();
        foreach ($brands as $brand) {
            DB::table('language_metas')->insert([
                'lang_table' => 'brands',
                'lang_table_id' => $brand->id,
                'lang_locale' => 'vi',
                'lang_code' => getCodeLangMeta(),
            ]);
        }
        dd($locations);
    }
}
