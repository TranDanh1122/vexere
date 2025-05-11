@php
    $storageCheck = RvMedia::alertStorageSize();
@endphp
@if (isset($storageCheck['full_storage']) && $storageCheck['full_storage'])
    <x-Core::alert type="danger">
        <p style="height: 100%; display:flex; align-items: center;">{!! $storageCheck['message'] !!}</p>
    </x-Core::alert>
@elseif(isset($storageCheck['warning_storage']) && $storageCheck['warning_storage'])
    <x-Core::alert type="warning">
        <p style="height: 100%; display:flex; align-items: center;">{!! $storageCheck['warning_message'] !!}</p>
    </x-Core::alert>
@endif
@if (isset($storageCheck['warning_end_addition']) && $storageCheck['warning_end_addition'])
    <x-Core::alert type="danger">
        <p style="height: 100%; display:flex; align-items: center;">{!! $storageCheck['warning_end_addition_message'] !!}</p>
    </x-Core::alert>
@elseif(isset($storageCheck['warning_15_day_addition']) && $storageCheck['warning_15_day_addition'])
    <x-Core::alert type="warning">
        <p style="height: 100%; display:flex; align-items: center;">{!! $storageCheck['warning_15_day_addition_message'] !!}</p>
    </x-Core::alert>
@endif
<div class="rv-media-container">
    <x-Core::card class="rv-media-wrapper">
        <input
            type="checkbox"
            id="media_details_collapse"
            class="d-none fake-click-event"
        >
    @if(checkAgent() == 'web')
        <x-Core::offcanvas
            id="rv-media-aside"
            @class(['d-md-none' => RvMedia::getConfig('sidebar_display') !== 'vertical'])
            style="--bb-offcanvas-width: 85%"
        >
            <x-Core::offcanvas.header>
                <x-Core::offcanvas.title>
                    {{ trans('media::media.menu_name') }}
                </x-Core::offcanvas.title>
                <x-Core::offcanvas.close-button />
            </x-Core::offcanvas.header>

            <x-Core::offcanvas.body class="p-0">
                <x-Core::list-group :flush="true">
                    <x-Core::list-group.header>
                        {{ trans('media::media.filter') }}
                    </x-Core::list-group.header>
                    <x-Core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="filter"
                        data-value="everything"
                    >
                        <x-Core::icon name="ti ti-recycle" />
                        {{ trans('media::media.everything') }}
                    </x-Core::list-group.item>

                    @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                        <x-Core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="filter"
                            data-value="video"
                        >
                            <x-Core::icon name="ti ti-photo" />
                            {{ trans('media::media.image') }}
                        </x-Core::list-group.item>
                    @endif

                    @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                        <x-Core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="filter"
                            data-value="document"
                        >
                            <x-Core::icon name="ti ti-video" />
                            {{ trans('media::media.video') }}
                        </x-Core::list-group.item>
                    @endif

                    <x-Core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="filter"
                        data-value="image"
                    >
                        <x-Core::icon name="ti ti-file" />
                        {{ trans('media::media.document') }}
                    </x-Core::list-group.item>
                </x-Core::list-group>

                <x-Core::list-group :flush="true">
                    <x-Core::list-group.header>
                        {{ trans('media::media.view_in') }}
                    </x-Core::list-group.header>
                    <x-Core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="all_media"
                    >
                        <x-Core::icon name="ti ti-world" />
                        {{ trans('media::media.all_media') }}
                    </x-Core::list-group.item>

                    @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                        <x-Core::list-group.item
                            :action="true"
                            class="js-rv-media-change-filter"
                            data-type="view_in"
                            data-value="trash"
                        >
                            <x-Core::icon name="ti ti-trash" />
                            {{ trans('media::media.trash') }}
                        </x-Core::list-group.item>
                    @endif

                    <x-Core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="recent"
                    >
                        <x-Core::icon name="ti ti-clock" />
                        {{ trans('media::media.recent') }}
                    </x-Core::list-group.item>

                    {{-- <x-Core::list-group.item
                        :action="true"
                        class="js-rv-media-change-filter"
                        data-type="view_in"
                        data-value="favorites"
                    >
                        <x-Core::icon name="ti ti-star" />
                        {{ trans('media::media.favorites') }}
                    </x-Core::list-group.item> --}}
                </x-Core::list-group>
            </x-Core::offcanvas.body>
        </x-Core::offcanvas>
    @endif
        <div class="rv-media-main-wrapper">
            <x-Core::card.header class="flex-column rv-media-header p-0">
                <div class="w-100 p-2 rv-media-top-header flex-wrap gap-3 d-flex justify-content-between align-items-start border-bottom bg-body">
                    <div class="d-flex gap-2 justify-content-between w-100 w-md-auto rv-media-actions">


                        <div class="btn-list">
                            @if (RvMedia::hasPermission('files.create'))
                                <x-Core::dropdown
                                    :label="trans('media::media.upload')"
                                    icon="ti ti-upload"
                                    color="primary"
                                >
                                    <x-Core::dropdown.item
                                        :label="trans('media::media.upload_from_local')"
                                        class="js-dropzone-upload dropdown-item"
                                        icon="ti ti-upload"
                                    />

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.upload_from_url')"
                                        class="js-download-action dropdown-item"
                                        icon="ti ti-link"
                                    />
                                </x-Core::dropdown>
                            @endif

                            @if (RvMedia::hasPermission('folders.create'))
                                <x-Core::button
                                    type="button"
                                    color="primary"
                                    :tooltip="trans('media::media.create_folder')"
                                    class="js-create-folder-action"
                                    icon="ti ti-folder-plus"
                                    :icon-only="true"
                                />
                            @endif

                            <x-Core::button
                                type="button"
                                color="primary"
                                :tooltip="trans('media::media.refresh')"
                                class="js-change-action"
                                icon="ti ti-refresh"
                                :icon-only="true"
                                data-type="refresh"
                            />

                            @if (RvMedia::getConfig('sidebar_display') !== 'vertical')
                                <x-Core::dropdown wrapper-class="d-none d-md-block">
                                    <x-slot:trigger>
                                        <x-Core::button
                                            type="button"
                                            color="primary"
                                            icon="ti ti-filter"
                                            class="dropdown-toggle js-rv-media-change-filter-group js-filter-by-type"
                                            data-bs-toggle="dropdown"
                                            :tooltip="trans('media::media.filter')"
                                        >
                                            <span class="js-rv-media-filter-current"></span>
                                        </x-Core::button>
                                    </x-slot:trigger>

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.everything')"
                                        icon="ti ti-recycle"
                                        class="js-rv-media-change-filter"
                                        data-type="filter"
                                        data-value="everything"
                                    />

                                    @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                                        <x-Core::dropdown.item
                                            :label="trans('media::media.image')"
                                            icon="ti ti-photo"
                                            class="js-rv-media-change-filter"
                                            data-type="filter"
                                            data-value="image"
                                        />
                                    @endif

                                    @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                                        <x-Core::dropdown.item
                                            :label="trans('media::media.video')"
                                            icon="ti ti-video"
                                            class="js-rv-media-change-filter"
                                            data-type="filter"
                                            data-value="video"
                                        />
                                    @endif

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.document')"
                                        icon="ti ti-file"
                                        class="js-rv-media-change-filter"
                                        data-type="filter"
                                        data-value="document"
                                    />
                                </x-Core::dropdown>

                                <x-Core::dropdown wrapper-class="d-none d-md-block">
                                    <x-slot:trigger>
                                        <x-Core::button
                                            type="button"
                                            color="primary"
                                            icon="ti ti-eye"
                                            class="dropdown-toggle js-rv-media-change-filter-group js-filter-by-view-in"
                                            data-bs-toggle="dropdown"
                                            :tooltip="trans('media::media.view_in')"
                                        >
                                            <span class="js-rv-media-filter-current"></span>
                                        </x-Core::button>
                                    </x-slot:trigger>

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.all_media')"
                                        icon="ti ti-world"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="all_media"
                                    />

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.trash')"
                                        icon="ti ti-trash"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="trash"
                                    />

                                    <x-Core::dropdown.item
                                        :label="trans('media::media.recent')"
                                        icon="ti ti-clock"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="recent"
                                    />

                                    {{-- <x-Core::dropdown.item
                                        :label="trans('media::media.favorites')"
                                        icon="ti ti-star"
                                        class="js-rv-media-change-filter"
                                        data-type="view_in"
                                        data-value="favorites"
                                    /> --}}
                                </x-Core::dropdown>
                            @endif

                            @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                                <x-Core::button
                                    type="button"
                                    color="danger"
                                    class="d-none js-files-action"
                                    data-action="empty_trash"
                                    icon="ti ti-trash"
                                >
                                    {{ trans('media::media.empty_trash') }}
                                </x-Core::button>
                            @endif
                        </div>
                    </div>
                    <div class="rv-media-search">
                        <form
                            class="input-search-wrapper"
                            action=""
                            method="GET"
                        >
                            <div class="input-group">
                                <input
                                    type="search"
                                    class="form-control"
                                    name="search"
                                    placeholder="{{ trans('media::media.search_file_and_folder') }}"
                                />
                                <x-Core::button
                                    type="submit"
                                    icon="ti ti-search"
                                    :icon-only="true"
                                />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="w-100 d-flex flex-wrap gap-3 p-2 rv-media-bottom">
                    <div class="d-flex w-100 w-md-auto align-items-center rv-media-breadcrumb">
                        <ul class="breadcrumb"></ul>
                    </div>
                    <div class="d-flex justify-content-between justify-content-md-end align-items-center rv-media-tools w-100 w-md-auto">
                        <div
                            class="btn-list"
                            role="group"
                        >
                            <x-Core::dropdown
                                :label="trans('media::media.sort')"
                                icon="ti ti-sort-a-z"
                            >
                                @foreach ($sorts as $key => $item)
                                    <x-Core::dropdown.item
                                        :label="$item['label']"
                                        :icon="$item['icon']"
                                        class="js-rv-media-change-filter"
                                        data-type="sort_by"
                                        :data-value="$key"
                                    />
                                @endforeach
                            </x-Core::dropdown>

                            <x-Core::dropdown
                                :label="trans('media::media.actions')"
                                icon="ti ti-hand-finger"
                                wrapper-class="rv-dropdown-actions"
                                :disabled="true"
                            />
                        </div>
                        <div
                            class="btn-group js-rv-media-change-view-type ms-2"
                            role="group"
                        >
                            <x-Core::button
                                type="button"
                                data-type="tiles"
                                icon="ti ti-layout-grid"
                                :icon-only="true"
                            />
                            {{-- <x-Core::button
                                type="button"
                                data-type="list"
                                icon="ti ti-layout-list"
                                :icon-only="true"
                            /> --}}
                            <x-Core::button
                                type="button"
                                data-type="details"
                                icon="ti ti-layout-list"
                                :icon-only="true"
                            />
                        </div>
                        <x-Core::button
                            tag="label"
                            for="media_details_collapse"
                            class="collapse-panel ms-2 d-none d-sm-flex"
                            icon="ti ti-arrow-bar-right"
                            :icon-only="true"
                        />
                    </div>
                </div>
            </x-Core::card.header>

            <main class="rv-media-main">
                <div class="rv-media-items"></div>
                <div class="rv-media-details" style="display: none">
                    <div class="rv-media-thumbnail">
                        <x-Core::icon name="ti ti-photo" />
                    </div>
                    <div class="rv-media-description">
                        <div class="rv-media-name">
                            <p>{{ trans('media::media.nothing_is_selected') }}</p>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="d-none rv-media-footer">
                <x-Core::button
                    type="button"
                    color="primary"
                    class="js-insert-to-editor"
                >
                    {{ trans('media::media.insert') }}
                </x-Core::button>
            </footer>
        </div>
        <div class="rv-upload-progress hide-the-pane position-fixed bottom-0 end-0 ">
            <div class="panel panel-default">
                <x-Core::card>
                    <x-Core::card.header class="position-relative">
                        <h3 class="panel-title mb-0">{{ trans('media::media.upload_progress') }}</h3>
                        <x-Core::button
                            class="close-pane position-absolute top-50 bg-primary text-white text-center p-0"
                        >
                            <x-Core::icon
                                class="m-0"
                                name="ti ti-x"
                            />
                        </x-Core::button>
                    </x-Core::card.header>
                    <div
                        class="table-responsive overflow-auto"
                        style="max-height: 180px"
                    >
                        <x-Core::table>
                            <x-Core::table.body class="rv-upload-progress-table">

                            </x-Core::table.body>
                        </x-Core::table>
                    </div>
                </x-Core::card>
            </div>
        </div>
    </x-Core::card>
