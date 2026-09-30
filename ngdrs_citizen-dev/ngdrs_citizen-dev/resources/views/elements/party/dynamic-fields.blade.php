@php

    $fieldMap = collect($partyFields)
        ->keyBy('field_id_name_en');
    $normalFieldMap = $fieldMap->filter(function ($field) {
        return ($field->is_auth_signtry_field ?? 'N') !== 'Y';
    });

    $signatoryFieldMap = $fieldMap->filter(function ($field) {
        return ($field->is_auth_signtry_field ?? null) === 'Y';
    });

    $partyName = session('party_name');
    $fatherName = session('party_father_name');
    $fatherName = session('party_father_name');

    $fatherName = preg_replace(
        '/^(C\/O|S\/O|D\/O|W\/O)\s*:?\s*/i',
        '',
        $fatherName
    );
    $address = session('party_address');
    $pincode = session('party_pincode');
    $gender = session('party_gender');
    $age = session('party_age');
    $mobile = session('mobile');
    $ekycType = session('ekyc_type');
    $hasFatherName =
        !empty($fatherName)
        && strtoupper(trim($fatherName)) != 'NA';

    function ekycValue($value)
    {
        return $value;
    }

    function ekycReadonly($value)
    {
        return !(
            empty($value)
            || strtoupper(trim($value)) == 'NA'
        );
    }

@endphp

<x-section-title title="Please Fill All the details" icon="images/icons/generalinfo/filldetails.svg" />
@php
    $poaPartyOptions = collect($existingParties ?? [])
        ->map(function ($party) {
            return [
                'id' => $party->id,
                'name' => $party->party_full_name_en,
            ];
        });
@endphp

@if($normalFieldMap->has('poa_id'))

    <div class="sm:col-span-2" mb-8>

        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $normalFieldMap['poa_id']->field_name_en ?? 'Power of Attorney For' }}
        </label>

        <div class="w-full border border-gray-300 rounded-lg px-4 py-3 focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-transparent">

            @forelse($poaPartyOptions as $party)

                <label class="flex items-center gap-3 py-2 px-1 rounded-md cursor-pointer hover:bg-orange-50 transition-colors">

                    <input
                        type="checkbox"
                        name="poa_id[]"
                        value="{{ $party['id'] }}"
                        class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 focus:ring-offset-0"
                    />

                    <span class="text-sm text-gray-700">
                        {{ $party['name'] }}
                    </span>

                </label>

            @empty

                <p class="text-sm text-gray-400 py-1">
                    No parties available
                </p>

            @endforelse

        </div>

    </div>
<x-section-divider />
@endif

{{-- Executer / Salutation --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $normalFieldMap['bank_id'] ?? null,
        'masters' => $masters
    ])
    

    @include('elements.party.field', [
        'field' => $normalFieldMap['salutation_id'] ?? null,
        'masters' => $masters
    ])

</div>

{{-- Name / Father Name --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $normalFieldMap['party_full_name_en'] ?? null,
        'value' => ekycValue($partyName),
        'readonly' => ekycReadonly($partyName),
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $normalFieldMap['father_full_name_en'] ?? null,
        'value' => ekycValue($fatherName),
        'readonly' => ekycReadonly($fatherName),
        'masters' => $masters
    ])
</div>

