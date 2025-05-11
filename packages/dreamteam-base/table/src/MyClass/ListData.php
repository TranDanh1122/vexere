<?php

namespace DreamTeam\Table\MyClass;

use Illuminate\Http\Request;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

class ListData
{

	/*
	* Mảng build form tìm kiếm
	*/
	private $no_add 			= true;
	private $no_trash 			= true;
	private $search 			= [];
	private $search_btn 		= [];
	private $table_generate 	= [];
	private $table_action 		= [];
	private $action 			= '';
	private $table_simple 		= false;
	private $paginate 			= true;
	private $top_action 		= [];

	protected Request $request;
	protected CrudServiceInterface $service;
	protected string $tableName;
	protected string $view;
	protected array $with;
	protected bool $hasStatusColumn;
	protected bool $hasLocale;
	protected int $pageSize;
	protected array $order;

	/**
	 * Khởi tạo Listdata
	 * @param Illuminate\Http\Request 		$request
	 * @param service 		$service: Service
	 * @param string 		$view: view hiển thị của các item cột VD: Core::admin_users.table
	 * @param array 		$with: with relationship
	 * @param bool 		    $hasStatusColumn: có cột trạng thái status hay k
	 * @param bool 		    $hasLocale: có đa nn hay không
	 * @param number 		$pageSize: số lượng phân trang tại bảng, mặc định là 30
	 * @param array 		$order: mảng sắp xếp VD: ['order' => 'asc', 'id' => 'desc'] -> ưu tiên sắp xếp theo order trước rồi mới sắp xếp theo mảng này
	 */
	public function __construct(
		Request $request,
		$service,
		string $tableName,
		string $view,
		array $with,
		bool $hasStatusColumn = true,
		bool $hasLocale = false,
		int $pageSize = 30,
		array $order = ['id' => 'desc']
	)
	{
		$this->request 			= $request;
		$this->service 			= $service;
		$this->tableName 		= $tableName;
		$this->view 			= $view;
		$this->with 			= $with;
		$this->hasStatusColumn 	= $hasStatusColumn;
		$this->hasLocale 		= $hasLocale;
		$this->pageSize 		= $pageSize;
		$this->order 			= $order;
	}

	// Ẩn nút thêm
	public function no_add() {
		$this->no_add = false;
	}
	// Ẩn nút xem thùng rác
	public function no_trash() {
		$this->no_trash = false;
	}
	// Gọi table ko có menu, header, footer
	public function table_simple() {
		$this->table_simple = true;
	}
	// ẩn phân trang
	public function no_paginate() {
		$this->paginate = false;
	}

	/**
	 * Build Form tìm kiếm
	 * @param string | array 		$field_name: tên cột tìm kiếm
	 * @param string 		$label: text hiển thị
	 * @param string 		$field_type: loại form (string, array, range)
	 * @param array 		$option: các cấu hình khác (Thường dùng cho mảng làm giá trị option cho select)
	 */
	public function search($field_name, $label, $field_type='string', $option=[]) {
		$this->search[] = [
			'fields' 		=> $field_name,
			'label' 		=> $label,
			'field_type' 	=> $field_type,
			'option' 		=> $option,
		];
	}

    /**
	 * Build Form tìm kiếm lựa chọn cột trên nhiều cột
	 * @param array 		$fields:
     *  Mảng các cột tìm kiếm có định dạng
     *  [
     *      'column_1' => 'Label 1',
     *      'column_2' => [
     *          'type'  => 'custom_conditions',
     *          'label' => 'Label 2'
     *      ]
     *  ]
	 * @param string 		$label: text hiển thị tại input tìm kiếm
	 */
    public function searchColumns($fields, $label) {
        $this->search($fields, $label, 'multipleColumns');
    }

