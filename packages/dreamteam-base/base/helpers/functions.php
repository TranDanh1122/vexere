<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use DreamTeam\Base\Facades\Action;
use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Base\Facades\CacheHelper;
use DreamTeam\Base\Facades\Filter;
use DreamTeam\Base\Facades\Setting;
use DreamTeam\Base\Supports\SettingStore;
use DreamTeam\Base\Events\ClearCacheEvent;
use DreamTeam\Base\Facades\DashboardMenu;
use DreamTeam\Base\Supports\DashboardMenu as DashboardMenuSupport;
use DreamTeam\Base\Facades\MenuStore;
use DreamTeam\Base\Supports\MenuStore as MenuStoreSupport;
use DreamTeam\Base\Services\Interfaces\SeoServiceInterface;
use DreamTeam\Base\Services\Interfaces\LanguageMetaServiceInterface;
use DreamTeam\Base\Services\Interfaces\MenuServiceInterface;
use DreamTeam\Base\Services\Interfaces\SlugServiceInterface;
use DreamTeam\Media\Facades\RvMedia;

/**
 * Đặt lại ngôn ngữ nếu trên url có request setLanguage
 * @param string        $language: key của ngôn ngữ đặt tại config('app.language')
 */
function setLanguage($language)
{
    // Đặt lại ngôn ngữ nếu trên url có request setLanguage
    if (isset($language) && !empty($language)) {
        // Chỉ lấy ngôn ngữ chỉ định tại config tránh nhập ngôn ngữ không có
        if (array_key_exists($language, config('app.language') ?? [])) {
            session(['locale' => $language]);
        }
    }
    // Set ngôn ngữ cho mọi route
    App::setLocale(Session::get('locale') ?? App::getLocale());
}
/**
 * Dùng để validate dữ liệu cho toàn bộ Form trên web
 * @param requests       $requests: Request của form truyên vào
 * @param string         $field: Tên trường dùng để kiểm tra
 * @param string         $message: Text kiểm tra nếu lỗi
 * @param string         $typeValidate: Loại validate (VD: required | unique | email | min | max | same)
 * @param string         $typeSpecial: Một vài Loại validate đặc biệt có giá trị (VD: min:10 | max:10 | unique:table_name)
 */
function validateForm($requests, $field, $message = '', $typeValidate = 'required', $typeSpecial = '')
{
    if (!empty($typeSpecial)) {
        $requests->validate([$field => $typeSpecial], [$field . '.' . $typeValidate => __($message)]);
    } else {
        $requests->validate([$field => $typeValidate], [$field . '.' . $typeValidate => __($message)]);
    }
}

/**
 * Cập nhật lịch sử hệ thống
 * @param string        $action: Hành động
 * @param array         $compact: Mảng giá trị thay đổi ['key' => 'value', 'key2' => 'value2']
 * @param string        $type: tên bảng
 * @param int           $typeId: id của bảng
 * @param string        $idName: tên id của cột (Một vài bảng đặc biệt)
 */
function systemLogs(string $action, array $compact = [], string $type = '', string|int $typeId = '', string $idName = 'id')
{
    app(\DreamTeam\Base\Services\Interfaces\SystemLogServiceInterface::class)
        ->saveLog($action, $compact, $type, $typeId, $idName);
}

/**
 * Hàm tự động tạo mới chuỗi ngẫu nhiên cho lang_code tại bảng language_metas
 * @return string
 */
function getCodeLangMeta()
{
    $rand_string = uniqid();
    return $rand_string;
}

/**
 * Tạo mật khẩu
 * @param number         $length: số lượng ký tự muốn sinh
 * @return string
 */
function passwordGenerate($length = 12)
{
    $characters = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Kiểm tra độ mạnh của mật khẩu
 * @param string         $password: mật khẩu muốn kiểm tra
 * @return number
 */
function passwordStrength($password)
{
    $strength = 0;
    if (strlen($password) < 6) { // Phải có 6 ký tự trở lên
        return $strength;
    } else {
        $strength++;
    }
    if (preg_match("@\d@", $password)) { // Nên có ít nhất 1 số
        $strength++;
    }
    if (preg_match("@[A-Z]@", $password)) { // Nên có ít nhất 1 chữ hoa
        $strength++;
    }
    if (preg_match("@[a-z]@", $password)) { // Nên có ít nhất 1 chữ thường
        $strength++;
    }
    if (preg_match("@\W@", $password)) { // Nên có 1 ký tự đặc biệt
        $strength++;
    }
    if (!preg_match("@\s@", $password)) { // Không nên có ký tự rỗng
        $strength++;
    }
    return $strength; // = 6 - Tùy vào mức độ cần thiết của ứng dụng mà đưa ra số strength yêu cầu
}

/**
 * Tạo chuỗi ngẫu nhiên gồm số, chữ, in hoa
 * @param number         $length: số lượng ký tự muốn sinh
 * @return string
 */
function randString($length = 10)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen($chars);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $size - 1)];
    }
    return $str;
}

/**
 * Xóa Cache
 * @param string         $name: tên trong cache
 */
