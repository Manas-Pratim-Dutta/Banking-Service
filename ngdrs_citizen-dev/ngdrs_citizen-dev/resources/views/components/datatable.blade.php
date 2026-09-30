@props([
    'headers' => [],
    'rows' => [],
    'showAction' => false,
    'actionLabel' => 'Select',
    'actionRoute' => null,
    'actionKey' => null,
])

<div class="bg-white py-6 px-8">
    <table id="table" class="w-full text-sm">
        <thead>
            <tr class="border-b">
                @foreach ($headers as $header)
                    <th class="py-2 text-left font-medium text-zinc-700">
                        {{ $header }}
                    </th>
                @endforeach

                @if($showAction)
                    <th class="py-2 text-left font-medium text-zinc-700">
                        Action
                    </th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse ($rows as $row)
                <tr class="border-b last:border-b-0 hover:bg-zinc-50">
                    @foreach ($row as $key => $cell)
                        @if($key !== $actionKey)
                            <td class="py-2 text-zinc-700">
                                {{ $cell }}
                            </td>
                        @endif
                    @endforeach

                    @if($showAction && $actionRoute && $actionKey)
                        <td class="py-2">
                            <a href="{{ route($actionRoute, $row[$actionKey]) }}"
                               class="inline-flex items-center rounded-md border
                                      border-primary px-3 py-1.5 text-xs
                                      font-medium text-primary hover:bg-primary/10">
                                {{ $actionLabel }}
                            </a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($showAction ? 1 : 0) }}"
                        class="py-6 text-center text-zinc-400">
                        No records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
