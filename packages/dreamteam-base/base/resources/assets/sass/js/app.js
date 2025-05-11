import { axios, HttpClient } from "./utilities";
import Toastify from './utilities/toast'

window._ = require("lodash");

window.axios = axios;

window.$httpClient = new HttpClient();

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(() => {
    setTimeout(() => {
        if (
            typeof siteAuthorizedUrl === "undefined" ||
            typeof isAuthenticated === "undefined" ||
            !isAuthenticated
        ) {
            return;
        }

        const $reminder = $('[data-bb-toggle="authorized-reminder"]');

        if ($reminder.length) {
            return;
        }

        $httpClient
            .makeWithoutErrorHandler()
            .get(siteAuthorizedUrl, { verified: true })
            .then(() => null)
            .catch((error) => {
                if (!error.response || error.response.status !== 200) {
                    return;
                }

                $(error.response.data.data.html).prependTo("body");
                $(document).find(".alert-license").slideDown();
            });
    }, 1000);
});

class DreamTeamCore {
    static noticesTimeout = {}
    static noticesTimeoutCount = 500

    constructor() {
        DreamTeamCore.initGlobalResources()
        DreamTeamCore.initMediaIntegrate()
    }

    static showNotice(messageType, message, messageHeader = '') {
        let key = `notices_msg.${messageType}.${message}`
        let color = ''
        let icon = ''

        if (DreamTeamCore.noticesTimeout[key]) {
            clearTimeout(DreamTeamCore.noticesTimeout[key])
        }

        DreamTeamCore.noticesTimeout[key] = setTimeout(() => {
            if (!messageHeader) {
                switch (messageType) {
                    case 'error':
                        messageHeader = DreamTeamCoreVariables.languages.notices_msg.error
                        break
                    case 'success':
                        messageHeader = DreamTeamCoreVariables.languages.notices_msg.success
                        break
                }
            }

            switch (messageType) {
                case 'error':
                    color = '#f44336'
                    icon =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>'
                    break
                case 'success':
                    color = '#4caf50'
                    icon =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>'
                    break
            }

            Toastify({
                text: message,
                duration: 5000,
                close: true,
                gravity: 'bottom',
                position: 'right',
                stopOnFocus: true,
                escapeMarkup: false,
                icon: icon,
                style: {
                    background: color,
                },
            }).showToast()
        }, DreamTeamCore.noticesTimeoutCount)
    }
    static showError(message, messageHeader = '') {
        this.showNotice('error', message, messageHeader)
    }

    static showSuccess(message, messageHeader = '') {
        this.showNotice('success', message, messageHeader)
    }