function pullCache($name)
{
    Cache::pull($name);
}

/**
 * Loại bỏ toàn bộ html tại chuỗi
 * @param string         $string: Chuỗi muốn bỏ
 * @return string
 */
function removeHTML($string)
{
    $string = preg_replace('/<[^>]*>/', ' ', $string);
    $string = str_replace('&nbsp;', ' ', $string);
    $string = str_replace("\r", '', $string);
    $string = str_replace("\n", ' ', $string);
    $string = str_replace("\t", ' ', $string);
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
    return $string;
}

/**
 * Loại bỏ toàn bộ script tại chuỗi
 * @param string         $string: Chuỗi muốn bỏ
 * @return string
 */
function removeScript($string)
{
    $string = preg_replace('/<script.*?\>.*?<\/script>/si', '<br />', $string);
    $string = preg_replace('/on([a-zA-Z]*)=".*?"/si', ' ', $string);
    $string = preg_replace('/On([a-zA-Z]*)=".*?"/si', ' ', $string);
    $string = preg_replace("/on([a-zA-Z]*)='.*?'/si", " ", $string);
    $string = preg_replace("/On([a-zA-Z]*)='.*?'/si", " ", $string);
    return $string;
}

/**
 * Loại bỏ toàn bộ XML tại chuỗi
 * @param string         $string: Chuỗi muốn bỏ
 * @return string
 */
function removeXML($string)
{
    $string = preg_replace('/<xml>.*?<\/xml>/si', '', $string);
    //$string = preg_replace('/<!--.*?-->/si','',$string);
    return $string;
}
function replaceMQ($text)
{
    $text    = str_replace("\'", "'", $text);
    $text    = str_replace("'", "''", $text);
    return $text;
}
function removeMagicQuote($str)
{
    $str = str_replace("\'", "'", $str);
    $str = str_replace("\&quot;", "&quot;", $str);
    $str = str_replace("\\\\", "\\", $str);
    return $str;
}
function removeUTF8BOM($text)
{
    $bom = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

/**
 * Lấy số lượng ký tự trong 1 chuỗi
 * @param string         $string: Chuỗi muốn cắt
 * @param number         $length: Số ký tự muốn cắt (Mặc định là 150)
 * @return string
 */
function cutString($str, $length = 150, $char = " ...")
{
    //Nếu chuỗi cần cắt nhỏ hơn $length thì return luôn
    $strlen    = mb_strlen($str, "UTF-8");
    if ($strlen <= $length) return $str;

    //Cắt chiều dài chuỗi $str tới đoạn cần lấy
    $substr    = mb_substr($str, 0, $length, "UTF-8");
    if (mb_substr($str, $length, 1, "UTF-8") == " ") return $substr . $char;

    //Xác định dấu " " cuối cùng trong chuỗi $substr vừa cắt
    $strPoint = mb_strrpos($substr, " ", 1, "UTF-8");

    //Return string
    if ($strPoint < $length - 20) return $substr . $char;
    else return mb_substr($substr, 0, $strPoint, "UTF-8") . $char;
}

/**
 * Lấy url
 * @param string         $url: Loại url muốn lấy (current: link hiện tại | back: link trang trước đó | full: link full có cả param)
 * @return string
 */
function getUrlLink($url = '')
{
    switch ($url) {
        case 'current':
            $link = url()->current();
            break;
        case 'back':
            $link = url()->previous();
            break;
        case 'full':
            $link = url()->full();
            break;
        default:
            $link = url('');
            break;
    }
    return $link;
}

/**
 * Lấy IP của máy hiện tại
 * @return IP
 */
function getClientIp()
{
    return \Request::ip();
}

/**
 * Lấy toàn bộ html của 1 trang web (Crawl)
 * @param string         $url: Loại url muốn lấy
 * @return string
 */
function getHtmlByCurl($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $str = curl_exec($curl);
    curl_close($curl);
    return $str;
}

/**
 * Nén string và encode cho các trường hợp xuất dữ liệu
 * @param  string       $string
 * @return  string
 */
function compress_encode($string)
{
    return base64_encode(gzcompress($string, 9));
}

/**
 * Giải nén string và decode cho các string qua func compress_encode
 * @param  string       $string
 * @return string
 */
function compress_decode($string)
{
    return gzuncompress(base64_decode($string));
}

/**
 * Verify email qua verify-email.org
 * @param  string       $string
 * @return  boolean
 */
function verifyEmailOrg($email)
{
    return true;
    if (strpos($email, '@yahoo.com.vn')) {
        return true;
    } else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://app.verify-email.org/api/v1/RxmBjTr3s8j5l8pvy4uRBxrKbV60ZR7fJ4AB1TtkpYVCjo7gTP/verify/$email");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);
        $data = json_decode($data, true);
        //$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($data['status'] == 1) {
            return true;
        }
        return false;
    }
}

/**
 * Định dạng giá
 * @param  number       $price: Giá
 * @param  number       $text_default: Text hiển thị giá là 0 để null thì sẽ không hiển thị text này
 * @return  string
 */
