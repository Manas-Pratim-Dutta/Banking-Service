
@php

$fieldMap = collect($fieldList)
    ->keyBy('field_id_name_en');

/*
|--------------------------------------------------------------------------
| SESSION DATA (your actual keys)
|--------------------------------------------------------------------------
*/

$witnessName =
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
    @include('elements.witness.field', [
        'field' => $fieldMap['salutation'] ?? null,
        'masters' => $masters
    ])

    {{-- Name --}}
    @include('elements.witness.field', [
        'field' => $fieldMap['witness_full_name_en'] ?? null,
        'value' => $witnessName,
        'masters' => $masters
    ])

    {{-- Father Name --}}
    @include('elements.witness.field', [
        'field' => $fieldMap['father_full_name_en'] ?? null,
        'value' => $fatherName,
        'masters' => $masters
    ])

    {{-- Occupation --}}
    @include('elements.witness.field', [
        'field' => $fieldMap['occupation_id'] ?? null,
        'masters' => $masters
    ])

    {{-- Address --}}
    @include('elements.witness.field', [
        'field' => $fieldMap['address_en'] ?? null,
        'value' => $address,
        'masters' => $masters
    ])

    {{-- Home Visit --}}
    <!-- @include('elements.witness.field', [
        'field' => $fieldMap['homevisit_flag'] ?? null,
        'masters' => $masters
    ]) -->

</div>