	/**
	 * Nút build các hành động theo forn tìm kiếm: Xuất Excel
	 * @param string 		$label: Text hiển thị nút
	 * @param string 		$url: route url xử lý hành động, thường là route Post xử lý form
	 * @param string 		$btn_type: Loại nút hiển thị (success | danger | primary | warning | default | info | secondary)
	 * @param string 		$btn_icon: icon hiển thị nút (lấy theo icon font-awesome)
	 */
	public function searchBtn($label, $url, $btn_type='success', $btn_icon='') {
		$this->search_btn[] = [
			'label' 		=> $label,
			'url' 			=> $url,
			'btn_type' 		=> $btn_type,
			'btn_icon' 		=> $btn_icon,
		];
	}

	/**
	 * Nút build các hành động theo forn tìm kiếm: Xuất Excel
	 * @param string 		$label: Text hiển thị nút
	 * @param string 		$url: route url xử lý hành động, thường là route Post xử lý form
	 * @param string 		$btn_type: Loại nút hiển thị (success | danger | primary | warning | default | info | secondary)
	 * @param string 		$btn_icon: icon hiển thị nút (lấy theo icon font-awesome)
	 */
	public function topAction($action) {
		$this->top_action[] = [
			'action' 		=> $action,
		];
	}

	/**
	 * Thêm các nút hành động chung cho toàn bộ bảng
	 * @param string 		$field_name: tên cột
	 * @param string 		$value: giá trị thay đổi
	 * @param string 		$label: tên cột hiển thị
	 * @param string 		$btnType: class màu sắc của nút, lấy theo tên rút ngắn btn- của bs3 (primary | default | danger | warning | ...)
	 * @param string 		$icon: icon hiển thị
	 */
	public function btnAction($field_name, $label = '', $btnType = 'default', $icon = '', $url = '') {
		$this->table_action[] = [
			'fields' 	=> $field_name,
			'label' 	=> $label,
			'btnType' 	=> $btnType,
			'icon' 		=> $icon,
			'url' 		=> $url,
		];
	}
	/**
	 * Thêm các hành động chung cho toàn bộ bảng
	 * @param string 		$field_name: tên cột
	 * @param string 		$value: giá trị thay đổi trong thẻ select option
	 * @param string 		$label: tên hiển thị trong thẻ select option
	 */
	public function action($field_name, $value=[-2,1,0,-1], $label = ['Translate::table.batch_process', 'Translate::table.turn_on_publish', 'Translate::table.turn_off_publish', 'Translate::table.delete_temp']) {
		$this->action = [
			'field_name' 	=> $field_name,
			'value' 		=> $value,
			'label' 		=> $label
		];
	}

	/**
	 * Thêm cột vào bảng
	 * @param string 		$field_name: tên cột
	 * @param string 		$label: tên cột hiển thị
	 * @param number 		$has_order: có sắp xếp theo cột hay không (1 có | 0 không)
	 * @param string 		$type: Loại hiển thị mặc định (time | status | show | lang | order | edit | delete | delete_custom | restore)
	 * @param array 		$option: mảng option theo type là status
	 */
	public function add($field_name, $label, $has_order=0, $type='', $option=[]) {
		$this->table_generate[] = [
			'field' 		=> $field_name,
			'label' 		=> $label,
			'has_order' 	=> $has_order,
			'type' 			=> $type,
			'option' 		=> $option,
		];
	}

