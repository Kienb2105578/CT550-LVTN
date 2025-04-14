<div class="ibox product-info">
    <div class="ibox-title">
        <h5>Thông tin sản phẩm</h5>
    </div>
    <div class="ibox-content">
        <div class="form-group">
            <label for="product_catalogue">Danh mục sản phẩm</label>
            <select class="form-control" id="product_catalogue" name="product_catalogue_id">
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="product_select">Chọn sản phẩm</label>
            <select class="form-control" id="product_select" name="product_id">
                <option value="">-- Chọn sản phẩm --</option>
            </select>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng hiện tại</th>
                    <th>Số lượng nhập mới</th>
                </tr>
            </thead>
            <tbody id="product-info-body">
                <!-- Dữ liệu sản phẩm sẽ được thêm vào đây -->
            </tbody>
        </table>

        <!-- Danh sách phiên bản NẰM BÊN TRONG phần thông tin sản phẩm -->
        <div id="variant-section" class="mt-4 hidden">
            <h5>Danh sách phiên bản</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên phiên bản</th>
                        <th>Số lượng hiện tại</th>
                        <th>Số lượng nhập mới</th>
                    </tr>
                </thead>
                <tbody id="variant-info-body">
                    <!-- Dữ liệu phiên bản sẽ được thêm vào đây -->
                </tbody>
            </table>
        </div>
    </div>
</div>
