$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var editorCustom = []
class CKEditorUploadAdapter {
    /**
     * Creates a new adapter instance.
     *
     */
    constructor(loader, url, t) {
        /**
         * FileLoader instance to use during the upload.
         */
        this.loader = loader;

        /**
         * Upload URL.
         *
         * @member {String} #url
         */
        this.url = url;

        /**
         * Locale translation method.
         */
        this.t = t;
    }

    /**
     * Starts the upload process.
     *
     * @returns {Promise.<Object>}
     */
    upload() {
        return this.loader.file.then(file => {
            return new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            });
        });
    }

    /**
     * Aborts the upload process.
     *
     */
    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }

    /**
     * Initializes the XMLHttpRequest object.
     *
     * @private
     */
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        xhr.open('POST', this.url, true);
        xhr.responseType = 'json';
    }

    /**
     * Initializes XMLHttpRequest listeners.
     *
     * @private
     * @param {Function} resolve Callback function to be called when the request is successful.
     * @param {Function} reject Callback function to be called when the request cannot be completed.
     * @param {File} file File instance to be uploaded.
     */
    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const loader = this.loader;
        const t = this.t;
        const genericError = t('Cannot upload file:') + ` ${file.name}.`;

        xhr.addEventListener('error', () => reject(genericError));
        xhr.addEventListener('abort', () => reject());
        xhr.addEventListener('load', () => {
            const response = xhr.response;

            if (!response || !response.url) {
                return reject(response && response.error && response.error.message ? response.error.message : genericError);
            }
            console.log(response.url)
            resolve({
                default: response.url
            });
        });

        // Upload progress when it's supported.
        /* istanbul ignore else */
        if (xhr.upload) {
            xhr.upload.addEventListener('progress', evt => {
                if (evt.lengthComputable) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            });
        }
    }

    /**
     * Prepares the data and sends the request.
     *
     * @private
     * @param {File} file File instance to be uploaded.
     */
    _sendRequest(file) {
        // Prepare form data.
        const data = new FormData();
        data.append('upload', file);
        data.append('_token', $('meta[name="csrf-token"]').attr('content')); // laravel token

        // Send request.
        this.xhr.send(data);
    }
}

class EditorManagement {
    constructor() {
        this.CKEDITOR = window.editor || {};
        this.shortcodes = [];
    }

