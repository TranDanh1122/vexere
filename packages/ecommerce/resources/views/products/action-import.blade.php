<a href="#show_import_data" style="height: 30px; margin-left: .3rem;" data-order_delivery data-bs-toggle="modal" class="btn-sm btn btn-info">
<i class="fas fa-upload"></i>&nbsp{{ __('Ecommerce::admin.import.name') }}</a>
<div class="modal fade" id="show_import_data">
    <div class="modal-dialog" style="max-width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Ecommerce::admin.import.title') }}</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;border: 0;width: 30px;height: 30px;border-radius: 50%;padding: 0;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.products.import') }}" class="form-inline form-group" method="POST" accept-charset="utf-8" enctype="multipart/form-data" style="width: 100%;">
                    @csrf
                    <div class="form-group form-import" style="min-width: 300px;">
                        <input accept=".xls,.xlsx" type="file" class="form-control input-sm mb-3" name="file" placeholder="Chọn file" required style="width: 100%;">
                        {{-- <p style="clear: both"><x-Core::form.checkbox
                            :label="trans('Ecommerce::admin.import.save_image')"
                            name="save_image"
                            value="yes"
                            :checked="true"
                        /> --}}
                        <style>.form-import .form-check-input{width: 14px !important;}</style></p>
                        <a href="/vendor/core/Ecommerce/sample_import.xlsx" target="_blank">{{ __('Ecommerce::admin.import.download') }}</a>
                    </div>
                    <button style="float: left;padding: 5px 10px;margin-top: 1px;margin-right: 5px;"
                        type="submit"
                        class="btn btn-sm btn-success"
                    > {{ __('Ecommerce::admin.import.import') }}</button>
                </form>
                <p>{{ __('Ecommerce::admin.import.note') }}</p>
                <ul>
                    <li>{{ __('Ecommerce::admin.import.require_title') }}</li>
                    <li>{{ __('Ecommerce::admin.import.require_slug') }}</li>
                    <li>
                        {{ __('Ecommerce::admin.import.detail') }}<br/>
                        <span>Slug: <b>{{ __('Ecommerce::admin.import.slug') }}</b></span><br/>
                        <span>Name: <b>{{ __('Ecommerce::admin.import.pd_name') }}</b></span><br/>
                        <span>Attribute: <b>{{ __('Ecommerce::admin.import.pd_attribute') }}</b></span><br/>
                        <span>Id: <b>{{ __('Ecommerce::admin.import.pd_variant_id') }}</b></span><br/>
                        <span>Variant name: <b>{{ __('Ecommerce::admin.import.pd_variant_name') }}</b></span><br/>
                        <span>Variant attribute: <b>{{ __('Ecommerce::admin.import.pd_variant_attribute') }}</b></span><br/>
                        <span>Content: <b>{{ __('Ecommerce::admin.content') }}</b></span><br/>
                        <span>Branh: <b>{{ __('Ecommerce::admin.brand') }}</b></span><br/>
                        <span>Category: <b>{{ __('Ecommerce::admin.category') }}</b></span><br/>
                        <span>Sku: <b>{{ __('Ecommerce::admin.sku') }}</b></span><br/>
                        <span>Price: <b>{{ __('Ecommerce::admin.price_retail') }}</b></span><br/>
                        <span>Price origin: <b>{{ __('Ecommerce::admin.price_old') }}</b></span><br/>
                        <span>Quantity: <b>{{ __('Ecommerce::admin.quantity') }}</b></span><br/>
                        <span>Weight: <b>{{ __('Ecommerce::admin.weight') }}</b></span><br/>
                        <span>Avatar: <b>{{ __('Ecommerce::admin.import.avatar') }}</b></span><br/>
                        <span>Slide images: <b>{{ __('Ecommerce::admin.image_slide') }}</b></span><br/>
                        <span>Seo title: <b>{{ __('Ecommerce::admin.import.seo_title') }}</b></span><br/>
                        <span>Seo description: <b>{{ __('Ecommerce::admin.import.seo_description') }}</b></span><br/>
                        <span>Description: <b>{{ __('Ecommerce::admin.description') }}</b></span><br/>
                        <span>Product type: <b>{{ __('Ecommerce::product.product_type') }}</b></span><br/>
                        <span>Digital attachments: <b>{{ __('Ecommerce::product.digital_attachment') }}</b></span><br/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>