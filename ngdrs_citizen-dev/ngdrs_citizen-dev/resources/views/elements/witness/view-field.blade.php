@php

$field = $field ?? null;
$value = $value ?? '';
$masters = $masters ?? [];
$readonly = $readonly ?? true;

if (!$field) return;

/*
|--------------------------------------------------------------------------
| GET MASTER OPTIONS
|--------------------------------------------------------------------------
*/
$options =
    $masters[$field->field_id_name_en] ?? [];

/*
|--------------------------------------------------------------------------
| IF VALUE IS ID → CONVERT TO LABEL
|--------------------------------------------------------------------------
*/
$displayValue = $value;

if (!empty($options)) {

    $displayValue =
        $options[$value]
        ?? $value;
}

$selectFields = [

    'salutation',
    'maincast_id',
    'relationship_status',
    'identificationtype_id',
    'pan_form_list',
    'gender_id',
    'occupation_id',
    'exemption_id',
    'homevisit_flag',
    'is_govt_org',
    'faith_id'

];

@endphp

@if(in_array($field->field_id_name_en, $selectFields))

    <div>

        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $field->field_name_en }}
        </label>

        <input
            type="text"
            value="{{ $displayValue }}"
            readonly
            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 bg-gray-100"
        />

    </div>

@else

    <div>

        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $field->field_name_en }}
        </label>

        <input
            type="text"
            value="{{ $displayValue }}"
            readonly
            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 bg-gray-100"
        />

    </div>

@endif