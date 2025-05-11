<?php

use Illuminate\Support\Facades\Cache;
use DreamTeam\Ecommerce\Facades\EcommerceHelper;
use DreamTeam\Ecommerce\Services\Interfaces\ProductCategoryServiceInterface;
use DreamTeam\Base\Enums\BaseStatusEnum;

if (!function_exists('getAllProductCategoryByLang')) {
    function getAllProductCategoryByLang(string $lang)
    {
        return Cache::remember('productCategories_' . $lang, 3600, function () use ($lang) {
            return app(ProductCategoryServiceInterface::class)
                ->getWithMultiFromConditions(
                    ['allChildrenCates', 'parentCate'],
                    [
                        'status' => ['=' => BaseStatusEnum::ACTIVE],
                        'parent_id' => ['=' => 0],
                    ],
                    'order',
                    'asc',
                    true,
                    'product_categories.*',
                    $lang
                );
        });
    }
}

function formatCategories($categories)
{
    $result = [];
    foreach ($categories as $category) {
        $children = formatCategories($category->allChildrenCates);
        $item = [
            'value' => $category->id,
            'label' => trim(html_entity_decode($category->name)),
        ];
        if (count($children)) {
            $item['children'] = $children;
        }
        $result[] = $item;
    }
    return $result;
}

function formatBreadCrumbs($category, $parents, $device = 'app')
{
    $parent = $category->allParentCates;
    if ($parent) {
        $pushItem = [
            'name' => $parent->name,
            'link' => $parent->getUrl($device),
        ];
        array_unshift($parents, $pushItem);
        if ($parent->parent_id != 0) {
            $parents = formatBreadCrumbs($parent, $parents);
        }
        return $parents;
    } else {
        return $parents;
    }
}

function getOrderFilter($order)
{
    switch ($order) {
        case 'price_low':
            $search = '(CASE WHEN price > 0 THEN price ELSE price_old END) asc';
            break;
        case 'price_high':
            $search = '(CASE WHEN price > 0 THEN price ELSE price_old END) desc';
            break;
        case 'lasted':
            $search = 'products.id desc';
            break;
        case 'olded':
            $search = 'products.id asc';
            break;
        case 'asc':
            $search = 'name asc';
            break;
        case 'desc':
            $search = 'name desc';
            break;

        default:
            $search = '';
            break;
    }
    return $search;
}
function randomCodeOrder()
{
    $string = '0123456789abcdefghijklmnopqrstuvwxyz';
    $code = strtoupper(substr(str_shuffle(str_repeat($string, 8)), 0, 8));
    $code_order = \DreamTeam\Ecommerce\Models\Order::pluck('code')->toArray();
    $check = true;
    while ($check) {
        if (!in_array($code, $code_order)) {
            $check = false;
        } else {
            $check = true;
            $code = strtoupper(substr(str_shuffle(str_repeat($string, 8)), 0, 8));
        }
    }
    return $code;
}

if (!function_exists('get_ecommerce_setting')) {
    function get_ecommerce_setting(string $key, bool|int|string|null $default = ''): array|int|string|null
    {
        $addvancedEcommerce = getOption('ec_advanced', null, false);
        return $addvancedEcommerce[$key] ?? $default;
    }
}

if (!function_exists('get_ecommerce_summary_setting')) {
    function get_ecommerce_summary_setting(string $key, bool|int|string|array|null $default = ''): array|int|string|null
    {
        $summaryProduct = getOption('summary_product');
        return $summaryProduct[$key] ?? $default;
    }
}

if (!function_exists('get_payment_method_setting')) {
    function get_payment_method_setting(string $key, bool|int|string|array|null $default = ''): array|int|string|null
    {
        $paymentMethod = getOption('ec_payment_method');
        return $paymentMethod['payment_method'][$key] ?? $default;
    }
}

if (!function_exists('get_shipping_method_setting')) {
    function get_shipping_method_setting(string $key, bool|int|string|array|null $default = ''): array|int|string|null
    {
        $shippingMethod = getOption('ec_shipping_method', null, false);
        return $shippingMethod['shipping_method'][$key] ?? $default;
    }
}

if (!function_exists('get_email_ecommerce')) {
    function get_email_ecommerce(string $key, bool|int|string|array|null $default = ''): array|int|string|null
    {
        $contentEmail = getOption('interface_email_ecommerce');
        return $contentEmail[$key] ?? $default;
    }
}
if (!function_exists('get_shipping_setting')) {
    function get_shipping_setting(string $key, $type = null, $default = null): string|array|null
    {
        if (!empty($type)) {
            $key = 'shipping_' . $type . '_' . $key;
        } else {
            $key = 'shipping_' . $key;
        }

        return setting($key, $default);
    }
}

if (!function_exists('getBestReview')) {
    function getBestReview($productId)
    {
        if (!is_plugin_active('comment')) return null;
        return Cache::rememberForever('best_review_product_' . $productId, function () use ($productId) {
            return \DreamTeam\Comment\Models\Comment::where('type', 'products')
                ->where('status', 1)
                ->orderBy('star', 'desc')
                ->where('type_id', $productId)
                ->first();
        });
    }
}

