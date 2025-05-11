<?php

namespace DreamTeam\Category\MyClass;

use Illuminate\Http\Request;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

class ListDataCategory
{

	/*
	* Mảng build form tìm kiếm
	*/
	private $no_add 			= true;
	private $no_trash 			= true;
	private $search 			= [];
	private $search_btn 		= [];
	private $table_generate 	= [];
	private $action 			= [];
	private $table_action 		= [];
	private $table_simple 		= false;
	private $paginate 			= true;
	private $categories         = [];
	private $index              = 0;

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
		CrudServiceInterface $service,
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

	// ẩn nút thêm
	public function no_add() {
		$this->no_add = false;
	}
	// ẩn nút thêm
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
	 * @param string 		$field_name: tên cột tìm kiếm
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
	 * Thêm các nút hành động chung cho toàn bộ bảng
	 * @param string 		$field_name: tên cột
	 * @param string 		$value: giá trị thay đổi
	 * @param string 		$label: tên cột hiển thị
	 * @param string 		$btnType: class màu sắc của nút, lấy theo tên rút ngắn btn- của bs3 (primary | default | danger | warning | ...)
	 * @param string 		$icon: icon hiển thị
	 */

	public function action($field_name, $value=[-2,1,0,-1], $label = ['Translate::table.batch_process', 'Translate::table.turn_on_publish', 'Translate::table.turn_off_publish', 'Translate::table.delete_temp']) {
		$this->action = [
			'field_name' 	=> $field_name,
			'value' 		=> $value,
			'label' 		=> $label
		];
	}
	public function btnAction($field_name, $label = '', $btnType = 'default', $icon = '') {
		$this->table_action[] = [
			'fields' 	=> $field_name,
			'label' 	=> $label,
			'btnType' 	=> $btnType,
			'icon' 		=> $icon,
		];
	}

	/**
	 * Thêm cột vào bảng
	 * @param string 		$field_name: tên cột
	 * @param string 		$label: tên cột hiển thị
	 * @param number 		$has_order: có sắp xếp theo cột hay không (1 có | 0 không)
	 * @param string 		$type: Loại hiển thị mặc định (show | edit | delete | status)
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
		// $conditions = ['parent_id' => ['=' => 0]];
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
				// Tên trường
				$fieldName = $field['fields'];
				// Giá trị search tại trường
				$searchField = $field['fields'];
				$searchValue = $this->request->$searchField;
				if (!in_array($field['field_type'], ['custom_conditions', 'custom_conditions_array'])) {
					$fieldName = $this->tableName . '.' .$field['fields'];
					switch ($field['field_type']) {
					    case 'string':
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
					$customConditions[$fieldName] = $searchValue;
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
		$data = $this->service->getListDataCategory($this->request, $this->with, [], $customConditions, $orders, $this->hasLocale);
		$this->formatCategories($data, 0);
		if (isset($conditions[$this->tableName . '.status']['DFF'])) {
            $categories = collect($this->categories)->where('status', '<>', $conditions[$this->tableName . '.status']['DFF']);
        } else if (isset($conditions[$this->tableName . '.status']['IN'])) {
            $categories = collect($this->categories)->whereIn('status', $conditions[$this->tableName . '.status']['IN']);
        } else if (isset($conditions[$this->tableName . '.status']['='])) {
            $categories = collect($this->categories)->where('status', $conditions[$this->tableName . '.status']['=']);
        }

        if (isset($conditions[$this->tableName . '.name']['LIKE'])) {
            $searchString = strtolower($conditions[$this->tableName . '.name']['LIKE']);
            $categories = $categories->reject(function ($item) use ($searchString) {
                return strpos(strtolower($item['name'] ?? ''), $searchString) === false;
            });
        }
		$this->categories = $categories;
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
			'show_data'			=> $this->categories,
			'table_name' 		=> $this->tableName,
			'view' 				=> $this->view,
			'search' 			=> $this->search,
			'search_btn' 		=> $this->search_btn,
			'table_generate' 	=> $this->table_generate,
			'no_add'			=> $this->no_add,
			'no_trash'			=> $this->no_trash,
			'table_simple'		=> $this->table_simple,
			'action'			=> $this->action,
			'page_size'			=> $this->pageSize,
			'paginate'			=> $this->paginate,
			'userRoleActions'	=> $userRoleActions,
		];
		if ($this->request->ajax()) {
			$data_html = view('Category::item', ['data' => $results])->render();
			$results['data_html'] = $data_html;
		}
		return $results;
	}

	/**
	 * Trả về mảng view
	 * @param array 	$compact: các giá trị thêm truyên vào từ controller
	 * Trả về string nếu là ajax còn không thì trả về view
	 */
	public function render($compact=[]) {
		$data = $compact['data'] ?? $this->data();
		if ($this->request->ajax()) {
            return $data;
        } else {
        	$compact['data'] = $data;
            return view('Category::index', $compact);
        }
	}
	public function render_data($compact=[]) {
		$data = $compact['data'] ?? $this->data();
    	$compact['data'] = $data;
        return $compact;
	}

	protected function formatCategories($categoryDatas, int $level = 0, int|string|null $parentId = 0)
    {
    	foreach ($categoryDatas->where('parent_id', $parentId) as $key => $value) {
    		$arrayValue = $value->toArray();
    		if(!isset($value->lang_locale)) {
    			$langMeta = $value->load('language_metas')?->language_metas?->toArray();
    			$arrayValue = array_merge($arrayValue, $langMeta);
    		}
    		$this->index++;
    		$categories = $value->allChildrenCates;
			if (!isset($this->categories[$this->index])) {
				$this->categories[$this->index] = $arrayValue;
				$this->categories[$this->index]['level'] = $level;
				$this->categories[$this->index]['haschild'] = false;
			}
    		if(count($categories)>0){
    			$this->categories[$this->index]['haschild'] = true;
    			$this->formatCategories($categories, $level + 1, $value->id);
    		}
    	}
    }
}