</div>

<x-Core::modal
    id="modal_add_folder"
    :title="trans('media::media.create_folder')"
    :has-form="true"
    :form-attrs="['class' => 'rv-form form-add-folder']"
>
    <x-Core::form.text-input
        name="name"
        type="text"
        :placeholder="trans('media::media.folder_name')"
    >
        <x-slot:append>
            <x-Core::button
                type="submit"
                color="primary"
            >
                {{ trans('media::media.create') }}
            </x-Core::button>
        </x-slot:append>
    </x-Core::form.text-input>
    <div class="modal-notice"></div>
</x-Core::modal>

<x-Core::modal
    id="modal_rename_items"
    :title="trans('media::media.rename')"
    :has-form="true"
    :form-attrs="['class' => 'form-rename']"
>
    <div class="rename-items"></div>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <x-Core::button data-close-modal>
            {{ trans('media::media.close') }}
        </x-Core::button>
        <x-Core::button type="submit" color="primary">
            {{ trans('media::media.save_changes') }}
        </x-Core::button>
    </x-slot:footer>
</x-Core::modal>

<x-Core::modal
    id="modal_alt_text_items"
    :title="trans('media::media.alt_text')"
    :has-form="true"
    :form-attrs="['class' => 'form-alt-text']"
>
    <div class="alt-text-items"></div>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <x-Core::button data-close-modal>
            {{ trans('media::media.close') }}
        </x-Core::button>
        <x-Core::button type="submit" color="primary">
            {{ trans('media::media.save_changes') }}
        </x-Core::button>
    </x-slot:footer>
