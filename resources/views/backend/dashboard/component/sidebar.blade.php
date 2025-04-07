@php
    $segment = request()->segment(1);
@endphp
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <h2 style="color: white; font-weight: bold;">ADMIN INCOM</h2>
                </div>

            </li>
            @php
                $sidebarModules = __('sidebar.module');
                $selectedModules = array_slice($sidebarModules, 0, 1);

                if ($languages->first()?->id == 1) {
                    $accessibleMenus = array_merge($selectedModules, $accessibleMenus ?? []);
                }
            @endphp

            @foreach ($accessibleMenus as $key => $val)
                <li
                    class="{{ isset($val['class']) ? $val['class'] : '' }} {{ in_array($segment, $val['name']) ? 'active' : '' }}">
                    <a href="{{ isset($val['route']) ? $val['route'] : '' }}">
                        <i class="{{ $val['icon'] }}"></i>
                        <span class="nav-label">{{ $val['title'] }}</span>
                        @if (isset($val['subModule']) && count($val['subModule']))
                            <span class="fa arrow"></span>
                        @endif
                    </a>
                    @if (isset($val['subModule']))
                        <ul class="nav nav-second-level">
                            @foreach ($val['subModule'] as $module)
                                <li><a href="{{ $module['route'] }}">{{ $module['title'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach

        </ul>
    </div>
</nav>
