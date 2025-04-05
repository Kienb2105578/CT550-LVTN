<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h3 id="title-admin">{{ $title }}</h3>
        <ol class="breadcrumb" style="margin-bottom:10px;">
            <li>
                <a href="{{ route('dashboard.index') }}">Tổng quan</a>
            </li>
            <li class="active"><strong>{{ $title }}</strong></li>
        </ol>
    </div>
</div>
<style>
    #title-admin {
        color: #4682b4;
        text-transform: uppercase;
        margin-top: 30px;
    }
</style>
