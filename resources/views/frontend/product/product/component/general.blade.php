@if (!empty($product->iframe))
    <div class="panel-product-detail mt30">
        <h2 class="heading-4"><span>Video</span></h2>
        <div class="productContent">
            {{ $product->iframe }}
        </div>
    </div>
@endif
@php
    $review = getReview($product);
@endphp
<div class="panel-product-detail mt30">
    <h2 class="heading-4 mb20">
        <span class="tab active" data-tab="info">Thông tin chi tiết</span>
        <span class="tab" data-tab="review-tab">Đánh giá ({{ $review['count'] }})</span>
    </h2>
    <div class="tab-content active" id="info">
        <div class="productContent">
            {!! $product->content !!}
        </div>
    </div>
    <div class="tab-content" id="review-tab">
        @include('frontend.product.product.component.review', [
            'model' => $product,
            'reviewable' => 'App\Models\Product',
        ])
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".tab");
        const contents = document.querySelectorAll(".tab-content");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                tabs.forEach(t => t.classList.remove("active"));
                contents.forEach(c => c.classList.remove("active"));

                this.classList.add("active");
                document.getElementById(this.dataset.tab).classList.add("active");
            });
        });
    });
</script>

<!--
<div class="woocommerce-tabs mb30">
    <ul class=" wc-tabs uk-flex uk-flex-middle"  data-uk-switcher="{connect:'#my-id'}">
       <li class="description_tab" id="tab-title-description" role="tab" >
            <a href=""> Chi tiết sản phẩm </a>
       </li>
       <li class="map_tab" id="tab-title-map_tab" role="tab" >
            <a href="">Video</a>
       </li>
   </li>
    </ul>
    <ul id="my-id" class="uk-switcher">
        <li class="tab-panel">
            <div class="woocommerce-Tabs-panel productContent">
              
            </div>
        </li>
    </ul>
</div> -->