function formatPrice($price, $text_default = "Ecommerce::product.price_none", $price_unit = 'đ')
{
    if (function_exists('format_price')) {
        return format_price($price);
    }
    if ($price == 0 && $text_default != null) {
        return __($text_default);
    } else {
        return number_format($price, 0, '.', '.') . $price_unit;
    }
}

/**
 * Định dạng thời gian
 * @param  timestamps   $time: Thời gian dạng timestamps
 * @return  string
 */
function formatTime($time, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($time));
}

/**
 * Định dạng số
 * @param  string       $string: chuỗi chỉ muốn lấy số
 * @return  string
 */
function onlyNumber($string = '')
{
    return trim(preg_replace('/[^0-9]/', '', $string));
}

/**
 * Thời gian sang khoảng thời gian dạng text
 * @param  timestamps   $time: Thời gian dạng timestamps
 * @param  string       $time_format: Định dạng thời gian
 * @param  string       $text_before: Text hiển thị đằng trước
 * @return  string
 */
function changeTimeToText($time, $time_format = 'H:i d/m/Y', $text_before = '')
{
    if (!is_numeric($time)) {
        $time = strtotime($time);
    }
    $time_check = time() - $time;
    if ($time_check < 60) {
        return $text_before . ' ' . round(time() - $time) . ' ' . __('giây trước');
    } elseif ($time_check < 60 * 60) {
        return $text_before . ' ' . round((time() - $time) / 60) . ' ' . __('phút trước');
    } elseif ($time_check < 60 * 60 * 24) {
        return $text_before . ' ' . round(((time() - $time) / 60) / 60) . ' ' . __('giờ trước');
    } else {
        return date($time_format, $time);
    }
}

/* CÁC HÀM LIÊN QUAN ĐẾN ẢNH */

/**
 * Hàm lấy link ảnh trong file public
 * @param  string       $file: Đường dẫn từ đi vào từ public/
 * @return  string
 */
function getImageFile($file = null)
{
    if (!isset($file)) {
        return getImageDefault();
    } else {
        return asset($file);
    }
}

/**
 * Thay đổi resize ảnh
 * @param  string       $linkImg: link ảnh
 * @param  string       $size: Kích thước ảnh
 * @return  string
 */
function resizeImage($linkImg = '', $size = '')
{
    if ((bool)getMediaConfig('media_compressed_size', 1)) {
        if (str_ends_with($linkImg, '.gif') !== false) {
            return $linkImg;
        }

        // Kích thước ảnh hợp lệ tiny 80, small 150, medium 300, large 600
        $array_size = array_keys(config('dreamteam_media.imageRenderSize', []));
        // nếu size không thuộc ảnh hợp lệ thì trả về ảnh đó luôn
        if (!in_array($size, $array_size)) {
            return $linkImg;
        }
        // Vị trí của cái . cuối cùng
        $endS = strrpos($linkImg, '.');
        // Đuôi ảnh
        $img_ext = substr($linkImg, $endS + 1);
        // Đường dẫn ảnh không có đuôi
        $img_path_name = substr($linkImg, 0, $endS);
        // Vị trí dấu - cuối cùng
        $starG = strrpos($img_path_name, '-');
        //tiny,small,medium,large hoặc 1 text bất kỳ nếu link là ảnh mặc định
        $prefix = substr($img_path_name, $starG + 1);
        // Xử lý thêm đuôi
        $resizeLink = $img_path_name . '-' . $size . '.' . $img_ext;
        return addResizeImage($linkImg, $resizeLink, $size);
    } else {
        return $linkImg;
    }
}

/**
 * replace link ảnh cũ sang mới
 * @param  string       $content: nội dung hoặc ảnh cần chuyển
 * @return  string
 */
function replaceImageLink(string $content): string
{
    $content = str_replace(config('dreamteam_media.image_old') ?? [], config('dreamteam_media.image_new') ?? '', $content);
    return $content;
}

/**
 * Lấy ảnh mặc định
 * @param  string       $name: loại ảnh, nếu là load thì sẽ lấy ảnh loading
 * @return  string
 */
function getImageDefault($name = '')
{
    if ($name == "load") {
        return asset('/vendor/core/core/base/img/loading_image.gif');
    } else {
        return RvMedia::getDefaultImage(false) ?? asset('/vendor/core/core/base/img/default_image.png');
    }
}

/**
 * Hàm lấy ảnh chính sau khi đã replace
 * @param  string       $image: link ảnh
 * @param  string       $moduleName: Module name call thumbnail
 * @param  string       $size: Kích thước ảnh
 * @param  string       $imageDefault: Ảnh mặc định nếu ảnh rỗng
 * @return  string
 */
if (!function_exists('getImage')) {
    function getImage(string|null $image = null, string|null $moduleName = '', string|null $size = '', string|null $imageDefault = ''): string|null
    {
        if (is_null($image) || $image == '/') {
            if (is_null($imageDefault)) {
                $image = getImageDefault();
            } else {
                $image = $imageDefault;
            }
        } else {
            $image = replaceImageLink($image);
            $image = RvMedia::getImageUrl($image, $moduleName, $size);
        }
        return $image;
    }
}

