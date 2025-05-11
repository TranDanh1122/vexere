<?php

namespace DreamTeam\Ecommerce\Http\Controllers;

use DreamTeam\Base\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use ListData;
use \DreamTeam\Ecommerce\Models\Order;
use DreamTeam\Ecommerce\Services\Interfaces\OrderServiceInterface;
use Illuminate\Support\Facades\DB;
use DreamTeam\Ecommerce\Enums\OrderStatusEnum;
use DreamTeam\Base\Enums\BaseStatusEnum;

class OrderController extends AdminController
{
    protected OrderServiceInterface $orderService;
    protected array $orderStatus;
    protected array $payment_method;
    protected array $payment_status;
    protected bool $hasPayment;

    function __construct(
        OrderServiceInterface $orderService,
    )
    {
        $this->table_name = (new Order)->getTable();
        $this->module_name = 'Ecommerce::admin.order';
        $this->has_seo = false;
        $this->has_locale = false;

        $this->orderService = $orderService;

        parent::__construct();

        $this->orderStatus = OrderStatusEnum::labels();

        $this->payment_method = [];
        $this->payment_status = [];
        $this->hasPayment = false;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $with = ['customer', 'orderDetail'];
        $listdata = new ListData(
                $requests,
                $this->orderService,
                $this->table_name,
                'Ecommerce::admin.orders.table.index',
                $with,
                false,
                $this->has_locale,
                30,
                [ $this->table_name.'.id' => 'desc' ]
            );
        // Build Form tìm kiếm
        $listdata->search('code', __('Ecommerce::admin.order_code'), 'string');
        $listdata->search('user_id', __(''), 'hidden');
        $listdata->search('customer_name', __('Ecommerce::admin.customer_name'), 'custom_conditions');
        $listdata->search('customer_phone', __('Ecommerce::admin.phone'), 'custom_conditions');
        $listdata->search('order_status', __('Ecommerce::admin.status'), 'custom_conditions_array', $this->orderStatus);
        $listdata->search('created_at', __('Ecommerce::admin.created_at'), 'range');
        // Build bảng
        $listdata->add('id', __('Ecommerce::admin.order_code'), 0);
        $listdata->add('created_at', __('Ecommerce::admin.time'), 0);
        $listdata->add('', __('Ecommerce::admin.customer'), 0);
        $listdata->add('status', __('Ecommerce::admin.status'),  'status');
        $listdata->add('total_price', __('Ecommerce::admin.order_value'), 0);
        $listdata->no_trash();
        $listdata->no_add();
        // Trả về views
        $options = $this->orderStatus;
        return $listdata->render(compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        exit('Tính năng đang phát triển!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests)
    {
        exit('Tính năng đang phát triển!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Toàn bộ admin_user để hiển thị cho lịch xử
        $admin_user_query = DB::table('admin_users')->get();
        $admin_users = [];
        foreach ($admin_user_query as $value) {
            $admin_users[$value->id] = $value->display_name ?? $value->name;
        }
        // Lấy bản ghi
        $order = $this->orderService->findOneWith(['orderDetail', 'customer'], compact('id'), true);
        // Thông tin sản phẩm
        $order_details = $order->orderDetail ?? [];
        // Lịch sử hành động của đơn hàng
        $order_histories = $this->orderService->getOrderHistory(['order_id' => $order->id], 'time', 'desc');
        $payment_status = $this->payment_status;
        // Khách hàng
        $customers = $order->customer ?? '';

        return view('Ecommerce::admin.orders.show', compact(
            'admin_users',
            'order',
            'customers',
            'order_details',
            'payment_status',
            'order_histories'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Ghi chú dành cho Admin
     */
    public function adminNote(Request $requests, $orderID) {
        // Không có quyền sửa thì trả về trang chủ
        if (!checkRole($this->table_name.'_edit')) {
            return redirect(route('admin.home'))->with([
                'type' => 'danger',
                'message' => 'Translate::admin.role.no_permission',
            ]);
        }
        //
        $note = $requests->admin_note;
        // Không có note sẽ không ghi
        if (!empty($note)) {
            $this->orderService->addOrderHistory($orderID, 'admin_note', $note);
        }
        return redirect(route('admin.orders.show', $orderID));
    }
}
