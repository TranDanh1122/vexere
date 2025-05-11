$(document).ready(function() {
    // Đường dẫn admin
	admin_dir = $('meta[name=admin_dir]').attr('content');
    // di chuyển lên đầu trang
    // Thẻ có data-gototop khi click vào sẽ luộn lên top
    // eg: <a href="javascript:;" data-gototop>Lên trên top</a>
    $('body').on('click', '*[data-gototop]', function(e){
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
    });

    // Đổi ngôn ngữ toàn trang
	$('body').on('click', '*[data-language]', function(e) {
		e.preventDefault();
		value = $(this).data('language');
		if($(this).prop("tagName").toLowerCase() != 'pre') {
			pushOrUpdate({
				setLanguage: value,
			});
			window.location.href = window.location.href;
		}
	});

    // Đổi ngôn ngữ tại bảng
	$('body').on('change', '*[data-language_table]', function(e) {
		e.preventDefault();
		value = $(this).val();
		setCookie('table_locale', value, 1);
		loadData('progress');
	});

    // Đổi số lượng bản ghi được load
	$('body').on('change', '*[data-pagesize]', function(e) {
		e.preventDefault();
		value = $(this).val();
		setCookie('dreamteam_page_size', value, 1);
		loadData('progress');
	});

    // Cuộn chuột
    /*
        Hiệu ứng cuộn xuống 1 id/class duy nhất nào đó trong trang
        data-scroll: id/class box muốn cuộn
        data-top: là một số sẽ đặt một khoảng cách top là giá trị
        eg: <a href="#id" data-scroll="#id" data-top="20">Click cuộn xuống</a>
    */
    $('body').on('click','*[data-scroll]',function(e) {
        e.preventDefault();
        id = $(this).data('scroll');
        offset_top = $(this).data('top');
        if (!checkEmpty(offset_top)) {
            offset_top = $(id).offset().top-offset_top;
        } else {
            offset_top = $(id).offset().top;
        }
        $('html, body').animate({scrollTop: offset_top}, 500);
    });

    // Toggle box
    /*
        Khi click vào data-toggle_btn thì sẽ tạo hiệu ứng toggle cho data-toggle_box
        data-toggle_btn: không giá trị, là nút click box
        data-toggle_wrap: không giá trị, là box cha chứa cả data-toggle_btn và data-toggle_box
        data-toggle_box: không giá trị, là box muốn toggle
        eg:
        <div data-toggle_wrap>
            <div data-toggle_btn></div>
            <div data-toggle_box></div>
        </div>
    */
    $('body').on('click','*[data-toggle_btn]',function(e) {
        e.preventDefault();
        $(this).toggleClass('active')
        $(this).closest('*[data-toggle_wrap]').find('*[data-toggle_box]').slideToggle();
    });
 
    // Load tìm kiếm
	$('body').on('click', '#listdata .search-container .search-btn', function(e) {
		e.preventDefault();
		form_serialize = $(this).closest('form').serialize();
		if (checkEmpty(window.location.search)) {
			form_serialize = '?'+form_serialize;
		} else {
			form_serialize = window.location.search+'&'+form_serialize;
		}
		var newUrl = window.location.origin+window.location.pathname+form_serialize;
		newUpdateUrl(newUrl);
		loadData();
	});
    // Load Sắp xếp tại bảng listdata
	$('body').on('click', '*[data-order_by]', function(e) {
		e.preventDefault();
		order_by 		= $(this).data('order_by');
		order_fields 	= $(this).data('order_fields');
		if (order_by == 'desc') {
			$(this).data('order_by', 'asc');
		} else {
			$(this).data('order_by', 'desc');	
		}
		newPushOrUpdate({
			order_fields: order_fields,
			order_by: order_by,
		});
		loadData();
	});

    // Load phân trang tại bảng listdata
	$('body').on('click', '#listdata .pagination-sm .relative a', function(e) {
		e.preventDefault();
		href = $(this).attr('href');
		page = getUrlParameter(href, 'page');
		newPushOrUpdate({ page: page, });
		loadData();
	});

    // Hành động checkall và uncheckall
	$('body').on('change', '.checkall', function(e) {
		$checkall = $(this).is(':checked');
		$(this).closest('#listdata').find('.checkone').prop('checked', $checkall);
	});

    // Tự động cập nhật nội dung sửa khi chỉ cần đặt data-quick_edit vào input, select, textarea
	$('body').on('change', '*[data-quick_edit]', function(e) {
		e.preventDefault();
		// Giá trị
		value = $(this).val();
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		id = $(this).closest('*[data-id]').data('id');
		// Trường
		field = $(this).attr('name');
		// Mảng id_array
		let id_array = [];
		id_array.push(id);
		// Chuẩn hóa data
		data = {
			table 		: table,
			id_array 	: id_array,
			value 		: value,
			field 		: field,
		};
		loadAjaxPost('/'+admin_dir+'/ajax/quick_edit', data, {
			beforeSend: function(){},
	        success:function(result){
	        	if (result.status == 1) {
	        		alertText(result.message);
	        	} else {
	        		alertText(result.message, 'warning');
	        	}
	        },
	        error: function (error) {}
		});
	});

	// Quick edit trang thai
	$('body').on('change', '#listdata .form-switch input', function(e){
		e.preventDefault();
		if($(this).is(':checked')){
			value = 1;
		}else{
			value = 0;
		}
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		id = $(this).closest('*[data-id]').data('id');
		// Trường
		field = $(this).attr('name');
		// Mảng id_array
		let id_array = [];
		id_array.push(id);
		// Chuẩn hóa data
		data = {
			table 		: table,
			id_array 	: id_array,
			value 		: value,
			field 		: field,
		};
		loadAjaxPost('/'+admin_dir+'/ajax/quick_edit', data, {
			beforeSend: function(){},
	        success:function(result){
	        	if (result.status == 1) {
	        		alertText(result.message);
	        	} else {
	        		alertText(result.message, 'warning');
	        	}
	        },
	        error: function (error) {}
		});
	});
    // Xóa khi click icon
	$('body').on('click', '*[data-quick_delete]', function(e) {
		e.preventDefault();
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		id = $(this).closest('*[data-id]').data('id');
		// mảng id_array
		let id_array = [];
		id_array.push(id);
		// Chuẩn hóa data
		data = {
			table 		: table,
			id_array 	: id_array,
		};
		if (confirm( $(this).data('message') )) {
			loadAjaxPost('/'+admin_dir+'/ajax/quick_delete', data, {
				beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        		loadData('no_action');
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
			});
		}
	});

    // Xóa nhưng dùng controller để custom
	$('body').on('click', '*[data-delete_custom]', function(e) {
		e.preventDefault();
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		id = $(this).closest('*[data-id]').data('id');
		// Chuẩn hóa data
		data = {
			_method 	: 'DELETE'
		};
		if (confirm( $(this).data('message') )) {
			loadAjaxPost('/'+admin_dir+'/'+table+'/'+id, data, {
				beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        		loadData('no_action');
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
			});
		}
	});

    // Lấy lại bản ghi
	$('body').on('click', '*[data-quick_restore]', function(e) {
		e.preventDefault();
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		id = $(this).closest('*[data-id]').data('id');
		// mảng id_array
		let id_array = [];
		id_array.push(id);
		// Chuẩn hóa data
		data = {
			table 		: table,
			id_array 	: id_array,
		};
		loadAjaxPost('/'+admin_dir+'/ajax/quick_restore', data, {
			beforeSend: function(){},
	        success:function(result){
	        	if (result.status == 1) {
	        		alertText(result.message);
	        		loadData('no_action');
	        	} else {
	        		alertText(result.message, 'warning');
	        	}
	        },
	        error: function (error) {}
		});
	});

    // Cập nhật toàn bộ giá trị trong bảng
	$('body').on('click', '*[data-action_all]', function(e) {
		e.preventDefault();
		e = $(this);
		// Giá trị
		value = $(this).parents('.box-action').find('select[data-action]').val();
		if(value == -2){
			alertText($(this).data('warning'), 'warning');
			return false;
		}
		// Bảng
		table = $(this).data('table');
		// Trường
		field = $('select[data-action]').data('field');
		// Mảng id_array
		let id_array = [];
		e.closest('#listdata').find('.checkone:checked').each(function() {
			id = $(this).closest('*[data-id]').data('id');
			id_array.push(id);
		});
		if(value == -3) {
			if(typeof id !== 'undefined') {
				data = {
					_method 	: 'DELETE',
					idArray 	: id_array,
				};
				if (confirm( $(this).data('message') )) {
					loadAjaxPost('/'+admin_dir+'/'+table+'/delete-forever/'+id, data, {
						beforeSend: function(){},
				        success:function(result){
				        	if (result.status == 1) {
				        		alertText(result.message);
				        		loadData('no_action');
				        		$('.modal-backdrop').remove();
				        	} else {
				        		alertText(result.message, 'warning');
				        	}
				        },
				        error: function (error) {},
					});
				}
			} else {
				alertText($(this).data('null'), 'warning');
			}
		} else {
			// Chuẩn hóa data
			data = {
				table 		: table,
				id_array 	: id_array,
				value 		: value,
				field 		: field,
			};
			loadAjaxPost('/'+admin_dir+'/ajax/quick_edit', data, {
				beforeSend: function(){},
				success:function(result){
					if (result.status == 1) {
						alertText(result.message);
						// Tải lại dữ liệu tại bảng
						loadData('no_action');
						// bỏ check nút chọn tất cả
						e.closest('#listdata').find('.checkall').prop('checked', false);
					} else {
						alertText(result.message, 'warning');
					}
				},
				error: function (error) {}
			});
		}
	});

    // media action
	$('body').on('click', '*[data-image]', function(e) {
		e.preventDefault();
		url_media = $(this).data('image');
		$('#media').find('iframe').attr('src', url_media);
		$('#media').modal('toggle');
	});
	$("#media").on("hidden.bs.modal", function () {
	    $('#media').find('iframe').attr('src', '');
	});
	$('body').on('click', '*[data-delete_nocomfirm]', function(e) {
		e.preventDefault();
		$(this).closest('*[data-delete_box]').remove();
	});
	$('body').on('click', '*[data-delete_comfirm]', function(e) {
		e.preventDefault();
		confirm_text = $(this).data('delete_comfirm');
		if (confirm(confirm_text)) {
			$(this).closest('*[data-delete_box]').remove();	
		}
	});

    // Cache clear
	$('body').on('click', '*[data-cache_clear]', function(e) {
		e.preventDefault();
		loadAjaxPost('/'+admin_dir+'/ajax/cache_clear', {}, {
	        beforeSend: function(){},
	        success:function(result){
	        	if (result.status == 1) {
	        		alertText(result.message);
	        	} else {
	        		alertText(result.message, 'error');
	        	}
	        },
	        error: function (error) {}
	    }, 'progress');
	});
	//xóa admin user
	$('body').on('click', '*[data-delete_admin_user]', function(e) {
		e.preventDefault();
		// Bảng
		table = $(this).closest('*[data-table]').data('table');
		// id
		user_id = $(this).parents('.modal-content').find('input[name="user_id"]').val();
		user_id_replace = $(this).closest('.modal-content').find('select[name="admin_users_list"]').val();
		// Chuẩn hóa data
		data = {
			_method 	: 'DELETE',
			user_id_replace: user_id_replace,
			user_id:user_id
		};
		if (confirm( $(this).data('message') )) {
			loadAjaxPost('/'+admin_dir+'/'+table+'/'+user_id, data, {
				beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        		// loadData('no_action');
		        		window.location.reload();
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
			});
		}
	});
	$('body .on-of-checkbox-config.hide').hide();
	$('body').on('change', '.toggle-on-off-checkbox input', function(){
        if($(this).prop('checked')) {
            $(this).parents('.card-body').find('.on-of-checkbox-config').show()
        }else {
             $(this).parents('.card-body').find('.on-of-checkbox-config').hide()
        }
    })
    // xoa vinh vien
    $('body').on('click', '*[data-delete_forever]', function(e) {
		e.preventDefault();
		// Bảng
		const _this = $(this)
		const table = _this.closest('*[data-table]').data('table')
		const id = _this.data('id')
		// Chuẩn hóa data
		const data = {
			_method 	: 'DELETE',
			id,
			table
		};
		if (confirm( $(this).data('message') )) {
			loadAjaxPost('/'+admin_dir+'/'+table+'/delete-forever/'+id, data, {
				beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message);
		        		loadData('no_action');
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
			});
		}
	});
    $('body').on('click', '.form-submit-confirm', function(e) {
		if (!confirm(form_delete_confirm || 'Bạn có chắc chắn muốn xoá dữ liệu? Dữ liệu được xoá là vĩnh viễn không thể khôi phục.')) {
			e.preventDefault();
		}
	});
});
// close alert
$('body').on('click', '*[close-alert]', function(e){
	e.preventDefault();
	$(this).hide();
});
// close modal alert
$('body').on('click', '.popup-container .popup-bottom button', function(e){
	e.preventDefault();
	$(this).closest('.popup-container').removeClass('show');
	$('body').css('overflow', 'auto');
	setTimeout(function() {
		$('.popup-container .popup-title').empty();
	}, 1000);
});
$('body').on('keyup', function (e) {
	if ( e.keyCode === 27 ) {
		closePopup();
	}
});
$('body').on('click', '.popup-container', function (e) {
	if ( $(e.target).is('.show')) {
		closePopup();
	}
	e.preventDefault();
});
function closePopup() {
	$('.popup-container').removeClass('show');
	$('body').css('overflow', 'auto');
	setTimeout(function() {
	    $('.popup-container .popup-title').empty();
    }, 1000);
}
window.closePopup = closePopup
// Load Dữ liệu ListData
// animate: progress | loading | no_action
function loadData(animate='progress') {
    let href = $('input.newUpdateUrl').val();
    if(checkEmpty(href)) {
        href = window.location.href;
    }
	loadAjaxGet(href, {
        beforeSend: function(){},
        success:function(result){
        	$('#listdata').find('table.table').find('tbody').html(result.data_html);
			$('#listdata').find('.pagination-sm').html(result.paginate);
			$('.top_html').html(result.top_html);
            $('#listdata').find('.total').html(result.show_data.total);
			setTimeout(function() {
				$("#listdata").find('.table-responsive').animate({ scrollTop: 0 }, 500);
			}, 200);
        },
        error: function (error) {}
    }, animate);
}
window.loadData = loadData

