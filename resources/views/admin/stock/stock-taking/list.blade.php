<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h3 id="title-admin">DANH SÁCH PHIẾU KIỂM KÊ KHO</h3>
    </div>
</div>
<style>
    #title-admin {
        color: #4682b4;
        text-transform: uppercase;
        margin-top: 30px;
    }
</style>

<div class="row mt20">
    <div class="col-lg-12">
        <a href="{{ route('stock.stock-taking.create') }}" class="btn btn-danger"></i>Thực hiện kiểm kê kho</a>
        <a href="{{ route('stock.stock-taking.list') }}" class="btn btn-warning mr10"></i>Danh sách phiếu kiểm kê
            kho</a>
    </div>
</div>
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>Danh sách phiếu kiểm kê kho</h5>
                </div>
            </div>
            <div class="ibox-content">
                @include('admin.stock.stock-taking.component.filterlist')
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:3%;">STT</th>
                                <th class="text-center" style="width:10%;">Mã Lô Hàng</th>
                                <th class="text-center" style="width:10%;">Tên người kiểm kê</th>
                                <th class="text-center" style="width:10%;">Chức vụ</th>
                                <th class="text-center" style="width:10%;">Trạng thái</th>
                                <th class="text-center" style="width:10%;">Thao tác</th>
                            </tr>
                        </thead>
                        @php
                            $index = 0;
                        @endphp
                        <tbody>
                            @if ($stocks->count())
                                @foreach ($stocks as $stock)
                                    @php
                                        $index += 1;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index }}</td>
                                        <td class="text-center">{{ $stock->code }}</td>
                                        <td class="text-center">{{ $stock->user_name }}</td>
                                        <td class="text-center">{{ $stock->user_position }}</td>
                                        <td class="text-center">
                                            @if ($stock->publish == 0)
                                                <span class="badge bg-secondary">Bản nháp</span>
                                            @else
                                                <span class="badge bg-primary">Cập nhật vào kho</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <!-- Nút Sửa -->
                                            <button type="button" class="btn btn-info btn-outline btn-sm"
                                                data-toggle="modal" data-target="#updateModal-{{ $stock->id }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <!-- Nút Xóa -->
                                            <button type="button" class="btn btn-danger btn-outline btn-sm"
                                                data-toggle="modal" data-target="#deleteModal-{{ $stock->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>

                                    </tr>

                                    @include('admin.stock.stock-taking.component.modaldelete')
                                    @include('admin.stock.stock-taking.component.modalupdate')
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
                <div class="pagination-wrapper">
                    {{ $stocks->links('pagination::bootstrap-4') }}
                </div>


                <style>
                    .text-truncate-1 {
                        display: -webkit-box;
                        -webkit-line-clamp: 1;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                </style>

            </div>
        </div>
    </div>
</div>
