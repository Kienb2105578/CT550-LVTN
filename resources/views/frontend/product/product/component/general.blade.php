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