</x-Core::modal>

<x-Core::modal
    id="modal_trash_items"
    :title="trans('media::media.move_to_trash')"
    :has-form="true"
    :form-attrs="['class' => 'form-delete-items']"
>
    <p>{{ trans('media::media.confirm_trash') }}</p>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-close-modal
        >{{ trans('media::media.close') }}</button>
    </x-slot:footer>
</x-Core::modal>

<x-Core::modal
    id="modal_delete_items"
    :title="trans('media::media.confirm_delete')"
    :has-form="true"
    :form-attrs="['class' => 'form-delete-items']"
>
    <p>{{ trans('media::media.confirm_delete_description') }}</p>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-close-modal
        >{{ trans('media::media.close') }}</button>
    </x-slot:footer>
</x-Core::modal>

<x-Core::modal
    id="modal_empty_trash"
    :title="trans('media::media.empty_trash_title')"
    :has-form="true"
    :form-attrs="['class' => 'form-empty-trash']"
>
    <p>{{ trans('media::media.empty_trash_description') }}</p>
    <div class="modal-notice"></div>

    <x-slot:footer>
        <button
            type="submit"
            class="btn btn-danger"
        >{{ trans('media::media.confirm') }}</button>
        <button
            type="button"
            class="btn btn-primary"
            data-close-modal
        >{{ trans('media::media.close') }}</button>
    </x-slot:footer>
