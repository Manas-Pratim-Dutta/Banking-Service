@foreach($dags as $index => $dag)
    <button type="button"
        class="dag-tab {{ $index === 0 ? 'active' : '' }} px-5 py-2 rounded-full text-sm font-medium border-[1.5px] transition-all duration-150 cursor-pointer"
        data-target="dag-{{ $dag['dagno'] }}">
        Dag {{ $dag['dagno'] }}
    </button>
@endforeach