/**
 * Định dạng lại keyword để tìm kiếm chính xác hơn
 * @param  string       $keyword: từ tìm kiếm
 * @return  array
 */
function formatKeyword($keyword = '')
{
    if (isset($keyword)) {
        $keyword = $keyword;
        $key = str_replace(" ", "%", $keyword);
        $key = str_replace("\'", "'", $key);
        $key = str_replace("'", "''", $key);
    } else {
        $keyword = $key = "";
    }
    return [
        'keyword' => $keyword,
        'key' => $key,
    ];
}

/**
 * Check xem có quyền hay không
 * @param string            $role
 * @return bool
 */
function checkRole($role)
{
    $has_role = Auth::guard('admin')->user()->hasRole($role);
    return $has_role;
}

/**
 * Phục vụ cho hiển thị active của menu tại admin
 * Check tên route hiện tại và tên mảng route điều kiện để active menu
 * @param string            $route_string: Chuỗi route mặc định
 * @param array             $route_array: mảng route check thêm
 * @return string
 */
function activeMenu($route_string = '', $route_array = [])
{
    $class = '';
    // Gộp chuỗi route với mảng làm một
    $active_group = [];
    if (isset($route_string) && !empty($route_string)) {
        $active_group[] = $route_string;
    }
    if (isset($route_array) && !empty($route_array)) {
        foreach ($route_array as $active) {
            $active_group[] = $active;
        }
    }
    // Kiểm tra route_name hiện tại có trùng chuỗi với chuỗi tại mảng trên hay không
    if (in_array(Route::currentRouteName(), $active_group)) {
        $class = 'mm-active';
    }
    // Trả về tên class
    return $class;
}
function getRole()
{
    $array_role = Auth::guard('admin')->user()->getRole();
    return $array_role;
}

if (!function_exists("hasRolePrivate")) {
    function hasRolePrivate(array $roles, string $item)
    {
        return array_filter($roles, function ($role) use ($item) {
            return strpos($role, $item) !== false;
        });
    }
}

if (!function_exists("convertRole")) {
    function convertRole($role)
    {
        if (is_array($role)) {
            if (count($role)) {
                $role = $role[0];
            } else {
                $role = "";
            }
        }
        return $role;
    }
}
/**
 * Chuyển collection sang dạng mảng [key => value]
 * @param collection    $collect: hàm được lấy ra từ query [get, paginate, ...]
 * @param string        $key_array: tên trường lấy làm key VD: 'id'
 * @param string        $value_array: tên trường lấy làm value VD: name
 * @return array        $array
 */
function collectToArray($collect, $key_array, $value_array)
{
    $array = [];
    foreach ($collect as $key => $value) {
        $array[$value->$key_array] = $value->$value_array;
    }
    return $array;
}

/**
 * Loại bỏ toàn bộ script tại mảng
 * @param array         $array: Mảng [key => value] cần bỏ script tại value
 * @return array
 */
function removeScriptArray($array = [])
{
    $data = [];
    foreach ($array as $key => $value) {
        $data[$key] = removeScript($value);
    }
    return $data;
}

function getStorageHost(): string|null
{
    return CacheHelper::getStorageHost();
}

function addWebp(string|null $image): string
{
    $host = parse_url($image)['host'] ?? null;
    if ($host != getStorageHost()) {
        return $image;
    }
    $image = strtok($image, '?');
    if (preg_match("/\.(webp|gif|svg)$/i", $image) || !((bool)getMediaConfig('media_show_webp', 1))) {
        return $image;
    }

    if (Str::startsWith($image, ['data:image/png;base64,', 'data:image/jpeg;base64,', 'data:image'])) {
        return $image;
    }

    $ext = File::extension($image);
    if ((empty($ext) || is_null($ext)) || $host != getStorageHost()) {
        return $image;
    }
    if (is_null($image) || empty($image) || is_null(File::extension($image)) || !in_array(strtolower(File::extension($image)), ['jpg', 'png', 'jpeg'])) {
        return RvMedia::getDefaultImage();
    }

    if (!RvMedia::isUsingCloud()) {
        return RvMedia::makeWebpImageForFallBack(rtrim(RvMedia::getRelativePathFromUrl($image), '.webp'));
    }
    return $image . '.webp';
}

function addResizeImage($source, $resizeLink, $labelName)
{
    return RvMedia::addThumnailWithSize($source, $resizeLink, $labelName);
}

/**
 * Lấy nhanh ngôn ngữ hiện tại dùng cho cả controller và blade
 */
function getLocale()
{
    return App::getLocale();
}

/**
 * @param string|null $table  Table Name
 * @param int|string|null $tableId  Table Id
 * @param array $options  Data if not exit seos
 * Lấy ra thẻ meta_seos
 */
