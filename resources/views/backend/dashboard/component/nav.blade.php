<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <!-- User Dropdown -->
            <li>
                <img alt="image" class="img-circle"
                    src="{{ auth()->user()->image ?? 'frontend/resources/img/no_image.png' }}"
                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 1px solid #ccc; margin-right: 10px;" />
            </li>
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="clear">
                        <span class="block m-t-xs">
                            <strong class="font-bold">{{ auth()->user()->name }} <b class="caret"></b></strong>
                        </span>
                    </span>
                </a>
                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                    <li><a href="{{ route('user.edit', auth()->user()->id) }}">Tài khoản</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ route('auth.logout') }}">Đăng xuất</a></li>
                </ul>
            </li>
            <li>
            </li>
        </ul>
    </nav>
</div>