</x-Core::modal>

<div
    class="modal modal-blur fade"
    tabindex="-1"
    role="dialog"
    id="modal_download_url"
>
    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h4
                    class="modal-title"
                    data-downloading="{{ trans('media::media.downloading') }}"
                    data-text="{{ trans('media::media.download_link') }}"
                >
                    <x-Core::icon name="ti ti-download" />
                    {{ trans('media::media.download_link') }}
                </h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ trans('media::media.close') }}"
                >
                </button>
            </div>
            <div class="modal-body">
                <form class="rv-form form-download-url">
                    <div id="download-form-wrapper">
                        <div class="mb-3">
                        <textarea
                            rows="4"
                            name="urls"
                            class="form-control"
                            placeholder="http://example.com/image1.jpg&#10;http://example.com/image2.jpg&#10;http://example.com/image3.jpg&#10;..."
                        ></textarea>

                            <x-Core::form.helper-text>
                                {{ trans('media::media.download_explain') }}
                            </x-Core::form.helper-text>
                        </div>
                    </div>

                    <x-Core::button type="submit" color="primary" class="w-100">
                        {{ trans('media::media.download_link') }}
                    </x-Core::button>
                </form>
                <div
                    class="mt-2 modal-notice"
                    id="modal-notice"
                    style="max-height: 350px;overflow: auto"
                ></div>
            </div>
        </div>
    </div>