	/**
	 * Trả về mảng data
	 * @return array
	 */
	public function data() {
		$conditions = [];
		$customConditions = [];
		// Nếu là view thùng rác thì xử lý riêng
		if (isset($this->request->trash) && $this->request->trash == true) {
			// Bỏ toàn bộ cột [trạng thái, xem, sửa, xóa] tại bảng
			foreach ($this->table_generate as $key => $value) {
				if (in_array($value['field'], ['status','show', 'edit', 'delete', 'delete_custom'])) {
					unset($this->table_generate[$key]);
				}
				if (in_array($value['type'], ['status','show', 'edit', 'delete', 'delete_custom'])) {
					unset($this->table_generate[$key]);
				}
			}
			// Bỏ toàn bộ tìm kiếm [trạng thái] tại thanh tìm kiếm
			foreach ($this->search as $key => $value) {
				if (in_array($value['fields'], ['status'])) {
					unset($this->search[$key]);
				}
			}
			// Thêm cột restore
			$this->table_generate[] = [
				'field' 			=> '',
			    'label' 			=> __('Core::admin.general.restore'),
			    'has_order' 		=> 0,
			    'type' 				=> 'restore',
			];
			// Hiển thị nút lấy lại
			$this->table_action = [];
			$this->table_action[] = [
				'fields' 			=> 'status',
				'value' 			=> 1,
				'label' 			=> __('Core::admin.general.restore'),
				'btnType' 			=> 'primary',
				'icon' 				=> 'fas fa-edit',
			];
			// Không hiển thị nút đi đến thùng rác
			$this->no_trash = false;
			// Chỉ lấy bản ghi có status là -1
			if ($this->hasStatusColumn) {
				$conditions[$this->tableName.'.status'] = ['=' => BaseStatusEnum::DELETE];
			}
		} else {
			if ($this->hasStatusColumn) {
				$conditions[$this->tableName.'.status'] = ['DFF' => BaseStatusEnum::DELETE];
			}
		}

		// Kiểm tra quyền của users hiện tại
		foreach ($this->table_generate as $key => $value) {
			if (in_array($value['type'], ['show', 'edit', 'lang','restore', 'delete'])) {
				if ($value['type'] == 'lang') { $value['type'] = 'edit'; }
				if ($value['type'] == 'delete_custom') { $value['type'] = 'delete'; }
				if (!checkRole($this->tableName.'_'.$value['type'])) {
					unset($this->table_generate[$key]);
				}
			}
		}
		// Kiểm tra thêm
		if (!checkRole($this->tableName.'_create')) {
			$this->no_add = false;
		}
		// Kiểm tra các button hành động tất cả
		foreach ($this->table_action as $key => $value) {
			if ($value['fields'] == 'delete') {
				$method = 'delete';
			} if ($value['fields'] == 'delete_custom') {
				$method = 'delete';
			} elseif ($value['label'] == __('Core::admin.general.restore')) {
				$method = 'restore';
			} else {
				$method = 'edit';
			}
			if (!checkRole($this->tableName.'_'.$method)) {
				unset($this->table_action[$key]);
			}
		}

		// Nếu tìm kiếm
		if(isset($this->request->search)) {
			foreach ($this->search as $field) {
                if ($field['field_type'] === 'multipleColumns') {
                    $columnName = null;
                    foreach ($field['fields'] as $key => $labelData) {
                        if ($this->request->has($key) && $this->request->$key !== null && $this->request->$key !== '') {
                            if (is_array($labelData)) {
                                $field['field_type'] = $labelData['type'];
                            }

                            $columnName = $key;
                            break;
                        }
                    }
                    $field['fields'] = $columnName ?? '';
                }

                // Tên trường
                $fieldName = $field['fields'];
                // Giá trị search tại trường
                $searchField = $field['fields'];
                $searchValue = $this->request->$searchField;
				if (!in_array($field['field_type'], ['custom_conditions', 'custom_conditions_array', 'custom_conditions_range'])) {
					$fieldName = $this->tableName . '.' .$field['fields'];
					switch ($field['field_type']) {
					    case 'string':
                        case 'multipleColumns':
					        isset($searchValue) ? $conditions[$fieldName] = ['LIKE' => $searchValue] : '';
					        break;
					    case 'array':
					    case 'hidden':
					        isset($searchValue) ? $conditions[$fieldName] = ['=' => $searchValue] : '';
					        break;
					    case 'range':
					        $fieldStart = $field['fields'].'_start';
					        $fieldEnd = $field['fields'].'_end';
					        $searchValueStart = $this->request->$fieldStart;
					        $searchValueEnd = $this->request->$fieldEnd;
					        if (!empty($searchValueStart) && !empty($searchValueEnd)) {
					            $conditions[$fieldName] = ['BETWEEN' => [$searchValueStart, $searchValueEnd]];
					        }
					        break;
					    default:
					        break;
					}
				} else {
					if ($field['field_type'] === 'custom_conditions_range') {
                        $fieldStart = $field['fields'].'_start';
                        $fieldEnd = $field['fields'].'_end';
                        $searchValueStart = $this->request->$fieldStart;
                        $searchValueEnd = $this->request->$fieldEnd;
                        if (!empty($searchValueStart) && !empty($searchValueEnd)) {
                            $customConditions[$fieldName] = ['BETWEEN' => [$searchValueStart, $searchValueEnd]];
                        }
                    } else {
                        $customConditions[$fieldName] = $searchValue;
                    }
				}
			}
		}
		// Nếu sắp xếp
		if(isset($this->request->order_fields) && isset($this->request->order_by)) {
			$orderField = $this->request->order_fields;
			$orderByValue = $this->request->order_by;
			$orders[$orderField] = $orderByValue;
		} else {
			$orders = $this->order;
		}
		if(hasRolePrivate(getRole(), '_private') && !hasRolePrivate(getRole(), '_index')) {
		    $conditions['admin_user_id'] = ['=' => \Auth::guard('admin')->user()->id];
		};
		$data = $this->service->getListData($this->request, $this->with, $conditions, $customConditions, $orders, $this->pageSize, $this->hasLocale);
		$userRoleActions = [];
		if (checkRole($this->tableName.'_create')) {
			$userRoleActions[$this->tableName.'_create'] = true;
		}
		if (checkRole($this->tableName.'_edit')) {
			$userRoleActions[$this->tableName.'_edit'] = true;
		}
		if (checkRole($this->tableName.'_show')) {
			$userRoleActions[$this->tableName.'_show'] = true;
		}
		if (checkRole($this->tableName.'_delete')) {
			$userRoleActions[$this->tableName.'_delete'] = true;
		}
		if (checkRole($this->tableName.'_deleteForever')) {
			$userRoleActions[$this->tableName.'_deleteForever'] = true;
		}
		if (checkRole($this->tableName.'_restore')) {
			$userRoleActions[$this->tableName.'_restore'] = true;
		}
		$results = [
			'show_data'			=> $data,
			'table_name' 		=> $this->tableName,
			'view' 				=> $this->view,
			'search' 			=> $this->search,
			'search_btn' 		=> $this->search_btn,
			'top_action'		=> $this->top_action,
			'table_generate' 	=> $this->table_generate,
			'no_add'			=> $this->no_add,
			'no_trash'			=> $this->no_trash,
			'table_simple'		=> $this->table_simple,
			'table_action'		=> $this->table_action,
			'action'			=> $this->action,
			'page_size'			=> $this->pageSize,
			'paginate'			=> $this->paginate,
			'userRoleActions'	=> $userRoleActions,
		];
		return $results;
	}

