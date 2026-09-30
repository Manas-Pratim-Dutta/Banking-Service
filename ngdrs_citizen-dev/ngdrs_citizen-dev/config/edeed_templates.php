<?php

// article_id => ['E' => method, 'A' => method, 'B' => method]
// Ported 1:1 from the if-chain in e_deed(). Where the original had no
// language-specific branch, the same method is mapped under all three
// keys so lookups are consistent regardless of the UI language pick.

return [
    91  => ['E' => 'saleTemplate', 'A' => 'saleTemplateAssamese', 'B' => 'saleTemplateBengali'],
    40  => ['E' => 'mortgageDeed', 'A' => 'mortgageDeedAssamese', 'B' => 'mortgageDeedBengali'],
    216 => ['E' => 'affidavitWithProperty'],
    3   => ['E' => 'affidavitWithoutProperty'],
    220 => ['E' => 'willWithProperty', 'A' => 'willWithPropertyAssamese', 'B' => 'willWithPropertyBengali'],

    1   => ['E' => 'acknowledgementTemplate', 'A' => 'acknowledgementTemplate', 'B' => 'acknowledgementTemplate'],
    55  => ['E' => 'settlementTemplate', 'A' => 'settlementTemplate', 'B' => 'settlementTemplate'],
    47  => ['E' => 'partnershipDeed', 'A' => 'partnershipDeed', 'B' => 'partnershipDeed'],

    87  => ['E' => 'adoptionDeed', 'A' => 'adoptionDeedAssamese', 'B' => 'adoptionDeedBengali'],
    51  => ['E' => 'reconveyanceDeed', 'A' => 'reconveyanceDeedAssamese', 'B' => 'reconveyanceDeedBengali'],
    58  => ['E' => 'surrenderLease'],

    61  => ['E' => 'trustWithProperty', 'A' => 'trustWithProperty', 'B' => 'trustWithProperty'],
    219 => ['E' => 'trustWithoutProperty', 'A' => 'trustWithoutProperty', 'B' => 'trustWithoutProperty'],

    34  => ['E' => 'giftDeed', 'A' => 'GiftAssamese'], // no Bengali gift variant exists

    204 => ['E' => 'agreementWithProperty'],
    210 => ['E' => 'agreementWithoutProperty'],

    217 => ['E' => 'poaWithProperty', 'A' => 'poaWithPropertyAssamese', 'B' => 'poaWithPropertyBengali'],
    86  => ['E' => 'poaWithoutProperty'],

    46  => ['E' => 'partition', 'A' => 'partitionAssamese', 'B' => 'partitionBengali'],

    212 => ['E' => 'cancellationWithoutProperty', 'A' => 'cancellationWithoutPropertyAssamese', 'B' => 'cancellationWithoutPropertyBengali'],
    161 => ['E' => 'cancellationWithProperty'],

    12  => ['E' => 'award'],
    10  => ['E' => 'articleOfAssociation'],

    117 => ['E' => 'debenture', 'A' => 'debentureAssamese', 'B' => 'debentureBengali'],
    13  => ['E' => 'simpleMoneyBond', 'A' => 'simpleMoneyBondAssamese', 'B' => 'simpleMoneyBondBengali'],

    89  => ['E' => 'lease'],
    104 => ['E' => 'release'],

    94  => ['E' => 'rectificationDeed', 'A' => 'rectificationDeedAssamese', 'B' => 'rectificationDeedBengali'],
    32  => ['E' => 'exchangeProperty', 'A' => 'exchangePropertyAssamese', 'B' => 'exchangePropertyBengali'],

    130 => ['E' => 'dissolutionPartnership'],

    92  => ['E' => 'saleCertificate', 'A' => 'saleCertificateAssamese', 'B' => 'saleCertificateBengali'],
];