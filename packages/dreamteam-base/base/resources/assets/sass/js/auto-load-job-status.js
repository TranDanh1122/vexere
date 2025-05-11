$(document).ready(function(){
    autoLoadData()
    $('body').on('click', '.delete-job-status', function(e){
        e.preventDefault()
        if(confirm($('#status_queued_summary').data('confirm'))) {
            let type = $(this).closest('form').find('select[name="type"]').val() || ''
            let status = $(this).closest('form').find('select[name="status"]').val() || ''
            loadAjaxPost($(this).attr('formaction'), {
                type,
                status
            }, {
                beforeSend: function(){},
                success:function(result){
                    setTimeout(function() {
                        loadData()
                    }, 200);
                    alertText(result.message, result.type);
                },
                error: function (error) {}
            }, 'animate');
        }
    });
    $('body').on('click' , '.btn-re_run', function(e){
        e.preventDefault();
        loadAjaxPost($(this).data('url'), {

        }, {
            beforeSend: function(){},
            success:function(result){
                setTimeout(function() {
                    loadData()
                }, 200);
                alertText(result.message, result.type);
            },
            error: function (error) {}
        }, 'animate');
    })
})
function autoLoadData() {
    if($('#status_queued').text() == '0' && $('#status_executing').text() == '0') return;
    loadData()
    setTimeout(function(){
        autoLoadData()
    }, 10000)
}
