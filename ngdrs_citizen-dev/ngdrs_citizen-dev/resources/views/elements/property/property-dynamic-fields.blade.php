@foreach($fields as $field)
    @php
        $actions = json_decode($field->field_actions, true);
    @endphp
    <div class="mb-4 p-0.5">
        @switch($field->type)
            @case('text')
                <x-text-input 
                    label="{{ $field->label }}"
                    required="{{ $field->is_required }}"
                    placeholder="{{ $field->placeholder }}"
                    id="{{ $field->field_key }}"
                    data-rule="{{ $field->validation_rules }}" 
                />
            @break

            @case('select')
                <x-select-input 
                    label="{{ $field->label }}"
                    required="{{ $field->is_required }}"
                    id="{{ $field->field_key }}"
                    name="{{ $field->field_key }}"
                    :options="json_decode($field->options, true)" 
                />
            @break

            @case('date')
                <x-date-input label="{{ $field->label }}" id="{{ $field->field_key }}"/>
                @break

            @case('numeric')
                <x-number-input label="{{ $field->label }}" id="{{ $field->field_key }}" name="{{ $field->field_key }}" readonly="{{ $field->is_readonly }}"/>
                @break
        @endswitch

        @if(!empty($actions['actions']))
            @foreach($actions['actions'] as $action)

                @if($action['type'] === 'button')
                    <x-ui.button type="button" label="{{ $action['label'] }}" id="{{ $action['id'] }}" dataAction="{{ $action['data-action'] }}" wrapperClass="mt-4" />
                @endif

                @if($action['type'] === 'link')
                    <a href="{{ $action['url'] }}" class="text-blue-600 underline ml-2">
                        {{ $action['label'] }}
                    </a>
                @endif

            @endforeach
        @endif
    </div>
@endforeach