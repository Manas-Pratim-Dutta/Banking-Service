@php
    $showRemoveButton = $showRemoveButton ?? true;
@endphp

@foreach($dags as $index => $dag)
    @include('elements.property.dag-content', [
        'dag' => [
            'id' => $dag['dagno'],
            'dag_no' => $dag['dagno'],
            'patta_no' => $dag['pattano'],
            'area_bigha' => $dag['bigha'],
            'area_kattha' => $dag['katha'],
            'area_lessa' => $dag['lessa'],
            'zonal_rate' => $dag['zonalvaluerate'],
            'land_valuation' => $dag['landvaluation'],
            'zone_name' => $dag['zonename'],
            'zone_class' => $dag['zonalclass'],
            'dharitree_class' => $dag['dharitreeclass'],
            'boundary_east' => $dag['eastside'],
            'boundary_west' => $dag['westside'],
            'boundary_south' => $dag['southside'],
            'boundary_north' => $dag['northside'],
            'additional_info' => '',
            'patta_type_id' => $dag['patta_type_id'],
            'patta_type_desc' => $dag['patta_type_desc']
        ],
        'isFirst' => $index === 0,
        'showRemoveButton' => $showRemoveButton,
    ])
@endforeach