function addLoadingBtn(element) {
	$(element).prepend('<i id="loadingIcon" class="fa fa-spinner fa-spin" style="margin-right: 3px"></i>');
	$(element).prop('disabled', true);
}

window.addLoadingBtn = addLoadingBtn

function removeLoadingBtn(element) {
	$(element).find('.fa-spinner').remove();
	$(element).prop('disabled', false);
}
window.removeLoadingBtn = removeLoadingBtn
function customAutoSave(hasId, type, element){
	$(element).on('keyup change', function () {
		switch(type){
			case 1:
				if(!hasId){
					localStorage.setItem(`${window.location.href}__${$(element).attr('name')}`, $(element).prop('checked'))
				}else{
					localStorage.setItem(`${window.location.href}__${$(element).attr('id')}`, $(element).prop('checked'))
				}
			break
			case 2:
				if(!hasId){
					localStorage.setItem(`${window.location.href}__${$(element).attr('name')}`, $(element).val())
				}else{
					localStorage.setItem(`${window.location.href}__${$(element).attr('id')}`, $(element).val())
				}
			break
		}
	})
}
window.customAutoSave = customAutoSave
$(document).ready(function(){
	if(window.location.href.includes('/create')){
		$('.form-horizontal .form-control, .form-check-input').each(function(index, element){
			let valContent = ''
			if($(element).attr('id') == undefined){
				valContent = localStorage.getItem(`${window.location.href}__${$(element).attr('name')}`);
				if($.inArray($(element).prop('type'), ['checkbox', 'radio', ]) !== -1){
					valContent != null ? 
					$(`.form-horizontal ${$(element).prop('tagName')}[name="${$(element).attr('name')}"]`).prop("checked", JSON.parse(valContent)) : null
					customAutoSave(false, 1, element)
				}else{
					valContent != null ? $(`.form-horizontal ${$(element).prop('tagName')}[name="${$(element).attr('name')}"]`).val(valContent):null
					customAutoSave(false, 2, element)
				}
			}else{
				valContent = localStorage.getItem(`${window.location.href}__${$(element).attr('id')}`);
				if($.inArray($(element).prop('type'), ['checkbox', 'radio', ]) !== -1){
					valContent != null ? $(`.form-horizontal #${$(element).attr('id')}`).prop("checked", JSON.parse(valContent)) :null
					customAutoSave(true, 1, element)
				}else{
					valContent != null ? $(`.form-horizontal #${$(element).attr('id')}`).val(valContent):null
					customAutoSave(true, 2, element)
				}
			}
			valContent != null ? $(element).change():null
		})
	}
});