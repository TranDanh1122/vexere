$(document).ready((function(){
	initCodeEditor("custom_css");
    initCodeEditor("custom_css_desktop");
    initCodeEditor("custom_css_mobile");
	})
);
function initCodeEditor(id, type = 'css') {
    $(document).find('#' + id).wrap('<div id="wrapper_' + id + '"><div class="container_content_codemirror"></div> </div>');
    $('#wrapper_' + id).append('<div class="handle-tool-drag" id="tool-drag_' + id + '"></div>');
    CodeMirror.fromTextArea(document.getElementById(id), {
        extraKeys: {'Ctrl-Space': 'autocomplete'},
        lineNumbers: true,
        mode: type,
        autoRefresh: true,
        lineWrapping: true,
    });

    $('.handle-tool-drag').mousedown(event => {
        let _self = $(event.currentTarget);
        _self.attr('data-start_h', _self.parent().find('.CodeMirror').height()).attr('data-start_y', event.pageY);
        $('body').attr('data-dragtool', _self.attr('id')).on('mousemove', onDragTool);
        $(window).on('mouseup', onReleaseTool);
    });
}
function onDragTool(e) {
    let ele = '#' + $('body').attr('data-dragtool');
    let start_h = parseInt($(ele).attr('data-start_h'));

    $(ele).parent().find('.CodeMirror').css('height', Math.max(200, start_h + e.pageY - $(ele).attr('data-start_y')));
}

function onReleaseTool() {
    $('body').off('mousemove', onDragTool);
    $(window).off('mouseup', onReleaseTool);
}