    initCkEditor(element, extraConfig) {
        if (this.CKEDITOR[element] || !$('body').find('#' + element).is(':visible')) {
            return false;
        }

        const editor = document.querySelector('#' + element);
        ClassicEditor
            .create(editor, {
                fontSize: {
                    options: [
                        9,
                        11,
                        13,
                        'default',
                        17,
                        16,
                        18,
                        19,
                        21,
                        22,
                        23,
                        24,
                        25,
                        26,
                        27,
                        28,
                        29,
                        30,
                        32,
                        34,
                        36,
                        38,
                        40
                    ]
                },
                alignment: {
                    options: ['left', 'right', 'center', 'justify']
                },
                shortcode: {
                    onEdit: (shortcode, name = () => {
                    }) => {
                        let description = null;
                        if (this.shortcodes.length) {
                            this.shortcodes.forEach(function (item) {
                                if (item.key === name) {
                                    description = item.description;
                                    return true;
                                }
                            });
                        }
                        this.shortcodeCallback({
                            key: name,
                            href: `/admin/short-codes/ajax-get-admin-config/${name}`,
                            data: {
                                code: shortcode,
                            },
                            description: description,
                            previewImage: '',
                            update: true
                        })
                    },
                    shortcodes: this.getShortcodesAvailable(editor) || [],
                    onCallback: (shortcode, options) => {
                        this.shortcodeCallback({
                            key: shortcode,
                            href: options.url,
                            previewImage: ''
                        });
                    }
                },

                heading: {
                    options: [
                        {model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph'},
                        {model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'},
                        {model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'},
                        {model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'},
                        {model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4'},
                        {model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5'},
                        {model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6'}
                    ]
                },
                placeholder: ' ',
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'fontColor',
                        'fontSize',
                        'fontBackgroundColor',
                        'fontFamily',
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'strikethrough',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'alignment',
                        'direction',
                        'shortcode',
                        'outdent',
                        'indent',
                        '|',
                        'htmlEmbed',
                        'imageInsert',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'undo',
                        'redo',
                        'findAndReplace',
                        'removeFormat',
                        'sourceEditing',
                        'codeBlock',
                    ]
                },
                language: {
                    ui: window.siteEditorLocale || 'en',
                    content: window.siteEditorLocale || 'en',
                },
                image: {
                    toolbar: [
                        "toggleImageCaption",
                        "imageTextAlternative",
                        "imageStyle:inline",
                        "imageStyle:block",
                        "imageStyle:side",
                        "imageStyle:alignLeft",
                        "imageStyle:alignRight",
                        "imageStyle:alignBlockLeft",
                        "imageStyle:alignBlockRight",
                        "imageStyle:alignCenter",
                        'ImageResize',
                    ],
                    upload: {
                        types: ['jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg+xml']
                    }
                },
                codeBlock: {
                    languages: [
                        {language: 'plaintext', label: 'Plain text'},
                        {language: 'c', label: 'C'},
                        {language: 'cs', label: 'C#'},
                        {language: 'cpp', label: 'C++'},
                        {language: 'css', label: 'CSS'},
                        {language: 'diff', label: 'Diff'},
                        {language: 'html', label: 'HTML'},
                        {language: 'java', label: 'Java'},
                        {language: 'javascript', label: 'JavaScript'},
                        {language: 'php', label: 'PHP'},
                        {language: 'python', label: 'Python'},
                        {language: 'ruby', label: 'Ruby'},
                        {language: 'typescript', label: 'TypeScript'},
                        {language: 'xml', label: 'XML'},
                        {language: 'dart', label: 'Dart', class: 'language-dart'},
                    ]
                },
                link: {
                    defaultProtocol: 'http://',
                    decorators: {
                        openInNewTab: {
                            mode: 'manual',
                            label: 'Open in a new tab',
                            attributes: {
                                target: '_blank',
                            }
                        },
                        noFollow: {
                            mode: "manual",
                            label: "Nofollow",
                            attributes: {
                              rel: "nofollow",
                            },
                        },
                    }
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells',
                        'tableCellProperties',
                        'tableProperties'
                    ]
                },
                htmlSupport: {
                    allow: [
                        {
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }
                    ]
                },
                ...extraConfig,
            })
            .then(editor => {
                editor.plugins.get('FileRepository').createUploadAdapter = loader => {
                    return new CKEditorUploadAdapter(loader, '/admin/media/upload-from-editor', editor.t);
                };
                editor.model.document.on("change:data", () => {
                    const editorData = editor.getData();
                    localStorage.setItem(`${window.location.href}__${element}`, editorData)
                });
                // create function insert html
                editor.insertHtml = html => {
                    const viewFragment = editor.data.processor.toView(html);
                    const modelFragment = editor.data.toModel(viewFragment);
                    editor.model.insertContent(modelFragment);
                }
                editorCustom[element] = editor
                window.editorCustom = editorCustom
                window.editor = editorCustom;

                this.CKEDITOR[element] = editor;

                const minHeight = $('#' + element).prop('rows') * 90;
                const className = `ckeditor-${element}-inline`;
                $(editor.ui.view.editable.element)
                    .addClass(className)
                    .after(`
                    <style>
                        .ck-editor__editable_inline {
                            min-height: ${minHeight - 100}px;
                            max-height: ${minHeight + 100}px;
                        }
                    </style>
                `);

                // debounce content for ajax ne
                let timeout;
                editor.model.document.on('change:data', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        editor.updateSourceElement();
                    }, 150)
                });

                // insert media embed
                editor.commands._commands.get('mediaEmbed').execute = url => {
                    editor.insertHtml(`[media url="${url}"][/media]`);
                }

            })
            .catch(error => {
                console.error(error);
            });
    }

    getShortcodesAvailable(editor) {
        const $dropdown = $(editor).parents('.form-group').find('.add_shortcode_btn_trigger')?.next('.dropdown-menu');
        const lists = [];

        if ($dropdown && $dropdown.find('> li').length) {
            $dropdown.find('> li').each(function () {
                let item = $(this).find('> a');
                lists.push({
                    key: item.data('key'),
                    hasConfig: item.data('has-admin-config'),
                    name: item.text(),
                    url: item.attr('href'),
                    description: item.data('description'),
                });
            });
        }

        this.shortcodes = lists;

        return lists;
    }

    initEditor(element, extraConfig, type) {
        if (!element.length) {
            return false;
        }

        let current = this;
        switch (type) {
            case 'ckeditor':
                $.each(element, (index, item) => {
                    current.initCkEditor($(item).prop('id'), extraConfig);
                });
                break;
        }
    }

    init() {
        let $ckEditor = $(document).find('.editor-ckeditor');
        let current = this;
        if ($ckEditor.length > 0) {
            current.initEditor($ckEditor, {}, 'ckeditor');
        }

        $(document).on('click', '.show-hide-editor-btn', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            const editorInstance = _self.data('result');

            let $result = $('#' + editorInstance);

            if ($result.hasClass('editor-ckeditor')) {
                if (this.CKEDITOR[editorInstance] && typeof this.CKEDITOR[editorInstance] !== 'undefined') {
                    this.CKEDITOR[editorInstance].destroy();
                    this.CKEDITOR[editorInstance] = null;
                    $('.editor-action-item').not('.action-show-hide-editor').hide();
                } else {
                    current.initCkEditor(editorInstance, {}, 'ckeditor');
                    $('.editor-action-item').not('.action-show-hide-editor').show();
                }
            } else if ($result.hasClass('editor-tinymce')) {
                tinymce.execCommand('mceToggleEditor', false, editorInstance);
            }
        });

        this.customImage()

        this.manageShortCode();

        return this;
    }

