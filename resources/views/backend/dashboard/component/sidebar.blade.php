@php
    $segment = request()->segment(1);
@endphp
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        {{-- <img alt="image" class="img-circle" src="backend/img/profile_small.jpg" />
                         </span> --}}
                        <h1 style="color: white; font-weight: bold;">ADMIN</h1>

                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong
                                        class="font-bold">{{ auth()->user()->name }}</strong>
                                </span> <span class="text-muted text-xs block">Cài đặt <b class="caret"></b></span>
                            </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="{{ route('user.edit', auth()->user()->id) }}">Hồ sơ</a></li>

                            <li class="divider"></li>
                            <li><a href="{{ route('auth.logout') }}">Logout</a></li>
                        </ul>
                </div>

            </li>
            @php
                $sidebarModules = __('sidebar.module');
                $selectedModules = array_slice($sidebarModules, 0, 1);
                if ($languages->first()->id == 1) {
                    $accessibleMenus = array_merge($selectedModules, $accessibleMenus ?? []);
                } else {
                    $accessibleMenus = $sidebarModules;
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
