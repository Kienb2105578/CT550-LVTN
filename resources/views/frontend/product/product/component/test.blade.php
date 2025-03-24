<div class="panel-body">
    <div class="review-filter">
        <div class="uk-flex uk-flex-middle">
            <span class="filter-text">Lọc xem theo: </span>
            <div class="filter-item">
                <span>Đã mua hàng</span>
                <span>5 sao</span>
                <span>4 sao</span>
                <span>3 sao</span>
                <span>2 sao</span>
                <span>1 sao</span>
            </div>
        </div>
    </div>
    <div class="review-wrapper">
        @if (!is_null($product->reviews))
            @foreach ($product->reviews as $review)
                @php
                    $avatar = getReviewName($review->fullname);
                    $name = $review->fullname;
                    $email = $review->email;
                    $phone = $review->phone;
                    $description = $review->description;
                    $rating = generateStar($review->score);
                    $created_at = convertDateTime($review->created_at);
                @endphp
                <div class="review-block-item ">
                    <div class="review-general uk-clearfix">
                        <div class="review-avatar">
                            <span class="shae">{{ $avatar }}</span>
                        </div>
                        <div class="review-content-block">
                            <div class="review-content">
                                <div class="name uk-flex uk-flex-middle">
                                    <span>{{ $name }}</span>
                                    {{-- <span class="review-buy">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        Đã mua hàng tại {{ $system['homepage_brand'] }}
                                    </span> --}}
                                </div>
                                {!! $rating !!}
                                <div class="description">
                                    {{ $description }}
                                </div>
                                <div class="review-toolbox">
                                    <div class="uk-flex uk-flex-middle">
                                        <div class="created_at">Ngày {{ $created_at }}</div>
                                        {{-- <div class="review-reply" data-uk-modal="{target:'#review'}">Trả lời</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="review-block-item uk-clearfix reply-block">
                        <div class="review-avatar">
                            <span class="shae">LV</span>
                        </div>
                        <div class="review-content-block">
                            <div class="review-content">
                                <div class="name uk-flex uk-flex-middle">
                                    <span>Nguyễn Công Tuấn</span>
                                    <span class="review-buy">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        Đã mua hàng tại {{ $system['homepage_brand'] }}
                                    </span>
                                </div>
                                <div class="review-star">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star-o"></i>
                                </div>
                                <div class="description">
                                    Chào anh Cường,
                                    Dạ Samsung Galaxy Z Flip4 5G 128GB có giá niêm yết 23.990.000đ, được giảm còn 11.990.000đ  áp dụng đơn hàng online đến 22h ngày 25/12 anh nha. Anh đang ở tỉnh, thành nào để bên em kiểm tra shop có hàng gần nhất ạ?  Để được hỗ trợ chi tiết về sản phẩm, anh vui lòng liên hệ tổng đài miễn phí 18006601 hoặc để lại SĐT bên em liên hệ tư vấn nhanh nhất ạ.Thân mến!
                                </div>
                                <div class="review-toolbox">
                                    <div class="uk-flex uk-flex-middle">
                                        <div class="created_at">Ngày 22/12/2023</div>
                                        <div class="review-reply">Trả lời</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            @endforeach
        @endif
    </div>
</div>