    shortcodeCallback(params = {}) {
        const {
            href,
            key,
            description = null,
            data = {},
            update = false,
            previewImage = null
        } = params;
        $('.short-code-admin-config').html('');

        let $addShortcodeButton = $('.short_code_modal .add_short_code_btn');

        if (update) {
            $addShortcodeButton.text($addShortcodeButton.data('update-text'));
        } else {
            $addShortcodeButton.text($addShortcodeButton.data('add-text'));
        }

        if (description != null) {
            $('.short_code_modal .modal-title strong').text(description);
        }

        if (previewImage != null && previewImage !== '') {
            $('.short_code_modal .shortcode-preview-image-link').attr('href', previewImage).show();
        } else {
            $('.short_code_modal .shortcode-preview-image-link').hide();
        }


        $.ajax({
            type: 'POST',
            data: {
                href,
                key,
                description,
                data,
                update,
                previewImage,
                recordLangLocale
            },
            url: href,
            success: res => {
                if (res.error) {
                    alertText(res.message, 'error');
                    return false;
                }
                $('.short_code_modal').modal('show');
                $('.half-circle-spinner').show();
                $('.short-code-data-form').trigger('reset');
                $('.short_code_input_key').val(key);
                $('.half-circle-spinner').hide();
                $('.short-code-admin-config').html(res.data);
            },
            error: data => {
                console.log(data)
            }
        });
    }

    manageShortCode() {
        const self = this;
        var editorInstance = $('.add_shortcode_btn_trigger').data('result');
        $('body').on('click', '.bb-shortcode-button, code.bb-shortcode', function (event) {
            editorInstance = $(this).closest('.form-group').find('.add_shortcode_btn_trigger').data('result');
        })
        $('.list-shortcode-items li a').on('click', function (event) {
            event.preventDefault();
            editorInstance = $(this).closest('.form-group').find('.add_shortcode_btn_trigger').data('result');
            if ($(this).data('has-admin-config') == '1') {

                self.shortcodeCallback({
                    href: $(this).prop('href'),
                    key: $(this).data('key'),
                    description: $(this).data('description'),
                    previewImage: $(this).data('preview-image'),
                });

            } else {

                const shortcode = '[' + $(this).data('key') + '][/' + $(this).data('key') + ']';

                if ($('.editor-ckeditor').length > 0) {
                    window.editor[editorInstance].commands.execute('shortcode', shortcode);
                } else if ($('.editor-tinymce').length > 0) {
                    tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode);
                }
            }
        });

        $.fn.serializeObject = function () {
            let o = {};
            let a = this.serializeArray();
            $.each(a, function () {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });

            return o;
        };

