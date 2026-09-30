@php

$field = $field ?? null;
$value = $value ?? '';
$readonly = $readonly ?? false;
$masters = $masters ?? [];



@endphp

@if(!$field)
    @php return; @endphp
@endif

@php

$options =
    $masters[
        $field->field_id_name_en
    ] ?? [];

@endphp

@if($field)

    @php

        $selectFields = [

            'is_executer',
            'salutation_id',
            'maincast_id',
            'relationship_status',
            'identificationtype_id',
            'pan_form_list',
            'gender_id',
            'occupation_id',
            'exemption_id',
            'home_visit_flag',
            'is_govt_org',
            'faith_id',
            'bank_id'

        ];

    @endphp
    

    @if(
        in_array(
            $field->field_id_name_en,
            $selectFields
        )
    )

        <x-select-input
            :id="$field->field_id_name_en"
            :label="$field->field_name_en"
            :name="$field->field_id_name_en"
            :options="$options"
            :value="$value"
        />

    @else

        <div>

            <label class="block text-sm font-medium text-gray-700 mb-1.5">

                {{ $field->field_name_en }}

            </label>

            <input
                id="{{ $field->field_id_name_en }}"
                type="text"
                name="{{ $field->field_id_name_en }}"
                value="{{ $value }}"
                @if($readonly) readonly @endif
                class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:border-transparent"
            />

        </div>

    @endif

@endif