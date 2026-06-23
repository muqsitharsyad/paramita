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
            {{ \App\Models\SidebarMenuItem::iconSvg($item->icon) }}
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
                    {{ \App\Models\SidebarMenuItem::iconSvg($child->icon) }}
                </span>
                {{ $child->label }}
            </a></li>
            @endif
        @endforeach
    @endif
@endforeach
