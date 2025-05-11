<style>.handle {margin-top: 25px;
  margin-right: 5px;} .handle i {font-size: 16px;}</style>
<div class="card card-pink mb-2 {{ $cardClass ?? ''}}">
    <div class="card-header text-sm" data-card-widget="collapse" style="padding: 5px 13px;">
        <div class="card-title" style="font-size: 14px; margin-bottom: 0;">{{ $sectionTitle ?? __('Pickup/Dropoff Points') }}</div>
    </div>
    <div class="card-body" style="padding: 10px 13px;">
        <div id="{{ $containerId ?? 'pickup-points-container' }}" class="sortable-container" data-prefix="{{ $pointsPrefix ?? 'pickup_points' }}">
            @if (isset($points) && count($points) > 0)
                @foreach ($points as $index => $point)
                    <div class="pickup-point-item sortable-item mb-2 p-2 border rounded bg-light">
                        <div class="d-flex align-items-center">
                            <div class="handle mr-2 text-muted"><i class="fas fa-grip-vertical"></i></div>
                            <div class="row w-100" style="position: relative;">
                                <div class="col-sm-3">
                                    <div class="form-group mb-1">
                                        <label class="small">@lang('Time')</label>
                                        <input type="text" class="form-control form-control-sm timepicker"
                                            name="{{ $pointsPrefix ?? 'pickup_points' }}[{{ $index }}][time]"
                                            value="{{ date('H:i', strtotime($point->time)) }}">
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="form-group mb-1">
                                        <label class="small">@lang('Location')</label>
                                        <select class="form-control form-control-sm"
                                            name="{{ $pointsPrefix ?? 'pickup_points' }}[{{ $index }}][location_id]">
                                            <option value="">@lang('Select Location')</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location['id'] }}"
                                                    {{ isset($point->location_id) && $point->location_id == $location['id'] ? 'selected' : '' }}>
                                                    {{ $location['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group mb-1">
                                        <label class="small" for="{{$pointsPrefix}}_transit_{{$index}}_{{str_random()}}">@lang('Transit')</label>
                                        <input type="checkbox" {{ $point->transit == 1 ? 'checked': '' }} class="form-check-input" id="{{$pointsPrefix}}_transit_{{$index}}_{{str_random()}}" name="{{ $pointsPrefix ?? 'pickup_points' }}[{{ $index }}][transit]">
                                    </div>
                                </div>
                                <div style="position: absolute; right: -10px; bottom: -12px; width: 30px; height: 30px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-point">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="pickup-point-item sortable-item mb-2 p-2 border rounded bg-light">
                    <div class="d-flex align-items-center">
                        <div class="handle mr-2 text-muted"><i class="fas fa-grip-vertical"></i></div>
                        <div class="row w-100 relative" style="position: relative">
                            <div class="col-sm-3">
                                <div class="form-group mb-1">
                                    <label class="small">@lang('Time')</label>
                                    <input type="text" class="form-control form-control-sm timepicker"
                                        name="{{ $pointsPrefix ?? 'pickup_points' }}[0][time]">
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group mb-1">
                                    <label class="small">@lang('Location')</label>
                                    <select class="form-control form-control-sm" name="{{ $pointsPrefix ?? 'pickup_points' }}[0][location_id]">
                                        <option value="">@lang('Select Location')</option>
                                        @foreach ($locations ?? [] as $location)
                                            <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group mb-1">
                                    <label class="small" for="{{$pointsPrefix}}_transit_0_{{str_random()}}">@lang('Transit')</label>
                                    <input type="checkbox" class="form-check-input" id="{{$pointsPrefix}}_transit_0_{{str_random()}}" name="{{ $pointsPrefix ?? 'pickup_points' }}[0][transit]">
                                </div>
                            </div>
                            <div style="position: absolute; right: -10px; bottom: -12px; width: 30px; height: 30px;">
                                <button type="button" class="btn btn-sm btn-danger remove-point">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="text-right mt-2">
            <button type="button" class="btn btn-sm btn-primary add-point" data-container="{{ $containerId ?? 'pickup-points-container' }}">
                <i class="fas fa-plus"></i> @lang('Thêm hành trình')
            </button>
        </div>
    </div>
</div>
<script>
    $(function() {
        let locationAll = @json($locationAll ?? []);
        
        // Initialize each container
        initializePointsContainer('{{ $containerId ?? 'pickup-points-container' }}');
        
        // Function to initialize container
        function initializePointsContainer(containerId) {
            const $container = $('#' + containerId);
            const prefix = $container.data('prefix');
            
            // Initialize timepicker
            initTimepicker($container);

            // Initialize sortable
            $container.sortable({
                forcePlaceholderSize: true,
                update: function() {
                    reindexItems($container, prefix);
                }
            });
        }

        // Add new item
        $(document).off().on('click', '.add-point', function() {
            const containerId = $(this).data('container');
            const $container = $('#' + containerId);
            const prefix = $container.data('prefix');
            const index = $container.find(".pickup-point-item").length;
            const options = locationAll[prefix] || [];
            const LocationOptions = options.map(location => `<option value="${location.id}">${location.name}</option>`).join('');
            const locationSelect = `
                <select class="form-control form-control-sm" name="${prefix}[${index}][location_id]">
                    <option value="">@lang('Select Location')</option>
                    ${LocationOptions}
                </select>
            `;
            const newItem = `
                <div class="pickup-point-item sortable-item mb-2 p-2 border rounded bg-light">
                    <div class="d-flex align-items-center">
                        <div class="handle mr-2 text-muted"><i class="fas fa-grip-vertical"></i></div>
                        <div class="row w-100">
                            <div class="col-sm-3">
                                <div class="form-group mb-1">
                                    <label class="small">@lang('Time')</label>
                                    <input type="text" class="form-control form-control-sm timepicker" 
                                        name="${prefix}[${index}][time]">
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group mb-1">
                                    <label class="small">@lang('Location')</label>
                                    ${locationSelect}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group mb-1">
                                    <label class="small" for="${prefix}_transit_${index + Math.random()}}}">@lang('Transit')</label>
                                    <input type="checkbox" class="form-check-input" id="${prefix}_transit_${index + Math.random()}}}" name="${prefix}[${index}][transit]">
                                </div>
                            </div>
                            <div style="position: absolute; right: -10px; bottom: -12px; width: 30px; height: 30px;">
                                <button type="button" class="btn btn-sm btn-danger remove-point">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $container.append(newItem);
            initTimepicker($container.find('.pickup-point-item:last'));
            $container.find('select').select2();
        });

        $('select').select2();

        // Remove item
        $(document).on('click', '.remove-point', function() {
            const $item = $(this).closest('.pickup-point-item');
            const $container = $item.closest('.sortable-container');
            const prefix = $container.data('prefix');
            
            if ($container.find(".pickup-point-item").length > 0) {
                $item.remove();
                reindexItems($container, prefix);
            } else {
                alert("@lang('At least one point is required')");
            }
        });

        // Initialize timepicker for elements
        function initTimepicker($container) {
            $container.find('.timepicker:not(.initialized)').each(function() {
                $(this).addClass('initialized').datetimepicker({
                    format: 'H:i',
                    defaultTime: '00:00',
                    formatTime: 'H:i',
                    scrollMonth: false,
                    scrollInput: false,
                    datepicker: false,
                    timepicker: true,
                    step: 15
                });
            });
        }

        // Reindex form inputs after sorting
        function reindexItems($container, prefix) {
            $container.find(".pickup-point-item").each(function(index) {
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\w+\[\d+\]/, `${prefix}[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        }
    });
</script>