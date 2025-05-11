<?php

namespace DreamTeam\Ecommerce\Providers;

use Illuminate\Support\ServiceProvider;
use DreamTeam\Ecommerce\Services\Interfaces\OrderServiceInterface;
use DreamTeam\Ecommerce\Repositories\Eloquent\OrderDetailRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\FlashSaleProductRepository;
use DreamTeam\Ecommerce\Services\Interfaces\ProductServiceInterface;
use DreamTeam\Ecommerce\Services\ProductService;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use DreamTeam\Ecommerce\Repositories\Eloquent\AttributeValueRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\BrandRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\LocationRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\RegionRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\FilterDetailRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\OrderRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\OrderHistoryRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\CustomerRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\FilterRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductAttributeRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductCategoryMapRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductCategoryRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductFilterRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductVariantRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductWarehousesRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\VariantAttributeValueRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\FilterProductCategoryMapRepository;
use DreamTeam\Ecommerce\Services\Interfaces\ProductCategoryServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\ProductVariantServiceInterface;
use DreamTeam\Ecommerce\Services\ProductCategoryService;
use DreamTeam\Ecommerce\Services\ProductVariantService;
use DreamTeam\Ecommerce\Services\Interfaces\BrandServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\LocationServiceInterface;
use DreamTeam\Ecommerce\Services\Interfaces\RegionServiceInterface;
use DreamTeam\Ecommerce\Services\LocationService;
use DreamTeam\Ecommerce\Services\RegionService;
use DreamTeam\Ecommerce\Services\OrderService;
use DreamTeam\Ecommerce\Services\Interfaces\FilterServiceInterface;
use DreamTeam\Ecommerce\Services\FilterService;
use DreamTeam\Ecommerce\Models\Order;
use DreamTeam\Ecommerce\Models\OrderDetail;
use DreamTeam\Ecommerce\Services\Interfaces\OrderDetailServiceInterface;
use DreamTeam\Ecommerce\Services\OrderDetailService;
use DreamTeam\Ecommerce\Enums\OrderStatusEnum;
use DreamTeam\Ecommerce\Models\Product;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductLocationRepository;
use DreamTeam\Ecommerce\Repositories\Eloquent\ProductScheduleRepository;
use DreamTeam\Ecommerce\Services\BrandService;

class EcommerceServiceProvider extends ServiceProvider
{
    /**
     * Register config file here
     * alias => path
     */
    private $configFile = [
        'DreamTeamModule' => 'DreamTeamModule.php',
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        //
    ];

    /**
     * Register middleare file here
     * name => middleware
     */
    protected $middleare = [];

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();
        // boot commands
        $this->commands($this->commands);

        $this->app->bind(BrandServiceInterface::class, function ($app) {
            return new BrandService(
                new BrandRepository(),
            );
        });

        $this->app->bind(ProductServiceInterface::class, function ($app) {
            return new ProductService(
                new ProductRepository(),
                new BrandRepository(),
                new ProductFilterRepository(),
                new ProductScheduleRepository()
            );
        });

        $this->app->bind(LocationServiceInterface::class, function ($app) {
            return new LocationService(
                new LocationRepository(),
                new ProductLocationRepository(),
            );
        });

        $this->app->bind(FilterServiceInterface::class, function ($app) {
            return new FilterService(
                new FilterRepository(),
                new FilterDetailRepository,
                new ProductFilterRepository,
            );
        });
        $this->app->bind(OrderDetailServiceInterface::class, function ($app) {
            return new OrderDetailService(
                new OrderDetailRepository()
            );
        });