if (!function_exists('metaSeo')) {
    function metaSeo(string|null $table = '', int|string|null $tableId = '', array $options = [])
    {
        $metaSeo = [];
        // Lấy dữ liệu seo từ options trước
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $metaSeo[$key] = $value;
            }
        }
        $metaSeo['url'] = $options['url'] ?? getUrlLink('current') ?? '';
        // Nếu tồn tại bảng và id thì sẽ query lấy dữ liệu từ DB
        if (!empty($table) && !empty($tableId)) {
            // Query lấy metaSeo
            $cacheKey = 'meta_seo_' . $table . '_' . $tableId;
            $dataSeo = Cache::rememberForever($cacheKey, function () use ($table, $tableId) {
                return app(SeoServiceInterface::class)->findOne([
                    'type'    => $table,
                    'type_id' => $tableId
                ]);
            });

            if (!empty($dataSeo)) {
                // Meta Title
                if (!empty($dataSeo->title)) {
                    $metaSeo['title'] = $dataSeo->title ?? '';
                }
                // Meta Description
                if (!empty($dataSeo->description)) {
                    $metaSeo['description'] = $dataSeo->description ?? '';
                }

                // Meta Social Title
                if (!empty($dataSeo->social_title)) {
                    $metaSeo['social_title'] = $dataSeo->social_title ?? '';
                }
                // Meta Social Description
                if (!empty($dataSeo->social_description)) {
                    $metaSeo['social_description'] = $dataSeo->social_description ?? '';
                }
                // Meta Social Image
                if (!empty($dataSeo->social_image)) {
                    $metaSeo['social_image'] = $dataSeo->social_image ?? '';
                }
                // Meta Robot
                if (!empty($dataSeo->robots)) {
                    $metaSeo['robots'] = $dataSeo->robots ?? '';
                }
                if (!empty($dataSeo->html_head)) {
                    $metaSeo['html_head'] = $dataSeo->html_head ?? '';
                }

                if ($dataSeo->is_custom_canonical && !empty($dataSeo->canonical)) {
                    $metaSeo['is_custom_canonical'] = true;
                    $metaSeo['url'] = $dataSeo->canonical;
                }
            }
        }
        // Các thẻ meta mặc định khác
        $metaSeo['image'] = $options['image'] ?? getImage() ?? '';
        $metaSeo['locale'] = $options['locale'] ?? config('app.language')[getLocale()]['locale'] ?? '';

        View::share('meta_seo', $metaSeo);
        return $metaSeo;
    }
}

/**
 * kiểm tra loại trình duyệt
 */
function checkAgent()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        $is_mobile = false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
    ) {
        $is_mobile = true;
    } else {
        $is_mobile = false;
    }

    if ($is_mobile == true) {
        return 'mobile';
    } else {
        return 'web';
    }
}

/**
 * Lấy option từ cache
 * @param string        $settingName: key của setting
 * @param string        $locale: ngôn ngữ muốn lấy (Mặc định sẽ lấy ngôn ngữ hiện tại)
 * @param bool        $hasLocale: setting có sử dụng đa ngôn ngữ hay không, một vài cấu hình không cần đa ngôn ngữ VD: mail_config
 * @return array        $array
 */
function getOption(string $settingName, string $locale = null, bool $hasLocale = true)
{
    return CacheHelper::getOption($settingName, $locale, $hasLocale);
}

/**
 * Lấy toàn bộ ngôn ngữ thuộc module
 * @param string        $model: Models của module và phải chứa hàm getUrl
 * @param number        $id: id của bản ghi muốn lấy
 * @return array        $language
 */

function getLanguageLink($model, $id, $languageOrigin = null)
{
    $model = new $model;
    $tableName = $model->getTable();
    // Ngôn ngữ bản ghi hiện tại
    if ($languageOrigin instanceof DreamTeam\Base\Models\LanguageMeta) {
        $languageOrigin = $languageOrigin;
    } else {
        $languageOrigin = app(LanguageMetaServiceInterface::class)->findOne([
            'lang_table' => $tableName,
            'lang_table_id' => $id
        ]);
    }
    // Ngôn ngữ toàn bộ bản ghi theo bản ghi hiện tại
    $languageAll = app(LanguageMetaServiceInterface::class)->search(['lang_code' => $languageOrigin->lang_code]);
    // Chuyển toàn bộ ngôn ngữ theo dạng [ 'lang_key' => $id ]
    $languageArray = collectToArray($languageAll, 'lang_locale', 'lang_table_id');
    // Lấy toàn bộ bản ghi
    $data = $model->whereIn('id', $languageAll->pluck('lang_table_id')->toArray())->get();
    // Lấy mảng toàn bộ ngôn ngữ
    $language = [];
    foreach (config('app.language') as $key => $value) {
        $record = $data->where('id', $languageArray[$key] ?? '')->first();
        if (!empty($record)) {
            $currentLang = $languageAll->where('lang_table_id', $record->id)
                ->first();
            setLanguage($currentLang->lang_locale ?? $key);
            $language[$key] = $record->getUrl();
        } else {
            $language[$key] = route('app.home.' . $key);
        }
    }
    // SET ngôn ngữ toàn trang
    setLanguage($languageOrigin->lang_locale);
    if (function_exists('getAndSetWithLocale')) {
        getAndSetWithLocale($languageOrigin->lang_locale);
    }
    $data = [
        'current' => $languageOrigin->lang_locale,
        'language' => $language
    ];
    View::share('language', $data);
    return $data;
}

