$(document).ready(function() {
	$('body').on('click', '#login button[type=submit]', function(e) {
		e.preventDefault();
		form_action = $(this).closest('form').attr('action');
		name = $('#name').val();
		password = $('#password').val();
		message = $(this).data('message');
		if ($('#remember-check').is(':checked')) {
			remember = true;
		} else {
			remember = false;
		}
		if (name == '' && password == '') {
			alertText(required_info, 'error');
		} else if (name == '') {
			alertText(required_name, 'error');
		} else if (password == '') {
			alertText(required_password, 'error');
		} else {
			data = {
				name:name,
				password:password,
				remember:remember,
			};
			loadAjaxPost(form_action, data, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message, 'success');
	        			window.location.href = result.url;
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {
		        	if(error.status == 429){
		        		alertText(message, 'error');
		        	}
		        }
		    });
		}
	});
	// Quên mật khẩu
	$('body').on('click', '#forgot_password_comfirm',function() {
		email = $('#forgot_password_email').val();
		form_action = $(this).attr('action');
		if (email == '') {
			alertText(required_forgot_email, 'error');
		} else {
			loadAjaxPost(form_action, { email: email }, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		$('#forgot_password').modal('toggle');
		        		$('#forgot_password_email').val('');
		        		alertText(result.message, 'success');
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
		    });
		}
	});
	// Đổi mật khẩu
	$('body').on('click', '#change_password button[type=submit]', function(e) {
		e.preventDefault();
		form_action = $(this).closest('form').attr('action');
		password = $('#password').val();
		password_comfirm = $('#password_comfirm').val();
		if (password == '' && password_comfirm == '') {
			alertText(required_info, 'error');
		} else if (password == '') {
			alertText(required_password, 'error');
		} else if (password_comfirm == '') {
			alertText(required_password_comfirm, 'error');
		} else if(password_comfirm != password) {
			alertText(required_equal, 'error');
		} else if (passwordStrength(password) < 3) {
			alertText(required_strong, 'error');
		} else {
			data = {
				password: password,
				password_comfirm: password_comfirm,
			};
			loadAjaxPost(form_action, data, {
		        beforeSend: function(){},
		        success:function(result){
		        	if (result.status == 1) {
		        		alertText(result.message, 'success');
	        			window.location.href = result.url;
		        	} else {
		        		alertText(result.message, 'warning');
		        	}
		        },
		        error: function (error) {}
		    });
		}
	});
});