{{-- Main Cast / Relationship --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">



    @include('elements.party.field', [
        'field' => $normalFieldMap['relationship_status'] ?? null,
        'masters' => $masters,
        'value' => $hasFatherName ? 'F' : ''
    ])

    @include('elements.party.field', [
        'field' => $normalFieldMap['representative_full_name_en'] ?? null,
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $normalFieldMap['age'] ?? null,
        'value' => ekycValue($age),
        'readonly' => ekycReadonly($age),
        'masters' => $masters
    ])
    
</div>

{{-- Age --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">



</div>

<x-section-divider />

{{-- Identification --}}
<!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $fieldMap['identificationtype_id'] ?? null,
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $fieldMap['identificationtype_desc_en'] ?? null,
        'masters' => $masters
    ])

</div> -->

{{-- PAN --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">


    @include('elements.party.field', [
        'field' => $normalFieldMap['org_name_en'] ?? null,
        'masters' => $masters
    ])
    {{-- Aadhaar / Manual --}}
    @include('elements.party.field', [
        'field' => $normalFieldMap['pan_form_list'] ?? null,
        'masters' => $masters
    ])

    <div id="panFieldWrapper" class="hidden">
        @include('elements.party.field', [
            'field' => $normalFieldMap['pan_no'] ?? null,
            'masters' => $masters
        ])
    </div>
    @include('elements.party.field', [
        'field' => $normalFieldMap['tan'] ?? null,
        'masters' => $masters
    ])



</div>

{{-- Gender / Faith --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $normalFieldMap['gender_id'] ?? null,
        'value' => ekycValue($gender),
        'readonly' => ekycReadonly($gender),
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $normalFieldMap['faith_id'] ?? null,
        'masters' => $masters
    ])

</div>

{{-- Occupation / Mobile --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $normalFieldMap['occupation_id'] ?? null,
        'masters' => $masters
    ])
    @include('elements.party.field', [
        'field' => $normalFieldMap['mobile_no'] ?? null,
        'value' => ekycValue($mobile),
        'readonly' => ekycReadonly($mobile),
        'masters' => $masters
    ])

</div>

{{-- Exemption --}}
<!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $fieldMap['exemption_id'] ?? null,
        'masters' => $masters
    ])

</div> -->

<x-section-divider />

{{-- District / Circle / Village --}}
<!-- <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">

    @include('elements.party.field', [
        'field' => $fieldMap['district_id'] ?? null,
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $fieldMap['taluka_id'] ?? null,
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $fieldMap['village_id'] ?? null,
        'masters' => $masters
    ])

</div> -->

{{-- PIN / Address / Home Visit --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">

    <!-- @include('elements.party.field', [
        'field' => $fieldMap['pin_code'] ?? null,
        'value' => ekycValue($pincode),
        'readonly' => ekycReadonly($pincode),
        'masters' => $masters
    ]) -->

    @if($signatoryFieldMap->isNotEmpty())

        @include('elements.party.field', [
            'field' => $normalFieldMap['address_en'] ?? null,
            'masters' => $masters
        ])

    @else

        @include('elements.party.field', [
            'field' => $normalFieldMap['address_en'] ?? null,
            'value' => ekycValue($address),
            'readonly' => ekycReadonly($address),
            'masters' => $masters
        ])

    @endif


    @include('elements.party.field', [
        'field' => $normalFieldMap['police_station_name'] ?? null,
        'masters' => $masters
    ])

    @include('elements.party.field', [
        'field' => $normalFieldMap['post_office_name'] ?? null,
        'masters' => $masters
    ])

</div>

<x-section-divider />

@if($signatoryFieldMap->isNotEmpty())
    <x-section-title title="Authorised Signatory Details" icon="images/icons/generalinfo/filldetails.svg" />


    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

        @include('elements.party.field', [
            'field' => $signatoryFieldMap['party_full_name_en'] ?? null,
            'value' => ekycValue($partyName),
            'readonly' => ekycReadonly($partyName),
            'masters' => $masters
        ])


        @include('elements.party.field', [
            'field' => $signatoryFieldMap['age'] ?? null,
            'value' => ekycValue($age),
            'readonly' => ekycReadonly($age),
            'masters' => $masters
        ])

        @include('elements.party.field', [
            'field' => $signatoryFieldMap['mobile_no'] ?? null,
            'value' => ekycValue($mobile),
            'readonly' => ekycReadonly($mobile),
            'masters' => $masters
        ])

        @include('elements.party.field', [
            'field' => $signatoryFieldMap['address2_en'] ?? null,
            'value' => ekycValue($address),
            'readonly' => ekycReadonly($address),
            'masters' => $masters
        ])

    </div>

    <x-section-divider />
@endif
{{-- Govt Org / Police Station / Post Office --}}

<script>
    window.partyGender = @json($gender);
</script>