function getAlt($link = '')
{
    if ($link == '') {
        return 'no-image';
    } else {
        $link_explore = explode('/', $link);
        $img = array_pop($link_explore);
        $alt = explode('.', $img)[0];
        $alt = str_replace(['-tiny', '-small', '-medium', '-large'], '', $alt);
        return $alt;
    }
}

if (!function_exists('replaceMenuLink')) {
    function replaceMenuLink($locale)
    {
        try {
            $menus = app(MenuServiceInterface::class)->search([]);
            if (!count($menus)) return true;
            $dataTable = menu_store()->getAll();
            foreach ($menus as $key => $menuItem) {
                $dataOld = json_decode(base64_decode($menuItem->value));
                $responseValue = replaceMenuLinkItem($dataOld, $dataTable);
                $menuValue = base64_encode(json_encode($responseValue));
                app(MenuServiceInterface::class)->update($menuItem->id, ['value' => $menuValue]);
            }
            event(new ClearCacheEvent());
        } catch (\Exception $e) {
            Log::error('replaceMenuLink error ' . $e->getMessage());
        }
    }

    function replaceMenuLinkItem($configMenu, $dataTable)
    {
        return collect($configMenu)->map(function ($item) use ($dataTable) {
            $appUrl = rtrim(config('app.url'), '/');
            $item = replaceMenuItemLink($item, $dataTable, $appUrl);
            return $item;
        })->toArray();
    }

    function replaceMenuItemLink($item, $dataTable, $appUrl)
    {
        if (isset($item->table)) {
            $item = getSlugFormat($item, $appUrl, $dataTable);
        }
        if (isset($item->children) && count($item->children)) {
            $item->children = collect($item->children)->map(function ($child) use ($dataTable, $appUrl) {
                $child = replaceMenuItemLink($child, $dataTable, $appUrl);
                return $child;
            })->toArray();
        }
        return $item;
    }


    function getSlugFormat($item, $appUrl, $dataTable)
    {
        $arrayLink = explode('/', rtrim($item->link, '/'));
        $link = array_pop($arrayLink);
        $slugItem = str_replace('.html', '', $link);
        if (isset($item->id) && !empty($item->id)) {
            $tableId = $item->id;
        } else {
            $tableId = app(SlugServiceInterface::class)->findOne([
                'table' => $item->table,
                'slug' => $slugItem
            ])->table_id ?? 0;
        }
        $itemUrl = '';
        if (isset($item->table) && $tableId && isset($dataTable[$item->table])) {
            $itemTable = new $dataTable[$item->table]['models'];
            $itemUrl = $itemTable->active()->where('id', $tableId)->first();
            $item->id = $tableId;
        }
        if ($itemUrl) {
            $itemLink = str_replace($appUrl, '', $itemUrl->getUrl());
            $item->link = $itemLink;
        }
        return $item;
    }
}

function getHostFromConfig()
{
    $url = config('app.url');
    $parsedUrl = parse_url($url);
    return $parsedUrl['host'] ?? '';
}

if (!function_exists('per_page')) {
    function per_page($perPage = 0, string $type = null): string
    {
        if ($perPage) return $perPage;

        $readingConfig = getOption('reading', null, false);
        return $readingConfig[$type] ?? 16;
    }
}

if (!function_exists('package_path')) {
    function package_path(?string $path = null): string
    {
        return base_path('packages/' . $path);
    }
}

