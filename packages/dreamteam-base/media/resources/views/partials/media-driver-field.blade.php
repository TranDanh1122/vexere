<x-Core::form-group>
    <x-Core::form.select
        name="media_driver"
        :label="trans('media::media.setting.select_driver')"
        :options="[
            'local' => 'Local disk',
            's3' => 'Amazon S3',
            'r2' => 'Cloudflare R2',
            'do_spaces' => 'DigitalOcean Spaces',
            'wasabi' => 'Wasabi',
            'bunnycdn' => 'BunnyCDN',
        ]"
        :value="RvMedia::getMediaDriver()"
        data-bb-toggle="collapse"
        data-bb-target=".media-driver"
    />

    <x-Core::form.fieldset
        data-bb-value="s3"
        class="media-driver"
        @style(['display: none;' => old('media_driver', RvMedia::getMediaDriver()) !== 's3'])
    >
        <x-Core::form.text-input
            name="media_aws_access_key_id"
            :label="trans('media::media.setting.aws_access_key_id')"
            :value="config('filesystems.disks.s3.key')"
            placeholder="Ex: ITHWBTGTSEKLXXXXXX"
        />

        <x-Core::form.text-input
            name="media_aws_secret_key"
            :label="trans('media::media.setting.aws_secret_key')"
            :value="config('filesystems.disks.s3.secret')"
            placeholder="Ex: +fivlGCeTJCEWRREGFBGFDXXXXX"
        />

        <x-Core::form.text-input
            name="media_aws_default_region"
            :label="trans('media::media.setting.aws_default_region')"
            :value="config('filesystems.disks.s3.region')"
            placeholder="Ex: ap-southeast-1"
        />

        <x-Core::form.text-input
            name="media_aws_bucket"
            :label="trans('media::media.setting.aws_bucket')"
            :value="config('filesystems.disks.s3.bucket')"
            placeholder="Ex: website-ai"
        />

        <x-Core::form.text-input
            name="media_aws_url"
            :label="trans('media::media.setting.aws_url')"
            :value="config('filesystems.disks.s3.url')"
            placeholder="Ex: https://s3-ap-southeast-1.amazonaws.com/website-ai"
        />

        <x-Core::form.text-input
            name="media_aws_endpoint"
            :label="trans('media::media.setting.aws_endpoint')"
            :value="config('filesystems.disks.s3.endpoint')"
            :placeholder="trans('media::media.setting.optional')"
        />
    </x-Core::form.fieldset>

    <x-Core::form.fieldset
        data-bb-value="r2"
        class="media-driver"
        @style(['display: none;' => old('media_driver', RvMedia::getMediaDriver()) !== 'r2'])
    >
        <x-Core::form.text-input
            name="media_r2_access_key_id"
            :label="trans('media::media.setting.r2_access_key_id')"
            :value="config('filesystems.disks.r2.key')"
            placeholder="Ex: ITHWBTGTSEKLXXXXXX"
        />

        <x-Core::form.text-input
            name="media_r2_secret_key"
            :label="trans('media::media.setting.r2_secret_key')"
            :value="config('filesystems.disks.r2.secret')"
            placeholder="Ex: +fivlGCeTJCEWRREGFBGFDXXXXX"
        />

        <x-Core::form.text-input
            name="media_r2_bucket"
            :label="trans('media::media.setting.r2_bucket')"
            :value="config('filesystems.disks.r2.bucket')"
            placeholder="Ex: website-ai"
        />

        <x-Core::form.text-input
            name="media_r2_endpoint"
            :label="trans('media::media.setting.r2_endpoint')"
            :value="config('filesystems.disks.r2.endpoint')"
            placeholder="Ex: https://xxx.r2.cloudflarestorage.com"
        />

        <x-Core::form.text-input
            name="media_r2_url"
            :label="trans('media::media.setting.r2_url')"
            :value="config('filesystems.disks.r2.url')"
            placeholder="Ex: https://pub-f70218cc331a40689xxx.r2.dev"
        />
    </x-Core::form.fieldset>

    <x-Core::form.fieldset
        data-bb-value="do_spaces"
        class="media-driver"
        @style(['display: none;' => old('media_driver', RvMedia::getMediaDriver()) !== 'do_spaces'])
    >
        <x-Core::form.text-input
            name="media_do_spaces_access_key_id"
            :label="trans('media::media.setting.do_spaces_access_key_id')"
            :value="config('filesystems.disks.do_spaces.key')"
            placeholder="Ex: ITHWBTGTSEKLXXXXXX"
        />

        <x-Core::form.text-input
            name="media_do_spaces_secret_key"
            :label="trans('media::media.setting.do_spaces_secret_key')"
            :value="config('filesystems.disks.do_spaces.secret')"
            placeholder="Ex: +fivlGCeTJCEWRREGFBGFDXXXXX"
        />

        <x-Core::form.text-input
            name="media_do_spaces_default_region"
            :label="trans('media::media.setting.do_spaces_default_region')"
            :value="config('filesystems.disks.do_spaces.region')"
            placeholder="Ex: SGP1"
        />

        <x-Core::form.text-input
            name="media_do_spaces_bucket"
            :label="trans('media::media.setting.do_spaces_bucket')"
            :value="config('filesystems.disks.do_spaces.bucket')"
            placeholder="Ex: website-ai"
        />

        <x-Core::form.text-input
            name="media_do_spaces_endpoint"
            :label="trans('media::media.setting.do_spaces_endpoint')"
            :value="config('filesystems.disks.do_spaces.endpoint')"
            placeholder="Ex: https://sfo2.digitaloceanspaces.com"
        />

        <x-Core::form.on-off.checkbox
            :label="trans('media::media.setting.do_spaces_cdn_enabled')"
            name="media_do_spaces_cdn_enabled"
            :checked="setting('media_do_spaces_cdn_enabled')"
        />

        <x-Core::form.text-input
            name="media_do_spaces_cdn_custom_domain"
            :label="trans('media::media.setting.media_do_spaces_cdn_custom_domain')"
            :value="setting('media_do_spaces_cdn_custom_domain')"
            :placeholder="trans('media::media.setting.media_do_spaces_cdn_custom_domain_placeholder')"
        />
    </x-Core::form.fieldset>

    <x-Core::form.fieldset
        data-bb-value="wasabi"
        class="media-driver"
        @style(['display: none;' => old('media_driver', RvMedia::getMediaDriver()) !== 'wasabi'])
    >
        <x-Core::form.text-input
            name="media_wasabi_access_key_id"
            :label="trans('media::media.setting.wasabi_access_key_id')"
            :value="config('filesystems.disks.wasabi.key')"
            placeholder="Ex: ITHWBTGTSEKLXXXXXX"
        />

        <x-Core::form.text-input
            name="media_wasabi_secret_key"
            :label="trans('media::media.setting.wasabi_secret_key')"
            :value="config('filesystems.disks.wasabi.secret')"
            placeholder="Ex: +fivlGCeTJCEWRREGFBGFDXXXXX"
        />

        <x-Core::form.text-input
            name="media_wasabi_default_region"
            :label="trans('media::media.setting.wasabi_default_region')"
            :value="config('filesystems.disks.wasabi.region')"
            placeholder="Ex: us-east-1"
        />

        <x-Core::form.text-input
            name="media_wasabi_bucket"
            :label="trans('media::media.setting.wasabi_bucket')"
            :value="config('filesystems.disks.wasabi.bucket')"
            placeholder="Ex: website-ai"
        />

        <x-Core::form.text-input
            name="media_wasabi_root"
            :label="trans('media::media.setting.wasabi_root')"
            :value="config('filesystems.disks.wasabi.root')"
            placeholder="Default: /"
        />
    </x-Core::form.fieldset>

    <x-Core::form.fieldset
        data-bb-value="bunnycdn"
        class="media-driver"
        @style(['display: none;' => old('media_driver', RvMedia::getMediaDriver()) !== 'bunnycdn'])
    >
        <x-Core::form.text-input
            name="media_bunnycdn_hostname"
            :label="trans('media::media.setting.bunnycdn_hostname')"
            :value="setting('media_bunnycdn_hostname')"
            placeholder="Ex: website-ai.b-cdn.net"
        />

        <x-Core::form.text-input
            name="media_bunnycdn_zone"
            :label="trans('media::media.setting.bunnycdn_zone')"
            :value="setting('media_bunnycdn_zone')"
            placeholder="Ex: website-ai"
        />

        <x-Core::form.text-input
            name="media_bunnycdn_key"
            :label="trans('media::media.setting.bunnycdn_key')"
            :value="setting('media_bunnycdn_key')"
            placeholder="Ex: 9a734df7-844b-..."
        />

        <x-Core::form.select
            name="media_bunnycdn_region"
            :label="trans('media::media.setting.bunnycdn_region')"
            :options="[
                '' => 'Falkenstein',
                'ny' => 'New York',
                'la' => 'Los Angeles',
                'sg' => 'Singapore',
                'syd' => 'Sydney',
            ]"
            :value="setting('media_bunnycdn_region')"
        />
    </x-Core::form.fieldset>
</x-Core::form-group>