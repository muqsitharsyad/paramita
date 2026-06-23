@php
    $state = trim((string) $getState());
    $svg = str_starts_with($state, '<svg')
        ? $state
        : \App\Models\SidebarMenuItem::iconSvg($state)->toHtml();
@endphp

<span style="display:inline-flex;width:28px;height:28px;align-items:center;justify-content:center;color:#374151;">
    {!! $svg !!}
</span>