        $this->app->bind(OrderServiceInterface::class, function ($app) {
            return new OrderService(
                new OrderRepository(),
                new OrderDetailRepository(),
                new OrderHistoryRepository(),
                new CustomerRepository(),
            );
        });

    }

    public function boot()
    {
        $this->registerModule();

        $this->publish();

        $this->registerMiddleware();

        $this->app->booted(function () {
            if (defined('FILTER_THUMBNAIL_MODULE_NAME')) {
                add_filter(FILTER_THUMBNAIL_MODULE_NAME, function(array $data) {
                    $data['products'] = ProductServiceInterface::class;
                    $data['brands'] = BrandServiceInterface::class;
                    return $data;
                }, 120, 1);
            }

            if (defined('FILTER_LIST_DATA_TABLE_QUERY')) {
                add_filter(FILTER_LIST_DATA_TABLE_QUERY, [$this, 'ecommerceFilterDataTable'], 135, 5);
            }

            if (defined('ROLLBACK_DATA_FROM_LOG')) {
                add_filter(ROLLBACK_DATA_FROM_LOG, [$this, 'ecommerceRollBackFromLog'], 138, 3);
            }

        });

        $this->adminMenu();
    }

    private function registerModule()
    {
        $modulePath = __DIR__ . '/../../';
        $moduleName = 'Ecommerce';

        // boot route
        if (File::exists($modulePath . "routes/routes.php")) {
            $this->loadRoutesFrom($modulePath . "/routes/routes.php");
        }
        if (File::exists($modulePath . "routes/general.php")) {
            $this->loadRoutesFrom($modulePath . "/routes/general.php");
        }
        if ($this->checkAgent() == 'mobile') {
            if (File::exists($modulePath . "routes/mobile.php")) {
                $this->loadRoutesFrom($modulePath . "/routes/mobile.php");
            }
        } else {
            if (File::exists($modulePath . "routes/web.php")) {
                $this->loadRoutesFrom($modulePath . "/routes/web.php");
            }
        }

        // boot migration
        if (File::exists($modulePath . "migrations")) {
            $this->loadMigrationsFrom($modulePath . "migrations");
        }

        // boot languages
        if (File::exists($modulePath . "resources/lang")) {
            $this->loadTranslationsFrom($modulePath . "resources/lang", $moduleName);
            $this->loadJSONTranslationsFrom($modulePath . 'resources/lang');
        }

        // boot views
        if (File::exists($modulePath . "resources/views")) {
            $this->loadViewsFrom($modulePath . "resources/views", $moduleName);
        }

        // boot all helpers
        if (File::exists($modulePath . "helpers")) {
            // get all file in Helpers Folder
            $helper_dir = File::allFiles($modulePath . "helpers");
            // foreach to require file
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require_once $file;
            }
        }
    }

    /*
    * publish dự án ra ngoài
    * publish config File
    * publish assets File
    */
    public function publish()
    {
        //
    }

    /*
    * Đăng ký config cho từng Module
    * $this->configFile
    */
    public function mergeConfig()
    {
        foreach ($this->configFile as $alias => $path) {
            $config = $this->app['config']->get($alias, []);
            $this->app['config']->set($alias, $this->mergeArrayConfigs(require __DIR__ . "/../../config/" . $path, $config));
        }
    }

    /**
     * Merge config để lấy ra mảng chung
     * Ưu tiên lấy config ở app
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeArrayConfigs(array $original, array $merging)
    {
        $array = array_merge($original, $merging);
        foreach ($original as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (!Arr::exists($merging, $key)) {
                continue;
            }
            if (is_numeric($key)) {
                continue;
            }
            $array[$key] = $this->mergeArrayConfigs($value, $merging[$key]);
        }
        return $array;
    }

    /**
     * Đăng ký Middleare
     */
    public function registerMiddleware()
    {
        foreach ($this->middleare as $key => $value) {
            $this->app['router']->pushMiddlewareToGroup($key, $value);
        }
    }

    private function checkAgent()
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
            return 'desktop';
        }
    }

    private function adminMenu()
    {
        $this->app['events']->listen(RouteMatched::class, function () {
            if (is_in_admin()) {
                $count = $this->countOrderNumberNew();
            }
            admin_menu()
                ->registerItem([
                    'id'             => 'plugins_ecommerce_orders',
                    'priority'       => 5,
                    'parent_id'      => '',
                    'type'           => 'single',
                    'name'           => 'Ecommerce::admin.order',
                    'icon'           => 'bx bx-list-check',
                    'route'          => 'admin.orders.index',
                    'role'           => 'orders_index',
                    'permissions'    => ['orders_index'],
                    'count'          => $count ?? 0,
                ])
                ->registerItem([
                    'id'             => 'plugins_ecommerce',
                    'priority'       => 8,
                    'parent_id'      => '',
                    'type'           => 'multiple',
                    'route'          => '',
                    'name'           => 'Ecommerce::admin.product',
                    'icon'           => 'bx bx-store',
                ])
                ->registerItem([
                    'id'          => 'plugins_ecommerce_product',
                    'priority'    => 0,
                    'parent_id'   => 'plugins_ecommerce',
                    'name'        => 'Ecommerce::admin.list_product',
                    'route'       => 'admin.products.index',
                    'role'        => 'products_create',
                    'active'      => ['admin.products.edit', 'admin.products.create'],
                    'permissions' => ['products_create']
                ])
                ->registerItem([
                    'id'          => 'plugins_ecommerce_filter',
                    'priority'    => 5,
                    'parent_id'   => 'plugins_ecommerce',
                    'name'        => 'Ecommerce::admin.filter_2',
                    'route'       => 'admin.filters.index',
                    'role'        => 'filters_index',
                    'active'      => ['admin.filters.edit', 'admin.filters.create'],
                    'permissions' => ['filters_index']
                ])
                ->registerItem([
                    'id'          => 'plugins_ecommerce_brand',
                    'priority'    => 7,
                    'parent_id'   => 'plugins_ecommerce',
                    'name'        => 'Ecommerce::admin.brand',
                    'route'       => 'admin.brands.index',
                    'role'        => 'brands_index',
                    'active'      => ['admin.brands.edit', 'admin.brands.create'],
                    'permissions' => ['brands_index']
                ])
                ->registerItem([
                    'id'          => 'plugins_ecommerce_location',
                    'priority'    => 7,
                    'parent_id'   => 'plugins_ecommerce',
                    'name'        => 'Ecommerce::admin.location',
                    'route'       => 'admin.locations.index',
                    'role'        => 'locations_index',
                    'active'      => ['admin.locations.edit', 'admin.locations.create'],
                    'permissions' => ['locations_index']
                ]);
        });
    }

    public function ecommerceFilterDataTable($datas, string $tableName, array $conditions, array $customConditions, Request $request)
    {
        if ($tableName == (new Order())->getTable() && count($customConditions)) {
            if (!empty($customConditions['customer_name'] ?? '') || !empty($customConditions['customer_phone'] ?? '')) {
                $datas = $datas->join('customers', 'customers.id', 'orders.customer_id');

                if (!empty($customConditions['customer_name'] ?? '')) {
                    $datas = $datas->where('customers.name', 'LIKE', "%" . str_replace(' ', '%', $customConditions['customer_name']) . '%');
                }
                if (!empty($customConditions['customer_phone'] ?? '')) {
                    $datas = $datas->where('customers.phone', 'LIKE', '%' . $customConditions['customer_phone'] . '%');
                }
            }

            if (!empty($customConditions['order_status'] ?? '')) {
                $datas = $datas->where('orders.status', $request->order_status);
            }

            if (!empty($customConditions['payment_method'] ?? '') || !empty($customConditions['payment_status'] ?? '')) {
                $datas = $datas->with('payment')->join('payments', 'payments.id', 'orders.payment_id');

                if (!empty($customConditions['payment_method'] ?? '')) {
                    $datas = $datas->where('payments.payment_channel', $customConditions['payment_method']);
                }

                if (!empty($customConditions['payment_status'] ?? '')) {
                    $datas = $datas->where('payments.status', $customConditions['payment_status']);
                }
            }
            return $datas->select('orders.*');
        } else if ($tableName == (new OrderDetail())->getTable() && count($customConditions)) {

            $datas = $datas->join('orders', 'orders.id', 'order_details.order_id')
                ->whereIn('orders.status', OrderStatusEnum::toArray());

            if (!empty($customConditions['customer_name'] ?? '') || !empty($customConditions['customer_phone'] ?? '')) {
                $datas = $datas->join('customers', 'customers.id', 'orders.customer_id');

                if (!empty($customConditions['customer_name'] ?? '')) {
                    $datas = $datas->where('customers.name', 'LIKE', '%' . str_replace(' ', '%', $customConditions['customer_name']) . '%');
                }
                if (!empty($customConditions['customer_phone'] ?? '')) {
                    $datas = $datas->where('customers.phone', 'LIKE', '%' . $customConditions['customer_phone'] . '%');
                }
            }
            if (isset($customConditions['order_code']) && $customConditions['order_code']) {
                $datas = $datas->where('orders.code', $customConditions['order_code']);
            }
            if (!empty($customConditions['payment_method'] ?? '') || !empty($customConditions['payment_status'] ?? '')) {
                $datas = $datas->join('payments', 'payments.id', 'orders.payment_id');
                if (!empty($customConditions['payment_method'] ?? '')) {
                    $datas = $datas->where('payments.payment_channel', $customConditions['payment_method']);
                }

                if (!empty($customConditions['payment_status'] ?? '')) {
                    $datas = $datas->where('payments.status', $customConditions['payment_status']);
                }
            }
            if (isset($customConditions['status']) && $customConditions['status']) {
                $datas = $datas->where('orders.status', $customConditions['status']);
            }
            if (isset($customConditions['created_at_start']) && !empty($customConditions['created_at_start']) && isset($customConditions['created_at_end']) && !empty($customConditions['created_at_end'])) {
                $datas = $datas->whereBetween('orders.created_at', [$customConditions['created_at_start'], $customConditions['created_at_end']]);
            }

            if (!empty($customConditions['product_name'] ?? '')) {
                $datas = $datas->where('order_details.product_name', 'LIKE', "%" . str_replace(' ', '%', $customConditions['product_name']) . '%');
            }

            return $datas->select('order_details.*');
        } else if ($tableName == (new Product())->getTable() && count($customConditions)) {
            if (isset($customConditions['category_id'])) {
                // get All child id by category
                $currentCategory = app(ProductCategoryServiceInterface::class)->findOneWith(['childrenCates'], ['id' => intval($customConditions['category_id'])]);
                if ($currentCategory) {
                    $categoryIds = $currentCategory->getChildID($currentCategory);
                    $datas = $datas->where(function ($query) use ($categoryIds) {
                        $query->whereIn('products.category_id', $categoryIds)
                            ->orWhereHas('productCategoryMaps', function ($dubQuery) use ($categoryIds) {
                                $dubQuery->whereIn('product_category_id', $categoryIds);
                            });
                    });
                }
            }
        }
        return $datas;
    }

    private function countOrderNumberNew()
    {
        return Cache::rememberForever('order_new_number', function() {
            return app(OrderServiceInterface::class)
            ->getMultipleWithFromConditions([], ['status' => \DreamTeam\Ecommerce\Enums\OrderStatusEnum::STATUS_NEW], 'id', 'desc')
            ->count();
        });
    }

    public function ecommerceRollbackFromLog($response, string $type, array $dataOld)
    {
        if ($type == 'brands') {
            $this->app->make(BrandServiceInterface::class)->insert($dataOld);
            return ['success' => true];
        } else if ($type == 'products') {
            $this->app->make(ProductServiceInterface::class)->rollbackFromLog($dataOld);
            return ['success' => true];
        } else if ($type == 'filters') {
            $filterDetails = $dataOld['filterDetails'];
            $productFilters = $dataOld['productFilters'];
            $filterProductCategoryMaps = $dataOld['filterProductCategoryMaps'];
            if (count($filterDetails)) {
                $filterDetails = formatDataSystermLog($filterDetails);
                $this->app->make(FilterServiceInterface::class)->insertMultipleFilterDetail($filterDetails);
            }
            if (count($productFilters)) {
                $this->app->make(FilterServiceInterface::class)->insertMultipleProductFilter($productFilters);
            }
            if (count($filterProductCategoryMaps)) {
                $this->app->make(FilterServiceInterface::class)->insertMultipleFilterMapCategory($filterProductCategoryMaps);
            }
            unset($dataOld['filterDetails']);
            unset($dataOld['productFilters']);
            unset($dataOld['filterProductCategoryMaps']);
            $this->app->make(FilterServiceInterface::class)->insert($dataOld);
            return ['success' => true];
        } else if ($type == 'filter_details') {
            $productFilters = $dataOld['productFilters'];
            if (count($productFilters)) {
                $this->app->make(FilterServiceInterface::class)->insertMultipleProductFilter($productFilters);
            }
            unset($dataOld['productFilters']);
            $this->app->make(FilterServiceInterface::class)->insertMultipleFilterDetail($dataOld);
            $type = 'filters';
            $typeID = $dataOld['filter_id'];
            return ['success' => true, 'type' => $type, 'typeID' => $typeID];
        }
        return $response;
    }

}
