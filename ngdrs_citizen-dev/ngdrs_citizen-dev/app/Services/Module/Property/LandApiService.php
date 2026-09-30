<?php

namespace App\Services\Module\Property;

use App\Services\Actions\Property\PropertyDataAction;

class LandApiService
{
    protected PropertyDataAction $propertyDataAction;

    public function __construct(PropertyDataAction $propertyDataAction)
    {
        $this->propertyDataAction = $propertyDataAction;
    }

    public function nocApi(string $nocNo, string $dharitriDistrictCode): ?array
    {
        return $this->propertyDataAction->fetchNoc($nocNo, $dharitriDistrictCode);
    }
    public function nocIndustrialLeaseApi(string $nocNo, string $dharitriDistrictCode): ?array
    {
        return $this->propertyDataAction->fetchIndustrialLeaseNoc($nocNo, $dharitriDistrictCode);
    }
    public function verifyDagApi($validated): ?array
    {
        return $this->propertyDataAction->verifyDag($validated);
    }
}