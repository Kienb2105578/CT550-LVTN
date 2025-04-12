<style>
    .main-menu .dropdown-menu li:hover>a {
        background: #056fda !important;
        color: #fff !important;
    }

    .nav-item.dropdown:hover>.dropdown-menu {
        display: block;
        margin-top: 0;
    }

    .main-menu .nav-item .nav-link {
        padding: 15px 20px;
    }
</style>

<nav class="navigation navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
            <ul class="navbar-nav main-menu mx-auto">
                {!! $menu['main-menu'] !!}
            </ul>
        </div>
    </div>
</nav>
