@include('admin.dashboard.component.breadcrumb', ['title' => $config['seo']['show']['title']])

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-4">
            <div class="panel-title">Danh sách menu</div>
            <div class="panel-description">
                <p>+ Danh sách Menu giúp bạn dễ dàng kiểm soát bố cục menu. Bạn có thể thêm mới hoặc cập nhật menu bằng
                    nút <span class="text-success">Cập nhật Menu</span></p>

            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5 style="margin:0;">{{ $menuCatalogue->name }}</h5>
                    <a href="{{ route('menu.editMenu', ['id' => $id]) }}" class="custom-button">Cập nhật Menu cấp
                        1</a>
                </div>
                <div class="ibox-content" id="dataCatalogue" data-catalogueId="{{ $id }}">
                    @php
                        $menus = recursive($menus);
                        $menuString = recursive_menu($menus);
                    @endphp
                    @if (count($menus))
                        <div class="dd" id="nestable2">
                            <ol class="dd-list">
                                {!! $menuString !!}
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
