@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url =
        $config['method'] == 'create'
            ? route('purchase-order.store')
            : route('purchase-order.update', [$purchaseOrder->id, $queryUrl ?? '']);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox w">
                    <div class="ibox-title">
                        <h5>NHÀ CUNG CẤP</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <select name="supplier_id" class="form-control setupSelect2">
                                        <option value="">-- Chọn nhà cung cấp --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!isset($purchaseOrder) || !$purchaseOrder->status || $purchaseOrder->status === 'pending')
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Chọn sản phẩm <span class="text-danger">(*)</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row mb15">
                                <div class="col-lg-12">
                                    <div class="form-row">
                                        <select name="product_id" id="product_id" class="form-control setupSelect2">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->product_id }}">{{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div id="product-info-container">
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.purchase-order.component.aside')
            </div>
        </div>

        @if (!isset($purchaseOrder) || !$purchaseOrder->status || $purchaseOrder->status === 'pending')
            @include('backend.dashboard.component.button')
        @endif
    </div>
</form>
<input type="hidden" id="purchase_order_id" value="{{ $purchaseOrder->id ?? null }}">
