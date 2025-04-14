<div class="ibox">
    <div class="ibox-title">
        <h5>CHỌN SẢN PHẨM</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="product_id" id="product_select" class="form-control setupSelect2"
                        style="height: 40px !important">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox-footer">
        <div class="row">
            <div class="col-lg-12">
                <h5 style="font-size: 14px">Danh sách biến thể:</h5>
                <div id="variants-container">
                    <!-- Các biến thể sẽ được load tại đây -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>DANH SÁCH SẢN PHẨM</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="selected-products-table">
                    <thead>
                        <tr>
                            <th class="text-center">Tên sản phẩm</th>
                            <th class="text-center" style="width:15%">Số lượng</th>
                            <th class="text-center" style="width:25%">Giá bán</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 text-right mt-3">
                <strong>Tổng giá bán: <span id="total-price">420,000 VND</span></strong>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const quantityInputs = document.querySelectorAll(".quantity");

        function updateTotalPrice() {
            let total = 0;
            quantityInputs.forEach(input => {
                let price = parseInt(input.getAttribute("data-price"));
                let quantity = parseInt(input.value);
                total += price * quantity;
            });
            document.getElementById("total-price").textContent = new Intl.NumberFormat("vi-VN").format(total) +
                " VND";
        }
        quantityInputs.forEach(input => {
            input.addEventListener("input", updateTotalPrice);
        });
        updateTotalPrice();
    });
</script>

<style>
    #variants-container .variant-checkbox {
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1px solid #000;
        display: inline-block;
        position: relative;
        cursor: pointer;
        margin-right: 20px;
    }

    #variants-container .variant-checkbox:checked::before {
        content: "";
        width: 8px;
        height: 8px;
        background-color: #056fda;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    #variants-container {
        margin: 15px 0;
        max-height: 200px;
        overflow-y: auto;
    }

    #variants-container>div {
        margin-bottom: 8px;
        padding: 5px;
        border-bottom: 1px solid #eee;
    }

    .quantity-input {
        width: 80px;
        display: inline-block;
    }

    #selected-products-table {
        margin-top: 20px;
    }

    .select2-container {
        width: 100% !important;
        height: 40px !important;
    }

    .selection {
        height: 40px !important;
    }

    .ibox {
        margin-bottom: 20px;
    }

    .ibox-title {
        padding: 15px;
        border-bottom: 1px solid #e7eaec;
    }

    .ibox-content {
        padding: 15px;
        background-color: #ffffff;
    }

    .ibox-footer {
        margin-top: 30px;
        padding: 10px 15px;
        background-color: #fff;
        border-top: 1px solid #e7eaec;
    }
</style>
