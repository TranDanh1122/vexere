import { MediaService } from './MediaService'
import { MessageService } from './MessageService'
import { Helpers } from '../Helpers/Helpers'
import { FolderService } from "./FolderService";

export class DownloadService {
    constructor() {
        this.MediaService = new MediaService()

        this.FolderService = new FolderService();

        $(document).on('shown.bs.modal', '#modal_download_url', (event) => {
            $(event.currentTarget).find('.form-download-url input[type=text]').focus()
        })
    }

    async download(urls, onProgress, onCompleted) {
        let _self = this

        urls = $.trim(urls).split(/\r?\n/)

        let index = 0
        let hasError = false
        for (let url of urls) {
            let filename = ''
            url = url.split('?').shift()
            try {
                filename = new URL(url).pathname.split('/').pop()
            } catch (e) {
                filename = url
            }
            let ok = onProgress(`${index} / ${urls.length}`, filename, url)
            await new Promise((resolve, reject) => {
                $httpClient
                    .make()
                    .post(RV_MEDIA_URL.download_url, {
                        folderId: Helpers.getRequestParams().folder_id,
                        allow_webp: Helpers.getRequestParams().allow_webp,
                        allow_thumb: Helpers.getRequestParams().allow_thumb,
                        module_name: Helpers.getRequestParams().module_name,
                        url,
                    })
                    .then(({ data }) => {
                        ok(true, data.message || data.data?.message)
                        if (data.data.refresh_folder && data.data.refresh_folder == true && data.data.folder_id && Helpers.getRequestParams().view_type != 'details') {
                            _self.FolderService.changeFolder(data.data.folder_id);
                        }
                        resolve()
                    })
                    .finally(() => onCompleted())
                    .catch((error) => reject(error))
            })

            index += 1
        }

        Helpers.resetPagination()
        _self.MediaService.getMedia(true)
        if (!hasError) {
            DownloadService.closeModal()
            MessageService.showMessage('success', Helpers.trans('message.success_header'))
        }
    }

    static closeModal() {
        $(document).find('#modal_download_url').modal('hide')
    }
}
