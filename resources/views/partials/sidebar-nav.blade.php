@php
    $role = \App\Helpers\RoleHelper::getPrimaryRoleName(auth()->user()) ?? 'viewer';
    $menuItems = \App\Models\SidebarMenuItem::treeForRole($role);
@endphp

{{-- Menu Items dari Database (dikelola dari admin panel) --}}
@foreach($menuItems as $item)
    @php $menuUrl = $item->getUrl(); @endphp
    @if($menuUrl)
    <li><a href="{{ $menuUrl }}" class="{{ $item->isActiveRoute() ? 'active' : '' }}">
        <span class="icon">
            @switch($item->icon)
                @case('dashboard') <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> @break
                @case('templates') <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> @break
                @case('vendors') <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> @break
                @case('monitoring') <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> @break
                @case('reports') <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg> @break
                @default <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            @endswitch
        </span>
        {{ $item->label }}
    </a></li>
    @endif

    {{-- Sub-menus --}}
    @if($item->children->isNotEmpty())
        @foreach($item->children as $child)
            @php $childUrl = $child->getUrl(); @endphp
            @if($childUrl)
            <li style="padding-left: 16px;"><a href="{{ $childUrl }}" class="{{ $child->isActiveRoute() ? 'active' : '' }}" style="font-size: 0.8125rem;">
                <span class="icon" style="width: 16px; height: 16px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </span>
                {{ $child->label }}
            </a></li>
            @endif
        @endforeach
    @endif
@endforeach
