<?php

namespace DreamTeam\Form\MyClass;

class FormBuilder
{

    /**
     * @array
     */
    private $data_form = [];
    /**
     * Các view mặc định
     * @array
     */
    private $view = [
        'create'                => 'Form::create',
        'create_multi_col'      => 'Form::create_multi_col',
        'create_and_show'       => 'Form::create_and_show',
        'edit'                  => 'Form::edit',
        'edit_multi_col'        => 'Form::edit_multi_col',
        'edit_and_show'         => 'Form::edit_and_show',
    ];
    /**
     * Form có full hay không
     * @boolean
     */
    private $form_full = false;

    public function __construct()
    {
    }

    /**
     * @return $data_form
     */
    public function formArray()
    {
        return $this->data_form;
    }

    /**
     * cấu hình form có full hay không
     * @param boolean   $full: có full hay không (true có | false không)
     */

    /**
     * render view
     */
    public function render(
        $type,
        $compact             = [],
        $custom_view         = ''
    ) {
        if (isset($this->view[$type])) {
            $view = $this->view[$type];
        } else {
            $view = $custom_view;
        }
        $compact['data_form'] = $this->formArray();
        $compact['has_full'] = $this->form_full;
        return view($view, $compact);
    }

    /**
     * @param string 	$label: Tiêu đề hiển thị
     */
    function title(
        $label                 = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'title',
            'label'            => $label
        ];
    }

    /**
     * @param string 	$label: Tiêu đề hiển thị
     */
    function note(
        $label                 = '',
        $has_row              = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'note',
            'label'            => $label,
            'has_row'         => $has_row,
        ];
    }

    /**
     * @param string 	$label: Tiêu đề hiển thị
     */
    function head(
        $label                 = '',
        $has_row              = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'head',
            'label'            => $label,
            'has_row'       => $has_row,
        ];
    }

    /**
     * @param string     $value:  hiển thị
     * @param string     $label: Tiêu đề hiển thị
     */
    function disable(
        $value              = '',
        $label              = '',
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'disable',
            'value'         => $value,
            'label'         => $label,
            'has_row'       => $has_row,
            'class_col'     => $class_col
        ];
    }

    /**
     * @param string    $col: Số cột chiếm
     * @param string    $label: Tiêu đề hiển thị
     */
    function card(
        $col                = '',
        $title                = '',
        $desc                = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'card',
            'col'           => $col,
            'title'         => $title,
            'desc'          => $desc
        ];
    }

    /**
     * @param string     $class: class để chia cột vd: col-lg-12
     */
    function endCard()
    {
        $this->data_form[] = [
            'form_type'     => 'endCard'
        ];
    }

    /**
     * @param string     $class: class để chia cột vd: col-lg-12
     */
    function col(
        $class              = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'col',
            'class'         => $class
        ];
    }

    /**
     * Đóng col
     */
    function endCol()
    {
        $this->data_form[] = [
            'form_type'     => 'endCol'
        ];
    }

    /**
     * @param string     $class: class để gom thành 1 hàng
     */
    function row()
    {
        $this->data_form[] = [
            'form_type'     => 'row',
        ];
    }

    /**
     * Đóng row
     */
    function endRow()
    {
        $this->data_form[] = [
            'form_type'     => 'endRow'
        ];
    }

    /**
     * @param string     $label: Label hiển thị bên trái
     * @param string     $list_tab: Danh sách tên của các tab
     * @param string     $list_tab: Danh sách các class được active khi click vào tên tab
     * @param string     $has_full: Có full hay không (false không | true có)
     */
    function tab(
        $label          = 'Tiêu đề',
        $list_tab       = [],
        $list_class     = [],
        $has_full       = false

    ) {
        $this->data_form[] = [
            'form_type'         => 'tab',
            'label'             => $label,
            'list_tab'          => $list_tab,
            'list_class'        => $list_class,
            'has_full'          => $has_full,
        ];
    }

    /**
     * Đóng tab
     * @param string     $has_full: Có full hay không (false không | true có)
     */
    function endTab($has_full = false)
    {
        $this->data_form[] = [
            'form_type'     => 'endTab',
            'has_full'      => $has_full,
        ];
    }

    /**
     * @param string     $class: Class dc active khi click vào tên tab (trùng với class tren function tab)
     */
    function contentTab(
        $class      = 'default'
    ) {
        $this->data_form[] = [
            'form_type'     => 'contentTab',
            'class'         => $class,
        ];
    }

    /**
     * Đóng contenttab
     */
    function endContentTab()
    {
        $this->data_form[] = [
            'form_type'     => 'endContentTab'
        ];
    }
    function startGroup(
        $id     = '',
        $label   = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'startGroup',
            'id'            => $id,
            'label'          => $label,
        ];
    }
    function endGroup()
    {
        $this->data_form[] = [
            'form_type'     => 'endGroup',
        ];
    }
    /**
     * @param string    $table_name: tên bảng
     */
    function lang(
        $table_name = null,
        $has_row    = false,
        $class_col  = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'lang',
            'table_name'    => $table_name,
            'has_row'       => $has_row,
            'class_col'     => $class_col
        ];
    }

    function alert(
        $type    = 'warning',
        $text  = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'alert',
            'type'          => $type,
            'text'          => $text,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     * @param string 	$has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function text(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = 'Tiêu đề',
        $placeholder         = null,
        $has_row            = false,
        $class_col          = '',
        $disable            = false,
        $limit              = 0,
    ) {
        $this->data_form[] = [
            'form_type'        => 'text',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'    => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'       => $disable,
            'limit'         => $limit,
        ];
    }

    /**
     * @param string    $name: tên
     * @param string    $value: giá trị
     * @param number    $required: bắt buộc (0 Không | 1 có)
     * @param string    $label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function color(
        $name               = '',
        $value              = '',
        $required           = 0,
        $label              = 'Tiêu đề',
        $placeholder        = null,
        $has_row            = false,
        $class_col          = '',
        $disable            = false
    ) {
        $this->data_form[] = [
            'form_type'     => 'color',
            'name'          => $name,
            'value'         => $value,
            'required'      => $required,
            'label'         => $label,
            'placeholder'   => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'       => $disable,
        ];
    }
    /**
     * @param string    $name: tên
     * @param string    $value: giá trị
     * @param number    $required: bắt buộc (0 Không | 1 có)
     * @param string    $label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     * @param string    $convert_number: conver số sang định dạng giá tiền VD: 10000 => 10,000
     * @param string    $style: conver style for input
     */
    function number(
        $name               = '',
        $value              = '',
        $required           = 0,
        $label              = 'Tiêu đề',
        $placeholder        = null,
        $has_row            = false,
        $class_col          = '',
        $disable            = false,
        $convert_number     = true,
        $style              = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'number',
            'name'          => $name,
            'value'         => $value,
            'required'      => $required,
            'label'         => $label,
            'placeholder'   => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'       => $disable,
            'convert_number' => $convert_number,
            'style' => $style,
        ];
    }

    function email(
        $name               = '',
        $value              = '',
        $required           = 0,
        $label              = 'Tiêu đề',
        $placeholder        = null,
        $has_row            = false,
        $class_col          = '',
        $disable            = false
    ) {
        $this->data_form[] = [
            'form_type'     => 'email',
            'name'          => $name,
            'value'         => $value,
            'required'      => $required,
            'label'         => $label,
            'placeholder'   => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'       => $disable,
        ];
    }
    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     */
    function hidden(
        $name                 = '',
        $value                 = ''
    ) {
        $this->data_form[] = [
            'form_type'         => 'hidden',
            'name'             => $name,
            'value'         => $value
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$placeholder: Gợi ý nhập
     * @param string 	$confirm: kiểm tra trùng tại box
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function password(
        $name                 = '',
        $value                = '',
        $required             = 0,
        $label                 = 'Tiêu đề',
        $placeholder         = null,
        $confirm            = '',
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'password',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'    => $placeholder,
            'confirm'        => $confirm,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$placeholder: Gợi ý nhập
     * @param string 	$confirm: kiểm tra trùng tại box
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function passwordGenerate(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = 'Tiêu đề',
        $placeholder         = null,
        $confirm            = '',
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'passwordGenerate',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'    => $placeholder,
            'confirm'        => $confirm,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$extends: Nhận từ động từ input nào
     * @param boolean   $unique: Check duy nhất hay không (true | false)
     * @param string 	$table: tên bảng để check giá trị unique
     */
    function slug(
        $name                 = '',
        $value                 = '',
        $required             = 1,
        $label                 = 'Đường dẫn',
        $extends            = 'name',
        $unique             = 'true',
        $table                = null,
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'slug',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'extends'        => $extends,
            'unique'        => $unique,
            'table'            => $table,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     * @param number 	$row: Số hàng (Mặc định 5)
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function textarea(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $placeholder        = null,
        $row                 = 5,
        $has_row            = false,
        $class_col          = '',
        $disable            = false,
        $limit              = 0,
    ) {
        $this->data_form[] = [
            'form_type'        => 'textarea',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'   => $placeholder,
            'row'            => $row,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'       => $disable,
            'limit'         => $limit,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     */
    function editor(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'editor',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }
    function ckeditor(
        $name               = '',
        $value              = '',
        $required           = 0,
        $label              = '',
        $auto_content       = false,
        $has_row            = false,
        $class_col          = '',
        $notShortcode       = false,
    ) {
        $this->data_form[] = [
            'form_type'     => 'ckeditor',
            'name'          => $name,
            'value'         => $value,
            'required'      => $required,
            'label'         => $label,
            'auto_content'  => $auto_content,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'notShortcode'  => $notShortcode,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param array 	$options: Mảng giá trị
     * @param number 	$select2: Có sử dụng select2 hay không (Mặc định có)
     * @param array 	$disabled: Mảng Giá trị bị disabled
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function select(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $options             = [],
        $select2             = 0,
        $disabled             = [],
        $has_row            = false,
        $class_col          = '',
        $cascader           = false
    ) {
        $this->data_form[] = [
            'form_type'        => 'select',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'options'        => $options,
            'select2'        => $select2,
            'disabled'        => $disabled,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'cascader'     => $cascader,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param array 	$options: Mảng giá trị
     * @param number 	$select2: Có sử dụng select2 hay không (Mặc định có)
     * @param string    $has_row: label và thẻ input có nằm 1 hàng không (false không | true có)
     * @param string    $class_col: class để chia cột (col-lg-6 chia 2 cột | col-lg-4 chia 3 cột | col-lg-3 chia 4 cột)
     */
    function multiSelect(
        $name                 = '',
        $value                 = [],
        $required             = 0,
        $label                 = '',
        $options             = [],
        $placeholder        = null,
        $select2             = 1,
        $disabled           = [],
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'multiSelect',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'options'       => $options,
            'placeholder'    => $placeholder,
            'select2'        => $select2,
            'disabled'      => $disabled,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param number 	$value: giá trị
     * @param number 	$checked: giá trị check với value (Nếu bằng với value thì sẽ checked)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string    $class_col: class của cha (class cha để 1 cột nhập col-lg-12 | class cha để 2 or 3 cột nhập col-lg-6)
     */
    function checkbox(
        $name                 = '',
        $value                 = '',
        $checked             = 1,
        $label                 = '',
        $class_col          = 'col-lg-12'
    ) {
        $this->data_form[] = [
            'form_type'        => 'checkbox',
            'name'            => $name,
            'value'            => $value,
            'checked'        => $checked,
            'label'         => $label,
            'class_col'     => $class_col
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param array 	$options: Mảng giá trị
     * @param string    $class_col: class của cha (class cha để 1 cột nhập col-lg-12 | class cha để 2 or 3 cột nhập col-lg-6)
     */
    function multiCheckbox(
        $name                 = '',
        $value                 = [],
        $required             = 0,
        $label                 = '',
        $options             = [],
        $has_row            = false,
        $class_col          = 'col-lg-12'
    ) {
        $this->data_form[] = [
            'form_type'        => 'multiCheckbox',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'options'        => $options,
            'has_row'       => $has_row,
            'class_col'     => $class_col
        ];
    }

    /**
     * @param string     $name: tên
     * @param number     $value: giá trị
     * @param string     $label: Tiêu đề hiển thị
     * @param array      $options: Mảng giá trị
     */
    function radio(
        $name               = '',
        $value              = '',
        $label              = '',
        $options            = [],
        $class_col          = 'col-lg-12',
        $has_full           = false,
    ) {
        $this->data_form[] = [
            'form_type'     => 'radio',
            'name'          => $name,
            'value'         => $value,
            'label'         => $label,
            'options'       => $options,
            'class_col'     => $class_col,
            'has_full'     => $has_full,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị
     * @param number    $required: bắt buộc (0 Không | 1 có)
     * @param string    $label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     * @param number 	$auto_click: tự động nhập (0 Không | 1 có) (Mặc định có)
     */
    function tags(
        $name                 = '',
        $value                 = [],
        $required             = 0,
        $label                 = '',
        $placeholder        = null,
        $auto_click         = 1,
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'tags',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'         => $label,
            'placeholder'   => $placeholder,
            'auto_click'    => $auto_click,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$title_btn: Text tại nút bấm hiển thị media
     */
    function image(
        $name                   = '',
        $value                  = '',
        $required               = 0,
        $label                  = 'Ảnh đại diện',
        $title_btn              = null,
        $helper_text            = 'Chọn ảnh có kích thước XXXxXXX',
        $has_row                = false,
        $class_col              = '',
        $attributes             = [],
    ) {
        $this->data_form[] = [
            'form_type'         => 'image',
            'name'              => $name,
            'value'             => $value,
            'required'          => $required,
            'label'             => $label,
            'title_btn'         => $title_btn,
            'helper_text'       => $helper_text,
            'has_row'           => $has_row,
            'class_col'         => $class_col,
            'attributes'        => $attributes,
        ];
    }

    /**
     * @param string    $name: tên
     * @param string    $value: giá trị
     * @param number    $required: bắt buộc (0 Không | 1 có)
     * @param string    $label: Tiêu đề hiển thị
     * @param string    $title_btn: Text tại nút bấm hiển thị media
     */
    function file(
        $name               = '',
        $value              = '',
        $required           = 0,
        $label              = 'Ảnh đại diện',
        $title_btn          = null,
        $helper_text        = 'Chọn ảnh có kích thước XXXxXXX',
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'file',
            'name'          => $name,
            'value'         => $value,
            'required'      => $required,
            'label'         => $label,
            'title_btn'     => $title_btn,
            'helper_text'   => $helper_text,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$title_btn: Text tại nút bấm hiển thị media
     */
    function multiImage(
        $name                   = '',
        $value                  = [],
        $required               = 0,
        $label                  = 'Ảnh slide',
        $title_btn              = null,
        $has_row                = false,
        $class_col              = '',
        $attributes             = []
    ) {
        $this->data_form[] = [
            'form_type'         => 'multiImage',
            'name'              => $name,
            'value'             => $value,
            'required'          => $required,
            'label'             => $label,
            'title_btn'         => $title_btn,
            'has_row'           => $has_row,
            'class_col'         => $class_col,
            'attributes'        => $attributes,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$title_btn: Text tại nút bấm hiển thị media
     */
    function multiFile(
        $name                   = '',
        $value                  = [],
        $required               = 0,
        $label                  = 'Files',
        $title_btn              = null,
        $has_row                = false,
        $class_col              = '',
        $attributes             = []
    ) {
        $this->data_form[] = [
            'form_type'         => 'multiFile',
            'name'              => $name,
            'value'             => $value,
            'required'          => $required,
            'label'             => $label,
            'title_btn'         => $title_btn,
            'has_row'           => $has_row,
            'class_col'         => $class_col,
            'attributes'        => $attributes,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     */
    function datepicker(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $placeholder        = null,
        $has_row            = false,
        $class_col          = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'datepicker',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'   => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string    $placeholder: Gợi ý nhập
     */
    function datetimepicker(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $placeholder        = null,
        $has_row            = false,
        $class_col          = '',
        $disable            = false
    ) {
        $this->data_form[] = [
            'form_type'        => 'datetimepicker',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'         => $label,
            'placeholder'    => $placeholder,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'disable'     => $disable,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$placeholder: Gợi ý nhập
     * @param string 	$suggest_table: tên bảng
     * @param string 	$suggest_id: tên giá trị
     * @param string    $suggest_name: tên giá trị hiển thị
     * @param string 	$suggest_locale: có search theo đa ngôn ngữ hay không (true có | false không) (Bảng $suggest_table phải là bảng set đa ngôn ngữ)
     */
    public function suggest(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $placeholder         = 'Tìm theo tên ...',
        $suggest_table         = 'news',
        $suggest_id         = 'id',
        $suggest_name       = 'name',
        $suggest_locale     = 'false',
        $has_row            = false,
        $class_col          = ''

    ) {
        $this->data_form[] = [
            'form_type'        => 'suggest',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'     => $placeholder,
            'suggest_table' => $suggest_table,
            'suggest_id'     => $suggest_id,
            'suggest_name'  => $suggest_name,
            'suggest_locale' => $suggest_locale,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
        ];
    }

    /**
     * @param string 	$name: tên
     * @param string 	$value: giá trị cách nhau bằng dấu phẩy
     * @param number 	$required: bắt buộc (0 Không | 1 có)
     * @param string 	$label: Tiêu đề hiển thị
     * @param string 	$placeholder: Gợi ý nhập
     * @param string 	$suggest_table: tên bảng
     * @param string 	$suggest_id: tên giá trị
     * @param string 	$suggest_name: tên giá trị hiển thị
     * @param string    $suggest_locale: có search theo đa ngôn ngữ hay không (true có | false không) (Bảng $suggest_table phải là bảng set đa ngôn ngữ)
     */
    public function multiSuggest(
        $name                 = '',
        $value                 = '',
        $required             = 0,
        $label                 = '',
        $placeholder         = 'Tìm theo tên ...',
        $suggest_table         = 'news',
        $suggest_id         = 'id',
        $suggest_name         = 'name',
        $suggest_locale     = 'false',
        $has_row            = false,
        $class_col          = '',
        $lang_locale        = ''
    ) {
        $this->data_form[] = [
            'form_type'        => 'multiSuggest',
            'name'            => $name,
            'value'            => $value,
            'required'        => $required,
            'label'            => $label,
            'placeholder'     => $placeholder,
            'suggest_table' => $suggest_table,
            'suggest_id'     => $suggest_id,
            'suggest_name'     => $suggest_name,
            'suggest_locale' => $suggest_locale,
            'has_row'       => $has_row,
            'class_col'     => $class_col,
            'lang_locale'   => $lang_locale,
        ];
    }

    function tableOption(string $defineName, string $tableName, int|string $tableId = 0)
    {
        if (defined($defineName)) {
            $filterResponse = apply_filters(constant($defineName), ['table_type' => $tableName, 'table_id' => $tableId]);
            $data = [
                'data' => $filterResponse['data'],
                'options' => $filterResponse['options'],
                'tableOptionID' => $filterResponse['tableOptionID']
            ];
            $this->data_form[] = [
                'form_type'     => 'custom',
                'template'      => $filterResponse['view'],
                'param'         => $data
            ];
        }
    }

    /**
     * @param string 	$template: view template
     * @param array 	$param: giá trị chấp nhận của view template
     */
    function custom(
        $template,
        $param                 = []
    ) {
        $this->data_form[] = [
            'form_type'        => 'custom',
            'template'        => $template,
            'param'            => $param
        ];
    }

    /**
     * @param string 	$name: tên
     * @param array 	$value: giá trị menu
     * @param string 	$label: Tiêu đề hiển thị
     * @param strong 	$template: View hiển thị menu
     */
    function customMenu(
        $name                 = '',
        $value                 = '',
        $label                 = '',
        $template             = 'Form::base.customMenu'
    ) {
        $this->data_form[] = [
            'form_type'        => 'customMenu',
            'name'            => $name,
            'value'         => $value,
            'label'            => $label,
            'template'         => $template,
        ];
    }

    /**
     * @param string     $type: tên hành động (add | edit | editconfig)
     * @param string 	$preview: Link xem nhanh
     * @param exit_url 	$exit_url: link Thoát
     */
    function action(
        $type                 = '',
        $preview             = '',
        $duplicate          = '',
        $exit_url           = '',
        $custom             = []
    ) {
        $this->data_form[] = [
            'form_type'     => 'action',
            'type'          => $type,
            'preview'         => $preview,
            'duplicate'     => $duplicate,
            'exit_url'      => $exit_url,
            'custom'         => $custom
        ];
    }
    function actionInline(
        $type               = ''
    ) {
        $this->data_form[] = [
            'form_type'     => 'actionInline',
            'type'          => $type
        ];
    }
    
    function html(
        $tag = 'div',
        $attributes = []

    ) {
        $this->data_form[] = [
            'form_type'         => 'html',
            'tag'               => $tag,
            'attributes'        => $attributes,
        ];
    }
}