	/**
	 * Trả về mảng view
	 * @param array 	$compact: các giá trị thêm truyên vào từ controller
	 * Trả về string nếu là ajax còn không thì trả về view
	 */
	public function render($compact=[], $view = 'Table::index', $view_item = 'Table::item') {
		// Nếu như truyền dữ liệu data tại controller thì sẽ lấy
		$data = $compact['data'] ?? $this->data();
		if ($this->request->ajax()) {
			// Merge mảng dữ liệu vào compact
			$compact['data'] = $data;
			// Truyền dữ liệu compact vào Table:item
			$data_html = view($view_item, $compact)->render();
			// Tạo HTML phân trang
			$paginate = $data['show_data']->appends(request()->all())->links()->toHtml();
			// Truyền giá trị cần lấy vào mảng vào Data
			$data['data_html'] = $data_html;
			$data['paginate'] = $paginate;
			// Trả về mảng giá trị data
			unset($compact['data']);
			$data = array_merge($data, $compact);
            extract($compact, EXTR_OVERWRITE);
            $top_html = '';
            if(isset($include_view_top) && !empty($include_view_top)){
                foreach ($include_view_top as $include_view => $include_data){
                    $top_html .= view($include_view, $include_data)->render();
                }
            }
            $data['top_html'] = $top_html;
            return $data;
        } else {
        	// Merge mảng dữ liệu vào compact
        	$compact['data'] = $data;
        	// Trả về view
            return view($view, $compact);
        }
	}
}
