
@php

$fieldMap = collect($fieldList)
    ->keyBy('field_id_name_en');

/*
|--------------------------------------------------------------------------
| SESSION DATA (your actual keys)
|--------------------------------------------------------------------------
*/

$identifierName =
    session('party_name');

$fatherName =
    session('party_father_name');

$address =
    session('party_address');

$mobile =
    session('mobile');

$age =
    session('party_age');

$gender =
    session('party_gender');

/*
|--------------------------------------------------------------------------
| CLEAN FATHER NAME
|--------------------------------------------------------------------------
*/
$fatherName = preg_replace(
    '/^(C\/O|S\/O|D\/O|W\/O)\s*:?\s*/i',
    '',
    $fatherName
);

@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    {{-- Salutation --}}
    @include('elements.identifier.field', [
        'field' => $fieldMap['salutation'] ?? null,
        'masters' => $masters
    ])

    {{-- Name --}}
    @include('elements.identifier.field', [
        'field' => $fieldMap['identification_full_name_en'] ?? null,
        'value' => $identifierName,
        'masters' => $masters
    ])

    {{-- Father Name --}}
    @include('elements.identifier.field', [
        'field' => $fieldMap['father_full_name_en'] ?? null,
        'value' => $fatherName,
        'masters' => $masters
    ])

    {{-- Occupation --}}
    @include('elements.identifier.field', [
        'field' => $fieldMap['occupation_id'] ?? null,
        'masters' => $masters
    ])

    {{-- Address --}}
    @include('elements.identifier.field', [
        'field' => $fieldMap['address_en'] ?? null,
        'value' => $address,
        'masters' => $masters
    ])

    {{-- Home Visit --}}
    <!-- @include('elements.identifier.field', [
        'field' => $fieldMap['homevisit_flag'] ?? null,
        'masters' => $masters
    ]) -->

</div>