        $('.add_short_code_btn').on('click', function (event) {
            event.preventDefault();
            let formElement = $('.short_code_modal').find('.short-code-data-form');
            let check = true
            formElement.find('.validate').each(function() {
                if($(this).val() == '' || $(this).val() == null || $(this).val() == undefined) {
                    check = false
                    $(this).css({'border-color': 'red'})
                }
            })
            if(!check) {
                alertText($(this).data('require') || 'Các trường có dấu * là bắt buộc!', 'error')
                return
            }
            let formData = formElement.serializeObject();
            let attributes = '';

            $.each(formData, function (name, value) {
                let element = formElement.find('*[name="' + name + '"]');
                let shortcodeAttribute = element.data('shortcode-attribute');
                if ((!shortcodeAttribute || shortcodeAttribute !== 'content') && value) {
                    name = name.replace('[]', '');
                    if (element.data('shortcode-attribute') !== 'content') {
                        name = name.replace('[]', '');
                        if(typeof value === 'string' || value instanceof String) {
                            value = value.replace(/"(.*?)"/g, '“$1”');
                        }
                        attributes += ' ' + name + '="' + value + '"';
                    }
                }
            });

            let content = '';
            let contentElement = formElement.find('*[data-shortcode-attribute=content]');
            if (contentElement != null && contentElement.val() != null && contentElement.val() !== '') {
                content = contentElement.val();
                content = content.replaceAll('</undefined>', '')
                content = content.trim()
            }

            const $shortCodeKey = $(this).closest('.short_code_modal').find('.short_code_input_key').val();

            const shortcode = '[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']';
            if ($('.editor-ckeditor').length > 0) {
                window.editor[editorInstance].commands.execute('shortcode', shortcode);
            } else if ($('.editor-tinymce').length > 0) {
                tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode);
            } else {
                const coreInsertShortCodeEvent = new CustomEvent('core-insert-shortcode', { detail: { shortcode: shortcode } })
                document.dispatchEvent(coreInsertShortCodeEvent)
            }

            $(this).closest('.modal').modal('hide');
        });
    }

    customImage() {
        $('body').on('click', '.ck-file-dialog-button', function(e) {
            e.preventDefault();
            const editorInstance = $(this).closest('.form-group').find('.add_shortcode_btn_trigger').data('result');
            let admin_dir = $("meta[name=admin_dir]").attr("content");
            let url_media =
                "/" +
                admin_dir +
                "/media?uploads=ckeditor&field_id=" +
                editorInstance +
                "&only=image";
            $("#media").find("iframe").attr("src", url_media);
            $("#media").modal("toggle");
        })
    }

}