if (!function_exists('core_path')) {
    function core_path(?string $path = null): string
    {
        return package_path('dreamteam-base/' . $path);
    }
}
if (!function_exists('add_filter')) {
    function add_filter(string|array|null $hook, string|array|Closure $callback, int $priority = 20, int $arguments = 1): void
    {
        Filter::addListener($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('remove_filter')) {
    function remove_filter(string $hook): void
    {
        Filter::removeListener($hook);
    }
}

if (!function_exists('add_action')) {
    function add_action(string|array|null $hook, string|array|Closure $callback, int $priority = 20, int $arguments = 1): void
    {
        Action::addListener($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(...$args)
    {
        return Filter::fire(array_shift($args), $args);
    }
}

if (!function_exists('do_action')) {
    function do_action(...$args): void
    {
        Action::fire(array_shift($args), $args);
    }
}

if (!function_exists('get_hooks')) {
    function get_hooks(?string $name = null, bool $isFilter = true): array
    {
        if ($isFilter) {
            $listeners = Filter::getListeners();
        } else {
            $listeners = Action::getListeners();
        }

        if (empty($name)) {
            return $listeners;
        }

        return Arr::get($listeners, $name, []);
    }
}

if (!function_exists('setting')) {
    function setting(?string $key = null, $default = null)
    {
        if (!empty($key)) {
            try {
                return app(SettingStore::class)->get($key, $default);
            } catch (Throwable) {
                return $default;
            }
        }

        return Setting::getFacadeRoot();
    }
}
if (!function_exists('is_in_admin')) {
    function is_in_admin(): bool
    {
        $prefix = BaseHelper::getAdminPrefix();

        $segments = array_slice(request()->segments(), 0, count(explode('/', $prefix)));

        $isInAdmin = implode('/', $segments) === $prefix;

        return $isInAdmin;
    }
}
if (!function_exists('admin_menu')) {
    function admin_menu(): DashboardMenuSupport
    {
        return DashboardMenu::getFacadeRoot();
    }
}

if (!function_exists('menu_store')) {
    function menu_store(): MenuStoreSupport
    {
        return MenuStore::getFacadeRoot();
    }
}

if (!function_exists('get_cms_version')) {
    function get_cms_version(): string
    {
        $version = '...';

        try {
            $core = BaseHelper::getFileData(core_path('core.json'));

            return Arr::get($core, 'version', $version);
        } catch (Exception) {
            return $version;
        }
    }
}
if (!function_exists('get_core_version')) {
    function get_core_version(): string
    {
        return '1.0';
    }
}

if (!function_exists('formatDataSystermLog')) {
    function formatDataSystermLog($data): array
    {
        return array_map(function ($item) {
            if (isset($item['created_at'])) {
                $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
                $item['updated_at'] = date('Y-m-d H:i:s', strtotime($item['updated_at']));
            }
            if (isset($item['time'])) {
                $item['time'] = date('Y-m-d H:i:s', strtotime($item['time']));
            }
            return $item;
        }, $data);
    }
}

if (!function_exists('getFilterOptions')) {
    function getFilterOptions(string $const, string $tableType, $table)
    {
        if (defined($const)) {
            if ($table->tableOption) {
                return json_decode($table->tableOption->value ?? '', 1);
            }
        }
        return [];
    }
}

if (!function_exists('getPerMarkLink')) {
    function getPerMarkLink(string $key, int|string $default = ''): int|string|null|array
    {
        $permarkLinks = getOption('link_custom', null, false);
        return $permarkLinks[$key] ?? $default;
    }
}

if (!function_exists('replaceImageInDataContent')) {
    function replaceImageInDataContent($content, $replaceLinks, $hasCrawlImage = false, $typeJob = null)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content);
        libxml_use_internal_errors(true);

        $images = $dom->getElementsByTagName('img');
        if (!count($images)) return $content;

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if ($src && $hasCrawlImage) {
                \DreamTeam\Base\Jobs\UploadImageCrawl::dispatch($src, $replaceLinks, false, false, $typeJob)->onQueue('low');
            }
            $src = str_replace($replaceLinks, '', $src);
            $image->setAttribute('src', $src);
        }
        // Lấy HTML sau khi đã thay đổi
        $content = $dom->saveHTML();
        $content = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body|meta))[^>]*>\s*~i', '', $content);

        return $content;
    }
}

