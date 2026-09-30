@php

$field = $field ?? null;
$masters = $masters ?? [];
$value = $value ?? '';
$readonly = $readonly ?? false;

@endphp

@if(!$field)
    @php return; @endphp
@endif

@php

$options =
    $masters[$field->field_id_name_en] ?? [];

@endphp

@if($field->is_list == 'Y')

    <x-select-input
        :id="$field->field_id_name_en"
        :label="$field->field_name_en"
        :name="$field->field_id_name_en"
        :options="$options"
        :selected="$value"
        :disabled="$readonly"
    />

@else

    <x-text-input
        :id="$field->field_id_name_en"
        :label="$field->field_name_en"
        :name="$field->field_id_name_en"
        :placeholder="'Enter '.$field->field_name_en"
        :value="$value"
        :readonly="$readonly"
    />

@endif