</div>
<x-Core::modal
    title="{{ trans('media::media.crop') }}"
    id="modal_crop_image"
    size="lg"
    :form-attrs="['class' => 'rv-form form-crop']"
    :has-form="true"
>
    <div>
        <input
            type="hidden"
            name="image_id"
        >
        <input
            type="hidden"
            name="crop_data"
        >
        <div class="row">
            <div class="col-lg-9">
                <div id="rv_crop_media_loading"></div>
                <div class="crop-image"></div>
            </div>
            <div class="col-lg-3">
                <div class="mt-3">
                    <x-Core::form.text-input
                        label="{{ trans('media::media.cropper.height') }}"
                        name="dataHeight"
                        id="dataHeight"
                    />

                    <x-Core::form.text-input
                        label="{{ trans('media::media.cropper.width') }}"
                        name="dataWidth"
                        id="dataWidth"
                    />

                    <x-Core::form.checkbox
                        :label="trans('media::media.cropper.aspect_ratio')"
                        name="aspectRatio"
                        :checked="false"
                        id="aspectRatio"
                    />
                </div>
            </div>
        </div>
    </div>
    <x-slot:footer>
        <x-Core::button data-close-modal>
            {{ trans('media::media.close') }}
        </x-Core::button>

        <x-Core::button
            type="submit"
            color="primary"
        >
            {{ trans('media::media.crop') }}
        </x-Core::button>
    </x-slot:footer>
</x-Core::modal>

<x-Core::modal
    id="modal-properties"
    :title="trans('media::media.properties.name')"
>
    <input type="hidden" name="selected">

    <x-Core::form.color-selector
        :label="trans('media::media.properties.color_label')"
        name="color"
        :choices="RvMedia::getFolderColors()"
    />

    <x-slot:footer>
        <x-Core::button data-close-modal>
            {{ trans('media::media.close') }}
        </x-Core::button>

        <x-Core::button
            type="submit"
            color="primary"
        >
            {{ trans('media::media.save_changes') }}
        </x-Core::button>
    </x-slot:footer>
</x-Core::modal>

<button class="d-none js-rv-clipboard-temp"></button>

<x-Core::custom-template id="rv_media_loading">
    <x-Core::loading />
</x-Core::custom-template>