if (!function_exists('uploadImageCrawlFromLink')) {
    function uploadImageCrawlFromLink($image, $replaceLinks)
    {
        try {
            $image = strtok($image, '?');
            $image = getFullUrlImage($image, $replaceLinks);
            $parsedUrl = parse_url($image);
            if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host']) || !isset($parsedUrl['path'])) {
                return false;
            }
            $image = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

            if (empty($image)) return true;
            $image = (string) $image;
            if (!Str::contains($image, ['http://', 'https://'])) {
                $image = rtrim($replaceLinks[0], '/') . '/' . ltrim($image, '/');
            }
            $srcImage = str_replace($replaceLinks, '', $image);
            $path = urldecode(trim($srcImage));
            if (Storage::exists($path)) {
                return $path;
            }
            $folderPath = explode('/', $path);
            array_pop($folderPath);
            $result = RvMedia::uploadFromUrl(urldecode(trim($image)), 0, implode('/', $folderPath));
            if (!($result['error'] ?? ''))
                return Storage::url($result['data']->url);
        } catch (\Exception $e) {
            Log::error('Upload image crawl error ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
if (!function_exists('getFullUrlImage')) {
    function getFullUrlImage($image, $replaceLinks)
    {
        if (is_array($image)) {
            $list = [];
            foreach ($image as $key => $value) {
                if (!(preg_match('#^http#', $value) === 1)) {
                    $isStartingWithDoubleSlash = (preg_match('#^//#', $value) === 1);
                    if ($isStartingWithDoubleSlash) {
                        $value = 'https:' . $value;
                    } else {
                        $value = ($replaceLinks[0] ?? '') . '/' . ltrim($value, '/');
                    }
                }
                $list[] = $value;
            }
            return $list;
        } else {
            if (!(preg_match('#^http#', $image) === 1)) {
                $isStartingWithDoubleSlash = (preg_match('#^//#', $image) === 1);
                if ($isStartingWithDoubleSlash) {
                    $image = 'https:' . $image;
                } else {
                    $image = ($replaceLinks[0] ?? '') . '/' . ltrim($image, '/');
                }
            }
            return $image;
        }
    }
}

if (!function_exists('removeDomainImageCrawl')) {
    function removeDomainImageCrawl($image, $replaceLinks, $hasCrawlImage = true)
    {
        if (empty($image)) return '';

        if (!$hasCrawlImage) return $image;

        if (is_array($image)) {
            $list = [];
            foreach ($image as $key => $value) {
                if (!(preg_match('#^http#', $value) === 1)) {
                    $isStartingWithDoubleSlash = (preg_match('#^//#', $value) === 1);
                    if ($isStartingWithDoubleSlash) {
                        $value = 'https:' . $value;
                    } else {
                        $value = ($replaceLinks[0] ?? '') . '/' . ltrim($value, '/');
                    }
                }
                $list[] = urldecode(str_replace(getReplaceLink($value), '', $value));
            }
            return $list;
        } else {
            if (!(preg_match('#^http#', $image) === 1)) {
                $isStartingWithDoubleSlash = (preg_match('#^//#', $image) === 1);
                if ($isStartingWithDoubleSlash) {
                    $image = 'https:' . $image;
                } else {
                    $image = ($replaceLinks[0] ?? '') . '/' . ltrim($image, '/');
                }
            }
            return urldecode(str_replace(getReplaceLink($image), '', $image));
        }
    }
}

if (!function_exists('getReplaceLink')) {
    function getReplaceLink($linkOld)
    {
        $linkOld = rtrim($linkOld, '/');
        $parseLink = parse_url($linkOld);
        // check link https | http
        if (isset($parseLink['scheme']) && $parseLink['scheme'] == 'https' && isset($parseLink['host'])) {
            $linkOld = (string) $parseLink['scheme'] . '://' . $parseLink['host'];
            $linkOldHttp = (string) str_replace('https://', 'http://', $linkOld);
        } else if (isset($parseLink['scheme']) && $parseLink['scheme'] == 'http' && isset($parseLink['host'])) {
            $linkOldHttp = (string) $parseLink['scheme'] . '://' . $parseLink['host'];
            $linkOld = (string) str_replace('http://', 'https://', $linkOldHttp);
        } else if (!isset($parseLink['scheme']) && isset($parseLink['path'])) {
            if (strpos($parseLink['path'], 'https://')) {
                $linkOld = (string) $parseLink['path'];
                $linkOldHttp = (string) str_replace('https://', 'http://', $linkOld);
            } else if (strpos($parseLink['path'], 'http://')) {
                $linkOldHttp = (string) $parseLink['path'];
                $linkOld = (string) str_replace('http://', 'https://', $linkOldHttp);
            } else {
                $linkOld = (string) 'https://' . $parseLink['path'];
                $linkOldHttp = (string) 'http://' . $parseLink['path'];
            }
        }
        return [$linkOld, $linkOldHttp];
    }
}

if (!function_exists('getGoogleConversion')) {
    function getGoogleConversion(string $key, $default = null): string|array|null
    {
        $data = getOption('google_conversion', '', false);
        return $data[$key] ?? $default;
    }
}

if (!function_exists('getEmailContent')) {
    function getEmailContent(string $module, string $emailName): array|null
    {
        $emails = getOption('email_contents');
        return $emails[$module][$emailName] ?? null;
    }
}

if (!function_exists('renderVote')) {
    function renderVote($id, $type, $location = null, $item = null)
    {
        if (!is_plugin_active('vote')) {
            return '';
        }
        $showLocation = getVoteLocation($type);
        if ($location !== null && $showLocation !== $location) {
            return '';
        }

        $html = apply_filters(FILTER_RENDER_VOTE_RESULT, $type, $id, $item ?? '');
        return $html ?? '';
    }
}

/**
 * Hàm tạo mã màu HEX không trùng
 */
function generateUniqueColor(&$usedColors)
{
    do {
        $color = sprintf("#%06X", mt_rand(0, 0xFFFFFF)); // Tạo mã màu HEX ngẫu nhiên
    } while (in_array($color, $usedColors)); // Kiểm tra trùng lặp

    $usedColors[] = $color; // Lưu vào danh sách đã dùng
    return $color;
}

function getAndSetWithLocale($lang)
{
    $config_general = getOption('general', $lang);
    View::share('config_general', $config_general);

    $themeConfig = getOption('theme_config', null, false);
    View::share('themeConfig', $themeConfig);
    //other
    $configOther = getOption('other', $lang);
    View::share('configOther', $configOther);
    // cache mã chuyển đổi
    $config_code = getOption('code', null, false);
    View::share('config_code', $config_code);
    // tổng quan
    $configOverview = getOption('overview', null, false);
    View::share('configOverview', $configOverview);
    $siteLanguage = getOption('siteLanguage', null, false);
    View::share('siteLanguage', $siteLanguage);
    // cache reading
    $configReading = getOption('reading', null, false);
    // luôn chặn với web test
    if (isset($configReading['no_index']) && $configReading['no_index'] == 0) {
        $configReading['no_index'] = 0;
    } else if (str_contains(config('app.url'), '.chanh.in')) {
        $configReading['no_index'] = 1;
    }
    View::share('configReading', $configReading);
}
