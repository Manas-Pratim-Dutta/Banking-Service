@php

$field = $field ?? null;
$party = $party ?? null;
$masters = $masters ?? [];

@endphp

@if(!$field || !$party)
    @php return; @endphp
@endif

@php

$fieldName =
    $field->field_id_name_en;

$value =
    $party->$fieldName ?? '';

if (
    isset($masters[$fieldName]) &&
    !empty($masters[$fieldName])
) {

    $masterOptions =
        $masters[$fieldName];

    foreach (
        $masterOptions as $key => $master
    ) {

        // Associative format
        if (
            !is_array($master)
        ) {

            if (
                (string)$key ===
                (string)$value
            ) {

                $value = $master;
                break;
            }

        } else {

            // Array format
            if (
                isset($master['value']) &&
                (string)$master['value']
                === (string)$value
            ) {

                $value =
                    $master['text']
                    ?? $master['label']
                    ?? $value;

                break;
            }
        }
    }
}

@endphp
@if(
    $fieldName === 'pan_no' &&
    ($party->pan_form_list ?? null) != 5
)
    @php return; @endphp
@endif
@if($fieldName === 'poa_id')

    @php
        $targetPartyId = $party->power_attoney_party_id ?? null;

        $poaName = null;

        if (!empty($targetPartyId)) {
            $poaName = \App\Models\PartyEntry::where(
                'id',
                $targetPartyId
            )->value('party_full_name_en');
        }
    @endphp

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $field->field_name_en }}
        </label>

        <input
            type="text"
            readonly
            value="{{ $poaName ?? 'N/A' }}"
            class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 bg-gray-100"
        />
    </div>

    @php return; @endphp

@endif
<div>

    <label
        class="block text-sm font-medium text-gray-700 mb-1.5"
    >
        {{ $field->field_name_en }}
    </label>

    <input
        type="text"
        readonly
        value="{{ $value }}"
        class="w-full border border-gray-300 rounded-lg text-sm px-4 py-2.5 bg-gray-100"
    />

</div>