    static handleError(data) {
        if (typeof data.errors !== 'undefined' && !_.isArray(data.errors)) {
            DreamTeamCore.handleValidationError(data.errors)
        } else {
            if (typeof data.responseJSON !== 'undefined') {
                if (typeof data.responseJSON.errors !== 'undefined') {
                    if (data.status === 422) {
                        DreamTeamCore.handleValidationError(data.responseJSON.errors)
                    }
                } else if (typeof data.responseJSON.message !== 'undefined') {
                    DreamTeamCore.showError(data.responseJSON.message)
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            DreamTeamCore.showError(item)
                        })
                    })
                }
            } else {
                DreamTeamCore.showError(data.statusText)
            }
        }
    }

    static handleValidationError(errors) {
        let message = ''
        $.each(errors, (index, item) => {
            message += item + '\n'
        })
        DreamTeamCore.showError(message)
    }
    /**
     * @param {HTMLElement} element
     * @param {Boolean} overlay
     * @param {String} position
     */
    static showButtonLoading(element, overlay = true, position = "start") {
        if (overlay && element) {
            $(element).addClass("btn-loading").attr("disabled", true);

            return;
        }

        const loading =
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
        const icon = $(element).find("svg");

        if (icon.length) {
            icon.addClass("d-none");
        }

        if (position === "start") {
            $(element).prepend(loading);
        } else if (position === "end") {
            $(element).append(loading);
        }
    }

    static hideButtonLoading(element) {
        if (!element) {
            return;
        }

        if ($(element).hasClass("btn-loading")) {
            $(element).removeClass("btn-loading").removeAttr("disabled");

            return;
        }

        $(element).find(".spinner-border").remove();
        $(element).find("svg").removeClass("d-none");
    }

    /**
     * @param {HTMLElement} element
     */
    static showLoading(element = null) {
        let check = null
        if (!element) {
            element = document.querySelector(".loading-wrapper");
            check = 1
        } else {
            $(element).addClass("position-relative");
        }

        if ($(element).find(".loading-spinner").length) {
            return;
        }
        if (check) {
            $(element).css({left: 0,height: '100vh',position: 'fixed',top: 0,right: 0});
        }
        $(element).append('<div class="loading-spinner"></div>');
    }

    static hideLoading(element = null) {
        if (!element) {
            element = document.querySelector(".loading-wrapper");
            $(element).css({position: 'relative', height: 0});
        }

        $(element).removeClass("position-relative");
        $(element).find(".loading-spinner").remove();
    }
    static initFieldCollapse() {
        $(document).on('click, change', '[data-bb-toggle="collapse"]', function (e) {
            const target = $(this).data('bb-target')

            let targetElement = null

            switch (e.currentTarget.type) {
                case 'checkbox':
                    targetElement = $(document).find(target)
                    const isReverse = $(this).data('bb-reverse')
                    const isChecked = $(this).prop('checked')

                    if (isReverse) {
                        isChecked ? targetElement.slideUp() : targetElement.slideDown()
                    } else {
                        isChecked ? targetElement.slideDown() : targetElement.slideUp()
                    }
                    break

                case 'radio':
                case 'select-one':
                    targetElement = $(document).find(`${target}[data-bb-value="${$(this).val()}"]`)

                    const targets = $(document).find(`${target}[data-bb-value]`)

                    if (targetElement.length) {
                        targets.not(targetElement).slideUp()
                        targetElement.slideDown()
                    } else {
                        targets.slideUp()
                    }
                    break

                case 'button':
                    targetElement = $(document).find(target)

                    if (targetElement.length) {
                        targetElement.slideToggle()
                    }
                    break

                default:
                    console.warn(`[DreamTeamCore] Unknown type ${e.currentTarget.type} of collapse`)

                    break
            }
        })
    }

    /**
     * @param {String[]|HTMLElement} sources
     * @return {FsLightbox}
     */
    static lightbox(sources) {
        const lightbox = new FsLightbox()

        if (Array.isArray(sources)) {
            lightbox.props.sources = sources
            lightbox.open()
        }

        return lightbox
    }

    static initLightbox() {
        let instance = window.lightboxInstance || {}

        const a = document.querySelectorAll('a[data-bb-lightbox]')

        if (!a.length) {
            return
        }

        a.forEach((element) => {
            const instanceName = element.dataset.bbLightbox

            if (!instance[instanceName]) {
                instance[instanceName] = DreamTeamCore.lightbox()
            }

            const source = element.href

            instance[instanceName].props.sources.push(source)
            instance[instanceName].elements.a.push(element)

            const currentIndex = instance[instanceName].props.sources.length - 1

            element.addEventListener('click', (e) => {
                e.preventDefault()

                instance[instanceName].open(currentIndex)
            })
        })

        window.lightboxInstance = instance
    }

    static initResources() {
        if (jQuery().tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip({ placement: 'top', boundary: 'window' })
        }

        if (jQuery().areYouSure) {
            $('form.dirty-check').areYouSure()
        }

        if (jQuery().textareaAutoSize) {
            $('textarea.textarea-auto-height').textareaAutoSize()
        }
        DreamTeamCore.initLightbox()

        document.dispatchEvent(new CustomEvent('core-init-resources'))
    }

    static initGlobalResources() {

        $(document).on('click', '.modal [data-close-modal]', function(e){
            e.preventDefault();
            let target = $(this).closest('.modal').attr('id')
            $('#' + target).modal('hide')
        });

        $(document).on('submit', '.js-base-form', (event) => {
            $(event.currentTarget).find('button[type=submit]').addClass('disabled')
        })

        $(document).on('change', '.media-image-input', function () {
            const input = this

            if (input.files && input.files.length > 0) {
                const reader = new FileReader()
                reader.onload = function (e) {
                    $(input).closest('.image-box').find('.preview-image').prop('src', e.target.result)
                }

                reader.readAsDataURL(input.files[0])
            }
        })

        $(document).on('click', '.media-select-file', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $(this).closest('.attachment-wrapper').find('.media-file-input').trigger('click')
        })

        DreamTeamCore.initFieldCollapse()
    }

    static openMediaUsing(callback) {}

    static handleOpenMedia(item) {}

    static initMediaIntegrate() {
        if (jQuery().rvMedia) {
            DreamTeamCore.gallerySelectImageTemplate = `
            <div class='custom-image-box image-box'>
                <input type='hidden' name='__name__' value='' class='image-data'>
                    <div class='preview-image-wrapper w-100'>
                    <div class='preview-image-inner'>
                        <img src='${RV_MEDIA_CONFIG.default_image}' alt='${RV_MEDIA_CONFIG.translations.preview_image}' class='preview-image'>
                        <div class='image-picker-backdrop'></div>
                        <span class='image-picker-remove-button'>
                            <button data-bb-toggle='image-picker-remove' class='btn btn-sm btn-icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm icon-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M18 6l-12 12" />
                                  <path d="M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <div data-bb-toggle='image-picker-edit' class='image-box-actions cursor-pointer'></div>
                    </div>
                </div>
            </div>`

            DreamTeamCore.gallerySelectFileTemplate = `
            <div class="image-box attachment-wrapper">
                <input type='hidden' name='__name__' value='' class='attachment-url'>
                <div class='preview-image-wrapper w-100'>
                    <div class='preview-image-inner' style="padding: 0;">
                        <div class='image-picker-backdrop'></div>
                        <div class="attachment-info">
                            <a href="" target="_blank" title=""></a>
                        </div>
                        <span class='image-picker-remove-button'>
                            <button data-bb-toggle='image-picker-remove' class='btn btn-sm btn-icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm icon-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M18 6l-12 12" />
                                  <path d="M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <div data-bb-toggle='file-picker-edit' data-action="__action__" class='image-box-actions cursor-pointer'></div>
                    </div>
                    <a class="preview-file" href="" target="_blank">Preview</a>
                </div>
            </div>`;

            const $btnGalleries = $('.btn_gallery')

            if ($btnGalleries.length > 0) {
                $btnGalleries.each(function () {
                    const item = $(this)

                    $(item).rvMedia({
                        multiple: false,
                        filter: $(item).data('action') === 'select-image' ? 'image' : 'everything',
                        view_in: 'all_media',
                        onSelectFiles: (files, $el) => {
                            switch ($el.data('action')) {
                                case 'media-insert-ckeditor':
                                    let content = ''
                                    $.each(files, (index, file) => {
                                        let link = file.full_url
                                        if (file.type === 'youtube') {
                                            link = link.replace('watch?v=', 'embed/')
                                            content +=
                                                '<iframe width="420" height="315" src="' +
                                                link +
                                                '" frameborder="0" allowfullscreen loading="lazy"></iframe><br />'
                                        } else if (file.type === 'image') {
                                            const alt = file.alt ? file.alt : file.name
                                            content +=
                                                '<img src="' + link + '" alt="' + alt + '" loading="lazy"/><br />'
                                        } else {
                                            content += '<a href="' + link + '">' + file.name + '</a><br />'
                                        }
                                    })

                                    window.EDITOR.CKEDITOR[$el.data('result')].insertHtml(content)

                                    break
                                case 'media-insert-tinymce':
                                    let html = ''
                                    $.each(files, (index, file) => {
                                        let link = file.full_url
                                        if (file.type === 'youtube') {
                                            link = link.replace('watch?v=', 'embed/')
                                            html += `<iframe width='420' height='315' src='${link}' allowfullscreen loading='lazy'></iframe><br />`
                                        } else if (file.type === 'image') {
                                            const alt = file.alt ? file.alt : file.name
                                            html += `<img src='${link}' alt='${alt}' loading='lazy'/><br />`
                                        } else {
                                            html += `<a href='${link}'>${file.name}</a><br />`
                                        }
                                    })
                                    tinymce.activeEditor.execCommand('mceInsertContent', false, html)
                                    break
                                case 'select-image':
                                    let firstImage = _.first(files)
                                    const $imageBox = $el.closest('.image-box')
                                    const allowThumb = $el.data('allow-thumb')
                                    $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                                    $imageBox
                                        .find('.preview-image')
                                        .attr(
                                            'src',
                                            allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url
                                        )
                                    $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                                    $imageBox.find('.preview-image').removeClass('default-image')
                                    $imageBox.find('.preview-image-wrapper').show()
                                    break
                                case 'attachment':
                                    const attachment = _.first(files)
                                    const wrapper = $el.closest('.attachment-wrapper')
                                    wrapper.find('.attachment-url').val(attachment.url)
                                    wrapper.find('.attachment-info').html(`
                                        <a href="${attachment.full_url}" target="_blank" title="${attachment.name}">${attachment.url}</a>
                                        <small class="d-block">${attachment.size}</small>
                                    `)

                                    wrapper.find('[data-bb-toggle="media-file-remove"]').show()
                                    wrapper.find('.attachment-details').removeClass('hidden')
                                    break
                                default:
                                    const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                                        detail: {
                                            files: files,
                                            element: $el,
                                        },
                                    })
                                    document.dispatchEvent(coreInsertMediaEvent)
                            }
                        },
                    })
                })
            }

            const gallerySelectImages = function (files, $currentBoxList, excludeIndexes = []) {
                let template = DreamTeamCore.gallerySelectImageTemplate
                const allowThumb = $currentBoxList.data('allow-thumb')
                _.forEach(files, (file, index) => {
                    if (_.includes(excludeIndexes, index)) {
                        return
                    }
                    let imageBox = template.replace(/__name__/gi, $currentBoxList.data('name'))

                    let $template = $(
                        '<div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">' + imageBox + '</div>'
                    )

                    $template.find('.image-data').val(file.url).trigger('change')
                    $template
                        .find('.preview-image')
                        .attr('src', allowThumb ? file.thumb : file.full_url)
                        .show()
                    if (!allowThumb) {
                        $template.find('.preview-image-wrapper').addClass('preview-image-wrapper-not-allow-thumb')
                    }
                    $currentBoxList.append($template)
                    $currentBoxList.closest('.list-images').find('.footer-action').show()
                })
            }

            const gallerySelectFiles = function (files, $currentBoxList, excludeIndexes = []) {
                let template = DreamTeamCore.gallerySelectFileTemplate
                _.forEach(files, (file, index) => {
                    if (_.includes(excludeIndexes, index)) {
                        return
                    }
                    let imageBox = template.replace(/__name__/gi, $currentBoxList.data('name'))
                    imageBox = imageBox.replace(/__action__/gi, $currentBoxList.data('action'))

                    let $template = $(
                        '<div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">' + imageBox + '</div>'
                    )

                    $template.find('.attachment-url').val(file.url)
                    $template.find('.attachment-info').html(`
                        <a href="${file.full_url}" target="_blank" title="${file.name}">${file.name}</a>
                    `)
                    $template.find('.preview-file').attr('src', file.full_url)
                    $currentBoxList.append($template)
                    $currentBoxList.closest('.list-images').find('.footer-action').show()
                })
            }

            $.each(
                $(document).find('[data-bb-toggle="gallery-add"]'),
                function (index, item) {
                    const _self = $(item)

                    _self.rvMedia({
                        filter: $(item).data('action') === 'video' ? 'video' : 'image',
                        view_in: 'all_media',
                        allow_webp: _self.data('allow-webp'),
                        allow_thumb: _self.data('allow-thumb'),
                        module_name: _self.data('module-name'),
                        onSelectFiles: (files, $el) => {
                            let $currentBoxList = $el
                                .closest('.gallery-images-wrapper');
                            $currentBoxList.find('.default-placeholder-gallery-image').addClass('hidden')
                            if($currentBoxList.find('.attachment-wrapper').length > 0) {
                                $currentBoxList = $currentBoxList.find('.attachment-wrapper .list-gallery-media-images')
                                $currentBoxList.removeClass('hidden')
                                gallerySelectFiles(files, $currentBoxList)
                            } else {
                                $currentBoxList = $currentBoxList.find('.images-wrapper .list-gallery-media-images')
                                $currentBoxList.removeClass('hidden')
                                gallerySelectImages(files, $currentBoxList)
                            }
                        },
                    })
                })

            new RvMediaStandAlone('[data-bb-toggle="image-picker-edit"]', {
                filter: 'image',
                view_in: 'all_media',
                onSelectFiles: (files, $el) => {
                    let firstItem = _.first(files)

                    let $currentBox = $el.closest('.gallery-image-item-handler').find('.image-box')
                    let $currentBoxList = $el.closest('.list-gallery-media-images')
                    const allowThumb = $currentBoxList.data('allow-thumb')

                    $currentBox.find('.image-data').val(firstItem.url).trigger('change')
                    $currentBox
                        .find('.preview-image')
                        .attr('src', allowThumb ? firstItem.thumb : firstItem.full_url)
                        .show()

                    gallerySelectImages(files, $currentBoxList, [0])
                },
            })
            $('body').on('click', '[data-bb-toggle="file-picker-edit"]', function(e) {
                const action = $(this).data('action')
                new RvMediaStandAlone('[data-bb-toggle="file-picker-edit"]', {
                    filter: action || 'everything',
                    view_in: 'all_media',
                    onSelectFiles: (files, $el) => {
                        let firstItem = _.first(files)
                        let $currentBox = $el.closest('.gallery-image-item-handler').find('.image-box')
                        let $currentBoxList = $el.closest('.list-gallery-media-images')

                        $currentBox.find('.attachment-url').val(firstItem.url)
                        $currentBox.find('.attachment-info').html(`
                            <a href="${firstItem.full_url}" target="_blank" title="${firstItem.name}">${firstItem.name}</a>
                        `)
                        $currentBox.find('.preview-file').attr('href', firstItem.full_url).removeClass('d-none')
                        $currentBox.find('.text-pick').addClass('d-none')

                        gallerySelectFiles(files, $currentBoxList, [0])
                    },
                })
            });

            $.each(
                $(document).find('[data-bb-toggle="image-picker-choose"][data-target="popup"]'),
                function (index, item) {
                    const _self = $(item)

                    _self.rvMedia({
                        multiple: false,
                        filter: 'image',
                        view_in: 'all_media',
                        allow_webp: _self.data('allow-webp'),
                        allow_thumb: _self.data('allow-thumb'),
                        module_name: _self.data('module-name'),
                        onSelectFiles: (files, $el) => {
                            let firstImage = _.first(files)
                            const $imageBox = $el.closest('.image-box')
                            const allowThumb = $el.data('allow-thumb')
                            $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                            $imageBox
                                .find('.preview-image')
                                .attr('src', allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url)
                            $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                            $imageBox.find('.preview-image').removeClass('default-image')
                            $imageBox.find('.preview-image-wrapper').show()

                            const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                                detail: {
                                    files: files,
                                    element: $el,
                                },
                            })

                            document.dispatchEvent(coreInsertMediaEvent)
                        },
                    })
                }
            )
            $('body').on('click', '.custom-image [data-bb-toggle="image-picker-choose"][data-target="popup"]', function(e) {
                e.preventDefault()
                let _self = $(this);
                _self.rvMedia({
                    multiple: false,
                    filter: 'image',
                    view_in: 'all_media',
                    allow_webp: _self.data('allow-webp') || 'no',
                    allow_thumb: _self.data('allow-thumb') || 'no',
                    module_name: _self.data('module-name') || '',
                    onSelectFiles: (files, $el) => {
                        let firstImage = _.first(files)
                        const $imageBox = $el.closest('.image-box')
                        const allowThumb = $el.data('allow-thumb')
                        $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                        $imageBox
                            .find('.preview-image')
                            .attr('src', allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url)
                        $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                        $imageBox.find('.preview-image').removeClass('default-image')
                        $imageBox.find('.preview-image-wrapper').show()

                        const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                            detail: {
                                files: files,
                                element: $el,
                            },
                        })

                        document.dispatchEvent(coreInsertMediaEvent)
                    },
                })
            })

            $('body').on('click', '.btn_gallery_js', function(e) {
                e.preventDefault()
                let _self = $(this);
                _self.rvMedia({
                    multiple: false,
                    filter: _self.data('action') === 'select-image' ? 'image' : 'everything',
                    view_in: 'all_media',
                    onSelectFiles: (files, $el) => {
                        const attachment = _.first(files)
                        const wrapper = $el.closest('.attachment-wrapper')
                        wrapper.find('.attachment-url').val(attachment.url)
                        wrapper.find('.attachment-name').val(attachment.name || '')
                        wrapper.find('.attachment-info').html(`
                            <a href="${attachment.full_url}" target="_blank" title="${attachment.name}">${attachment.url}</a>
                            <small class="d-block">${attachment.size}</small>
                        `)

                        wrapper.find('[data-bb-toggle="media-file-remove"]').show()
                    },
                })
            })
        }

        $('body').on('click', '.btn_remove_attachment', event => {
            event.preventDefault();
            $(event.currentTarget).closest('.attachment-wrapper').find('.attachment-info').empty();
            $(event.currentTarget).closest('.attachment-wrapper').find('.attachment-url').val('');
        });

        $(document).on('click', '[data-bb-toggle="image-picker-choose"][data-target="direct"]', (event) => {
            event.preventDefault()
            event.stopPropagation()

            $(event.currentTarget).closest('.image-box').find('.media-image-input').trigger('click')
        })

        $(document).on('show.bs.modal', '#image-picker-add-from-url', (event) => {
            const relatedTarget = $(event.relatedTarget)
            const imageBoxTarget = relatedTarget.data('bb-target')
            const allowWebp = relatedTarget.data('allow-webp')
            const allowThumb = relatedTarget.data('allow-thumb')

            const modal = $(event.currentTarget)
            modal.find('input[name="image-box-target"]').val(imageBoxTarget)
            modal.find('input[name="image-allow-webp"]').val(allowWebp)
            modal.find('input[name="image-allow-thumb"]').val(allowThumb)
        })

        $(document).on('submit', '#image-picker-add-from-url-form', (event) => {
            event.preventDefault()

            const form = $(event.currentTarget)
            const modal = form.closest('.modal')
            const button = modal.find('button[type="submit"]')
            let inputValue = form.find('input[name="url"]').val()
            inputValue = inputValue.split('?').shift()

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), {
                    url: inputValue,
                    folderId: 0,
                    makeRealPath: form.find('input[name="real_path"]').prop('checked') ? 'yes' : 'no',
                    allow_webp: form.find('input[name="image-allow-webp"]').val(),
                    allow_thumb: form.find('input[name="image-allow-thumb"]').val(),
                    module_name: form.find('input[name="image-module-name"]').val() || null
                })
                .then(({ data }) => {
                    form[0].reset()
                    modal.modal('hide')

                    const $imageBox = $(form.find('input[name="image-box-target"]').val())
                    $imageBox.find('.image-data').val(data.data.url).trigger('change')
                    $imageBox.find('.preview-image').prop('src', data.data.src)
                    $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                    $imageBox.find('.preview-image').removeClass('default-image')
                    $imageBox.find('.preview-image-wrapper').show()
                })
        })

        $(document).on('click', '.btn-upload-from-device', function(event) {
            event.preventDefault();
            $(this).siblings('.input-upload-from-device').click();
        });

        $(document).on('change', '.input-upload-from-device', function(event) {
            event.preventDefault()
            const _this = $(this);

            let files = event.target.files;
            if (!files || files.length === 0) {
                console.error("Không có file nào được chọn!");
                return;
            }
            loadingBox('open');
            const isFile = _this.closest('.image-box').hasClass('attachment-wrapper')
            let formData = new FormData();
            formData.append("custom_upload", 1);
            for (let i = 0; i < files.length; i++) {
                formData.append("file[]", files[i]);
            }
            formData.append("allow_webp", null);
            formData.append("allow_thumb", null);
            formData.append("module_name", null);

            $httpClient
                .make()
                .post('/admin/media/files/upload', formData)
                .then(({ data }) => {
                    const records = data.data || [];
                    const addElem = _this.closest('tbody').find('tr.thead');
                    if (isFile) {
                        records.forEach((item, index) => {
                            if (index === 0) {
                                const name = item.url.split('/').pop();
                                const $template = _this.closest('.image-box')

                                $template.find('.attachment-url').val(item.url)
                                $template.find('.attachment-info').html(`
                                    <a href="${ item.src }" target="_blank" title="${ name }">${ name }</a>
                                `)
                                $template.find('.preview-file').attr('href', item.src).removeClass('d-none')
                                $template.find('.text-pick').addClass('d-none')
                            } else {
                                let html = getHtmlRecordVideo(item);
                                addElem.before(html)
                            }
                        });
                        loadingBox('close');
                    } else {
                        records.forEach((item, index) => {
                            if (index === 0) {
                                const $imageBox = _this.closest('.image-box')
                                $imageBox.find('.image-data').val(item.url).trigger('change')
                                $imageBox.find('.preview-image').prop('src', item.src)
                                $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                                $imageBox.find('.preview-image').removeClass('default-image')
                                $imageBox.find('.preview-image-wrapper').show()
                            } else {
                                let html = getHtmlRecordImage(item.id, item);
                                addElem.before(html)
                            }
                        });
                        loadingBox('close');
                    }
                    _this.closest('tbody').sortable();
                })
        })

        $(document).on('click', '[data-bb-toggle="image-picker-remove"]', (event) => {
            event.preventDefault()
            const $this = $(event.currentTarget)
            let $imageBox = $this.closest('.image-box')
            $imageBox
                .find('.preview-image-wrapper img')
                .prop('src', $imageBox.find('.preview-image-wrapper img').data('default'))
            $imageBox.find('.image-data').val('').trigger('change')
            $imageBox.find('.preview-image').addClass('default-image')
            $this.hide()
        })

        $(document).on('click', '[data-bb-toggle="media-file-remove"]', (event) => {
            event.preventDefault()

            const currentTarget = $(event.currentTarget)
            const wrapper = currentTarget.closest('.attachment-wrapper')

            wrapper.find('.attachment-details').addClass('hidden')
            wrapper.find('.attachment-url').val('')

            currentTarget.hide()
        })

        $(document).on('click', '[data-bb-toggle="image-picker-remove"]', (e) => {
            e.preventDefault()
            const $this = $(e.currentTarget)
            $this.tooltip('dispose')
            const $list = $this.closest('.list-gallery-media-images')
            $this.closest('.gallery-image-item-handler').remove()
            if ($list.find('.gallery-image-item-handler').length === 0) {
                const $listImage = $list.closest('.list-images')
                $listImage.find('.default-placeholder-gallery-image').removeClass('hidden')
                $listImage.find('.footer-action').hide()
            }
        })

        const $listImages = $('.list-images')

        if ($listImages.length) {
            $(document).on('click', '[data-bb-toggle="gallery-reset"]', (e) => {
                e.preventDefault()
                const $this = $(e.currentTarget)
                $this.closest('.gallery-images-wrapper').find('.list-gallery-media-images .gallery-image-item-handler').remove()
                $this.closest('.gallery-images-wrapper').find('.default-placeholder-gallery-image').removeClass('hidden')
                $this.closest('.gallery-images-wrapper').find('.footer-action').hide()
            })

            $listImages.find('.list-gallery-media-images').each((index, item) => {
                if (jQuery().sortable) {
                    let $current = $(item)
                    if ($current.data('ui-sortable')) {
                        $current.sortable('destroy')
                    }

                    $current.sortable()
                }
            })
        }
    }
}
$(() => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    })

    new DreamTeamCore()
    window.DreamTeamCore = DreamTeamCore
})