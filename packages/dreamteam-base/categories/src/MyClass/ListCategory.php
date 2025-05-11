<?php

namespace DreamTeam\Category\MyClass;

use DB;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Services\Interfaces\CrudServiceInterface;

class ListCategory
{

	public $index = -1;
	public $categories = [];

	protected CrudServiceInterface $service;
	protected bool $hasLocale;
	protected string $locale;

	/**
	 * Khởi tạo List danh mục
	 * @param string 			$service
	 * @param boolean 			$hasLocale: có đa ngôn ngữ hay không (true có | false không)
	 * @param string 			$locale: Ngôn ngữ bản ghi hiện tại. Để Null sẽ lấy theo ngôn ngữ hệ thống
	 * @return array mảng danh sách các categories theo thứ tự cấp độ - sắp xếp (order)
	 */
	function __construct(CrudServiceInterface $service, bool $hasLocale = true, string $locale = null) {
		$this->service = $service;
		$this->hasLocale = $hasLocale;
		$this->locale = $locale;
	}
	
	/**
	 * Lấy ra danh sách danh mục thuộc mảng
	 * @param int 				$parentId id danh mục cha
	 * @param int 				$level level của cấp bắt đầu
	 * @param array 			$status trang thái lấy (1 = hoạt động, 2 = không hoạt động, 3 = thùng rác)
	 * @return array mảng danh sách các categories theo thứ tự cấp độ - sắp xếp (order)
	 */
	public function lists(int $parentId = 0, $level = 0, $status = [BaseStatusEnum::ACTIVE]) {
		$datas = $this->service->getWithMultiFromConditions(
			['allChildrenCates'],
			[
				'parent_id' => ['=' => $parentId],
				'status'    => ['IN' => $status]
			],
			'order', 'asc', true, '*', $this->locale);
		$this->getAllChilds($datas, 0);
		return $this->categories;
	}

	public function data_raw(int $parentId = 0, int $level = 0, array $status = [BaseStatusEnum::ACTIVE]) {
		return collect($this->lists($parentId, $level, $status));
	}

	public function data(int $parentId = 0, int $level = 0, array $status = [BaseStatusEnum::ACTIVE]) {
		$list_categories = $this->lists($parentId, $level, $status);
        $array_categories = [];
        foreach($list_categories as $key=>$value) {
            $prefix = '';
            for($i = 0; $i < $value['level']; $i++) $prefix .= '|—';
            $array_categories[$value['id']] = $prefix.$value['name'];
        }
        return $array_categories;
	}

	public function data_select(int $parentId = 0, int $level = 0, array $status = [BaseStatusEnum::ACTIVE]) {
		$list_categories = $this->lists($parentId, $level, $status);
        $array_categories = [];
        $array_categories[''] = 'Translate::admin.no_select_category';
        foreach($list_categories as $key=>$value) {
            $prefix = '';
            for($i = 0; $i < $value['level']; $i++) $prefix .= '|—';
            $array_categories[$value['id']] = $prefix.$value['name'];
        }
        return $array_categories;
	}

	public function dataToArray(int $parentId = 0, int $level = 0, array $status = [BaseStatusEnum::ACTIVE]) {
		$list_categories = $this->lists($parentId, $level, $status);
        $array_categories = [];
		$array_categories[] = [
			'value' => 0,
			'name' => trans('Translate::admin.no_select_category')
		];
        foreach($list_categories as $key=>$value) {
            $prefix = '';
            for($i = 0; $i < $value['level']; $i++) $prefix .= '|—';
            $array_categories[] = [
				'value' => $value['id'],
				'name' => $prefix.$value['name']
			];
        }
        return $array_categories;
	}


    protected function getAllChilds($categoryDatas, int $level = 0)
    {
    	foreach ($categoryDatas as $key => $value) {
    		$this->index++;
    		$this->categories[$this->index] = $value->toArray();
    		$this->categories[$this->index]['level'] = $level;
    		$categories = $value->allChildrenCates->where('status', BaseStatusEnum::ACTIVE);
    		$this->categories[$this->index]['haschild'] = false;
    		if(count($categories)>0){
    			$this->categories[$this->index]['haschild'] = true;
    			$this->getAllChilds($categories, $level + 1);
    		}
    	}
    }
}