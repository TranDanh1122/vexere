<div class="form-actions-inline">
	<div class="form-actions__group float-left">
		<button type="button" class="btn btn-sm btn-primary" id="test_mail_btn">{{ __('Core::admin.setting.email.check') }}</button>
	</div>
</div>
<div id="test_mail_notificate">{{__('Core::admin.setting.email.wait_check_mail')}}</div>
<script>
	$(document).ready(function() {
		$('body').on('click', '#test_mail_btn', function() {
			email = $('#test_mail').val();
			if (email == '') {
				alertText('{{__('Core::admin.setting.email.requied')}}', 'warning');
			} else {
				data = $(this).closest('form').serialize()+'&email='+email;
				loadAjaxPost('{{route('admin.settings.test_mail')}}', data, {
					beforeSend: function(){
				        $('#test_mail_notificate').html('{{__('Core::admin.setting.email.wait_check_mail')}}');
				        $('#test_mail_notificate').show();
				    },
				    success:function(result){
				        $('#test_mail_notificate').html(result.message);
				    },
				    error: function (error) {
				        $('#test_mail_notificate').html('{{__('Core::admin.setting.email.error')}} <br>'+error.responseJSON.message);
				    }
				});
			}
		});
	});
</script>