<x-Core::custom-template id="rv_action_item">
    <x-Core::dropdown.item
        class="js-files-action"
        data-action="__action__"
        icon="__icon__"
        label="__name__"
    />
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_items_list">
    <div class="rv-media-list">
        <ul>
            <li class="no-items">
                <x-Core::icon name="ti ti-upload" />
                <h3>Drop files and folders here</h3>
                <p>Or use the upload button above.</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level" title="{{ trans('media::media.up_level') }}">
                <div class="custom-checkbox"></div>
                <div class="rv-media-file-name">
                    <x-Core::icon name="ti ti-corner-up-left" />
                    <span>...</span>
                </div>
                <div class="rv-media-file-size"></div>
                <div class="rv-media-created-at"></div>
            </li>
        </ul>
    </div>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_items_tiles" class="hidden">
    <div class="rv-media-grid">
        <ul>
            <li class="no-items">
                __noItemIcon__
                <h3>__noItemTitle__</h3>
                <p>__noItemMessage__</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level">
                <div class="rv-media-item" data-context="__type__" title="{{ trans('media::media.up_level') }}">
                    <div class="rv-media-thumbnail">
                        <x-Core::icon name="ti ti-corner-up-left" size="lg" />
                    </div>
                    <div class="rv-media-description">
                        <div class="title">...</div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_items_list_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" title="__name__" data-id="__id__">
        <div class="custom-checkbox">
            <label>
                <input type="checkbox">
                <span></span>
            </label>
        </div>
        <div class="rv-media-file-name">
            __thumb__
            <span>__name__</span>
        </div>
        <div class="rv-media-file-size">__size__</div>
        <div class="rv-media-created-at">__date__</div>
    </li>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_items_tiles_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" data-id="__id__">
        <input type="checkbox" class="hidden">
        <div class="rv-media-item" title="__name__">
            <span class="media-item-selected">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M186.301 339.893L96 249.461l-32 30.507L186.301 402 448 140.506 416 110z"></path>
                </svg>
            </span>
            <div class="rv-media-thumbnail">
                __thumb__
            </div>
            <div class="rv-media-description">
                <div class="title title{{ BaseHelper::stringify(request()->input('file_id')) }}">__name__</div>
            </div>
        </div>
    </li>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_upload_progress_item">
    <x-Core::table.body.row>
        <x-Core::table.body.cell>
            <span class="file-name">__fileName__</span>
            <div class="file-error"></div>
        </x-Core::table.body.cell>
        <x-Core::table.body.cell>
            <span class="file-size">__fileSize__</span>
        </x-Core::table.body.cell>
        <x-Core::table.body.cell>
            <span class="label label-__status__">__message__</span>
        </x-Core::table.body.cell>
    </x-Core::table.body.row>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_breadcrumb_item">
    <li>
        <a href="#" data-folder="__folderId__" class="text-decoration-none js-change-folder">
            __icon__
            __name__
        </a>
    </li>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_rename_item">
    <div class="mb-3">
        <div class="input-group">
            <div class="input-group-text">__icon__</div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>

    <x-Core::form.checkbox
        data-folder-label="{{ trans('media::media.rename_physical_folder') }}"
        data-file-label="{{ trans('media::media.rename_physical_file') }}"
        label="__label__"
        name="rename_physical_file"
        data-bb-toggle="collapse"
        data-bb-target=".rename-physical-file-warning"
    />

    <x-Core::alert type="warning" class="rename-physical-file-warning" style="display: none">
        {{ trans('media::media.rename_physical_file_warning') }}
    </x-Core::alert>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_alt_text_item">
    <div class="mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <x-Core::icon name="__icon__" />
            </div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>
</x-Core::custom-template>

<x-Core::custom-template id="rv_media_crop_image">
    <img src="__src__" style="display: block;max-width: 100%">
</x-Core::custom-template>

<div class="media-download-popup" style="display: none">
    <x-Core::alert type="success">{{ trans('media::media.prepare_file_to_download') }}</x-Core::alert>
</div>