$(document).ready(() => {
    window.EDITOR = new EditorManagement().init();
    window.EditorManagement = window.EditorManagement || EditorManagement;
});
function initFunctionCkeditor(ckeditor_name) {
    let parent_name = ".editor_" + ckeditor_name;
    $('body').on('click', '.btn_writer', function()  {
        ckeditor_name = $(this).data('result')
        aiAutoContentWrite($(this).text())
    })
    $('body').on('click', '.btn_rewriter', function()  {
        ckeditor_name = $(this).data('result')
        aiAutoContentRewrite($(this).text())
    })
    const aiAutoContentWrite = (text) => {
        let editor = window.editor[ckeditor_name];
        let sHtmlSelection = editor.data.stringify(editor.model.getSelectedContent(editor.model.document.selection));
        let box = $(`#${ckeditor_name}_rewrite_box`);
        box.show();
        box.find(`input.selected-text`).val(sHtmlSelection);

        getContentRewriteFromGPT('write');
        box.find('.rewrite_box__header .title').html(`
            <i class="fa fa-pencil"></i> ${text}
        `);

        box.find('.rewrite_box__header__bottom').hide();
    }
    const aiAutoContentRewrite = (text) => {
        let editor = window.editor[ckeditor_name];
        let sHtmlSelection = editor.data.stringify(editor.model.getSelectedContent(editor.model.document.selection));
        let box = $(`#${ckeditor_name}_rewrite_box`);
        box.show();
        box.find(`input.selected-text`).val(sHtmlSelection);
        box.find('.rewrite_box__header .title').html(`
            <i class="fa fa-paint-brush"></i> ${text} </span>
        `);

        box.find('.rewrite_box__header__bottom').show();
    }
    $('body').on('click', `#${ckeditor_name}_rewrite_box .btn-go`, () => {
        let type = $(`#${ckeditor_name}_rewrite_box .type-rewrite`).val();
        getContentRewriteFromGPT(type);
    });

    $('body').on('click', `#${ckeditor_name}_rewrite_box .redo`, () => {
        let type = $(`#${ckeditor_name}_rewrite_box .current-type`).val();
        getContentRewriteFromGPT(type);
    });

    $('body').on('click', `#${ckeditor_name}_rewrite_box .option .copy`, (e) => {
        const copyText = (html) => {
            let text = $(html).text();

            if(navigator.clipboard){
                navigator.clipboard.writeText(text);

            }else{
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(text).select();
                if(document.execCommand('copy')) {
                    // copied
                } else {
                    Clipboard.copy(text);
                }
                $temp.remove();
            }
        }
        let btn = $(e.target);
        let option = btn.closest('.option');
        let html = option.find('.option_body').html().trim();
        copyText(html);
        btn.find('span').text('Copied');
    });

    $('body').on('click', `#${ckeditor_name}_rewrite_box .option .insert`, (e) => {
        const insertText = (html, is_replace = 0) => {
            let editor = window.editor[ckeditor_name];

            editor.model.change( writer => {
                let viewFragment = editor.data.processor.toView( html );
                let modelFragment = editor.data.toModel( viewFragment );

                if(is_replace){
                    editor.model.insertContent( modelFragment);
                }else{
                    editor.model.insertContent( modelFragment, editor.model.document.selection.getLastPosition());
                }
            });

            editor.editing.view.focus();
            editor.editing.view.scrollToTheSelection();

        }
        let btn = $(e.target);
        let option = btn.closest('.option');
        let type = option.data('type');
        let html = option.find('.option_body').html().trim();
        insertText(html, type =='write' ? 0 : 1 );
        btn.find('span').text('Inserted');
    });

    let topPositionInput = $(`.editor_${ckeditor_name}`).offset().top;

    jQuery(window).scroll( function( ) {
        let currentTop = jQuery(window).scrollTop();
        let bottomPositionInput = topPositionInput + $(`.editor_${ckeditor_name}`).height();
        let rewriteBox = $(`#${ckeditor_name}_rewrite_box`);
        let rewriteBoxHeight = rewriteBox.height();
        if(currentTop < topPositionInput){
            rewriteBox.css({position:'relative'});
        }else if((currentTop + rewriteBoxHeight) >= bottomPositionInput){
            rewriteBox.css({position:'absolute', 'top': bottomPositionInput - rewriteBoxHeight -topPositionInput, right: 10, left: 10});
        }else{
            rewriteBox.css({position:'absolute', 'top': currentTop - topPositionInput+10, right: 10, left: 10});
        }
    });

    const getContentRewriteFromGPT = (type) => {
        const getContent = () => {
            let content = window.editor[ckeditor_name].getData() || $(`textarea[name="${field_detail}"]`).val();
            let div_content = $('<div></div>').html(content);
            return div_content;
        }
        const getOutline = () => {
            let heading_tags = getContent().find('h1, h2, h3, h4, h5, h6');
            return $.map(heading_tags, (el, i)=>{
                    return el.outerHTML;
            }).join(' ');

        }

        const getClosestHeading = (text) => {
            let div_content = getContent();
            let html_content = div_content.html();
            let index_text = html_content.indexOf(text);
            if(index_text == -1){
                return '';
            }
            let child_html = html_content.substring(0, index_text);
            let parser = new DOMParser();
            let doc = parser.parseFromString(child_html, "text/html");
            let headings = doc.querySelectorAll("h1, h2, h3, h4, h5, h6");
            if(!headings.length){
                return '';
            }

            return headings[headings.length - 1].textContent;
        }

        const renderLoading = (open = 1) => {
            $(`#${ckeditor_name}_rewrite_box .rewrite_result_list .item_loading`).remove();
            if(open){
                let html = `<div class="option item_loading">
                                <svg aria-labelledby="xyrsghc-aria" role="img" width="100%" height="100%" viewBox="0 0 340 84"><title id="xyrsghc-aria">Loading...</title><rect role="presentation" x="0" y="0" width="100%" height="100%" clip-path="url(#xyrsghc-diff)" style="fill: url(&quot;#xyrsghc-animated-diff&quot;);"></rect><defs><clipPath id="xyrsghc-diff"><rect x="0.541992" y="0.28125" width="100%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="41.394" width="100%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="20.8374" width="90%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="61.9502" width="90%" height="10.7675" fill="#D9D9D9"></rect></clipPath><linearGradient id="xyrsghc-animated-diff"><stop offset="0%" stop-color="#f3f3f3" stop-opacity="1"><animate attributeName="offset" values="-2; -2; 1" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop><stop offset="50%" stop-color="#ecebeb" stop-opacity="1"><animate attributeName="offset" values="-1; -1; 2" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop><stop offset="100%" stop-color="#f3f3f3" stop-opacity="1"><animate attributeName="offset" values="0; 0; 3" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop></linearGradient></defs></svg>
                            </div>
                            <div class="option item_loading">
                                <svg aria-labelledby="xyrsghc-aria" role="img" width="100%" height="100%" viewBox="0 0 340 84"><title id="xyrsghc-aria">Loading...</title><rect role="presentation" x="0" y="0" width="100%" height="100%" clip-path="url(#xyrsghc-diff)" style="fill: url(&quot;#xyrsghc-animated-diff&quot;);"></rect><defs><clipPath id="xyrsghc-diff"><rect x="0.541992" y="0.28125" width="100%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="41.394" width="100%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="20.8374" width="90%" height="10.7675" fill="#D9D9D9"></rect><rect x="0.541992" y="61.9502" width="90%" height="10.7675" fill="#D9D9D9"></rect></clipPath><linearGradient id="xyrsghc-animated-diff"><stop offset="0%" stop-color="#f3f3f3" stop-opacity="1"><animate attributeName="offset" values="-2; -2; 1" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop><stop offset="50%" stop-color="#ecebeb" stop-opacity="1"><animate attributeName="offset" values="-1; -1; 2" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop><stop offset="100%" stop-color="#f3f3f3" stop-opacity="1"><animate attributeName="offset" values="0; 0; 3" keyTimes="0; 0.25; 1" dur="2s" repeatCount="indefinite"></animate></stop></linearGradient></defs></svg>
                            </div>`;
                $(`#${ckeditor_name}_rewrite_box .rewrite_result_list`).prepend(html);
            }


        }


        const renderError = (message) => {
            let html = `<div class="option">
                            <div class="option_header">
                                <span>Errors: </span>
                            </div>
                            <div class="option_body">
                                <p class="error">${message}</p>
                            </div>
                        </div>`;
            $(`#${ckeditor_name}_rewrite_box .rewrite_result_list`).prepend(html);
        }


        const renderOption = (messages, type) => {
            let html = '';
            messages.forEach((ans, i) => {
                let title = `Option ${i+1}`;
                ans = ans.trim().replace("\r", "").replace("\n\n", "<br />").replace("\n", "<br />");

                html += `<div class="option" data-type="${type}">
                            <div class="option_header">
                                <span>${title}</span>
                            </div>
                            <div class="option_body">
                                ${ans}
                            </div>
                            <div class="option_footer">
                                <div class="option_footer__tool insert">
                                    <i class="fa fa-arrow-circle-o-left"></i> Insert
                                </div>
                                <div class="option_footer__tool copy">
                                    <i class="fa fa-copy"></i> Copy
                                </div>
                            </div>
                        </div>`;
            });

            $(`#${ckeditor_name}_rewrite_box .rewrite_result_list`).prepend(html);
        }


        let rewriteBox = $(`#${ckeditor_name}_rewrite_box`);
        rewriteBox.find('input.current-type').val(type);
        let text = rewriteBox.find('input.selected-text').val();
        let heading = getClosestHeading(text);
        let field_title = rewriteBox.data('field_title');
        let title = $(`input[name="${field_title}"`).val().trim();
        let outline = getOutline();

        // Run
        console.log('Bắt đầu viết bài chatgpt');
        renderLoading();

        rewriteBox.find('.rewrite_result_list').animate({ scrollTop: 0 }, 'slow');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
                type, text, title, heading, outline
            },

            url: '/admin/ajax/getRewriteContentFromChatGPT',

            success: function (result) {
                if(result.success == 1){
                    data = result.data;
                    ans = data.answer;
                    renderOption(ans, type);

                }else{
                    renderError(result.message);
                }
                renderLoading(false);
                rewriteBox.find('.rewrite_box__header__bottom').hide();

                console.log('Hoàn tất viết bài chatgpt');

            },
            error: function (error) {
                rewriteBox.find('.rewrite_box__header__bottom').hide();
                renderError(error, 1);
                renderLoading(false);
                console.log('Hoàn tất viết bài chatgpt');
            },
            // async: false
        });

    }
}
