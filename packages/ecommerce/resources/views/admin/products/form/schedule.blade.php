<div class="form-group schedule-group" data-card="{{ $card }}">
    <label class="">@lang('Ngày chạy')</label>
    <div class="flex" style="display: flex; flex-wrap: wrap;gap: 15px;margin-bottom: 10px;">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check-all-days-{{$card}}" checked>
            <label class="form-check-label" for="check-all-days-{{$card}}">@lang('Tất cả')</label>
        </div>
        @foreach (['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'] as $day)
            @php
                $column = match($day) {
                    'CN' => 'sunday',
                    'T2' => 'monday',
                    'T3' => 'tuesday',
                    'T4' => 'wednesday',
                    'T5' => 'thursday',
                    'T6' => 'friday',
                    'T7' => 'saturday',
                };
            @endphp
            <div class="form-check">
                <input type="checkbox" class="form-check-input day-checkbox" name="{{$card}}[]" id="day-{{ $card }}-{{ $day }}" value="{{ $day }}" {{ (($productSchedules->{$column} ?? 0) == 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="day-{{ $card }}-{{ $day }}">@lang($day)</label>
            </div>
        @endforeach
    </div>
    <div class="{{ $card }}">
        <div class="form-group mb-1">
            <label class="small">Giờ khởi hành</label>
            <input type="text" class="form-control form-control-sm timepicker time-input-start" style="width: 120px" name="{{ $card }}_time" value="{{ !empty($time) ? date('H:i', strtotime($time)) : '' }}" placeholder="08:00">
        </div>
    </div>
</div>
<script>
    $(function() {
        $('.schedule-group').each(function() {
            const $formGroup = $(this);
            const $card = $(`.${$formGroup.data('card')}`);
            const $checkAll = $formGroup.find('#check-all-days-' + $formGroup.data('card'));
            const $dayCheckboxes = $formGroup.find('.day-checkbox');

            // Handle "Check All" functionality
            $checkAll.on('change', function() {
                const isChecked = $(this).is(':checked');
                $dayCheckboxes.prop('checked', isChecked);
                $card.toggle(isChecked);
            });

            // Handle individual day checkboxes
            $dayCheckboxes.on('change', function() {
                const allChecked = $dayCheckboxes.length === $dayCheckboxes.filter(':checked').length;
                $checkAll.prop('checked', allChecked);
                $card.toggle($dayCheckboxes.filter(':checked').length > 0);
            });
        });
        $('.time-input-start:not(.initialized)').each(function() {
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
    });
</script>