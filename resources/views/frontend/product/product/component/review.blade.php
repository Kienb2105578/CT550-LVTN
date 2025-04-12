{{-- @php
    $totalReviews = $model->reviews()->count();
    $totalRate = number_format($model->reviews()->avg('score'), 1);
    $starPercent = $totalReviews == 0 ? '0' : ($totalRate / 5) * 100;

    $fiveStar = $model->reviews()->where('score', 5)->count();
@endphp
<div class="review-container">
    <div class="panel-head">
        <div class="review-statistic">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                <div class="uk-width-large-1-3">
                    <div class="review-averate review-item">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent }}%"></div>
                        </div>
                        <div class="total-rate">{{ $totalReviews }} đánh giá</div>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="progress-block review-item">
                        @for ($i = 5; $i >= 1; $i--)
                            @php
                                $countStar = $model->reviews()->where('score', $i)->count();
                                $starPercent = $countStar > 0 ? ($countStar / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="progress-item">
                                <div class="uk-flex uk-flex-middle">
                                    <span class="text">{{ $i }}</span>
                                    <i class="fa fa-star"></i>
                                    <div class="uk-progress">
                                        <div class="uk-progress-bar" style="width: {{ $starPercent }}%;"></div>
                                    </div>
                                    <span class="text">{{ $countStar }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                @if (auth()->guard('customer')->check() && $order_product == true)
                    <div class="uk-width-large-1-3">
                        <div class="review-action review-item">
                            <div class="text">Bạn đã dùng sản phẩm này?</div>
                            <button class="btn btn-review" data-uk-modal="{target:'#review'}">Gửi đánh
                                giá</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="panel-body">
        <h2 class="review-heading">Đánh giá sản phẩm</h2>
        <div class="review-wrapper">
            @if (!is_null($product->reviews))
                @foreach ($product->reviews as $review)
                    @php
                        $avatar = $review->customer->image;
                        $name = $review->fullname;
                        $description = $review->description;
                        $score = max(1, min(5, $review->score));
                        $created_at = convertDateTime($review->created_at);
                        $replies = json_decode($review->replies, true);
                    @endphp
                    <div class="review-block-item ">
                        <div class="review-general uk-clearfix">
                            <div class="review-avatar">
                                <img id="image_review"
                                    src="{{ $avatar ? asset($avatar) : 'frontend/resources/img/no_image.png' }}">
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name uk-flex uk-flex-middle">
                                        <span>{{ $name }}</span>
                                        <span class="review-buy">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            Đã mua hàng tại {{ $system['homepage_brand'] }}
                                        </span>
                                    </div>
                                    <div class="review-star">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $score)
                                                <i class="fa fa-star"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="description">
                                        {{ $description }}
                                    </div>
                                    <div class="review-toolbox">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="created_at">Ngày {{ $created_at }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (!empty($replies))
                            @foreach ($replies as $reply)
                                <div class="review-block-item uk-clearfix reply-block">
                                    <div class="review-avatar">
                                        <img id="image_review"
                                            src="{{ $system['homepage_logo'] ? asset($system['homepage_logo']) : 'frontend/resources/img/no_image.png' }}">
                                    </div>
                                    <div class="review-content-block">
                                        <div class="review-content">
                                            <div class="name uk-flex uk-flex-middle">
                                                <span>Nhân Viên chăm sóc KH</span>

                                            </div>
                                            <div class="description">
                                                {{ $reply['reply_text'] }}
                                            </div>
                                            <div class="review-toolbox">
                                                <div class="uk-flex uk-flex-middle">
                                                    @php
                                                        $formattedDate = \Carbon\Carbon::parse(
                                                            $reply['created_at'],
                                                        )->format('Y-m-d H:i:s');
                                                    @endphp
                                                    <div class="created_at">Ngày {{ convertDateTime($formattedDate) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<div id="review" class="uk-modal uk-container-center" data-uk-modal>
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="review-popup-wrapper">
            <div class="panel-head">Đánh giá sản phẩm</div>
            <div class="panel-body">
                <div class="product-preview">
                    <span class="image img-scaledown"><img src="{{ image($model->image) }}"
                            alt="{{ $model->name }}"></span>
                    <div class="product-title uk-text-center">{{ $model->name }}</div>
                    <div class="popup-rating uk-clearfix uk-text-center">
                        <div class="rate uk-clearfix ">
                            <input type="radio" id="star5" name="rate" class="rate" value="5" />
                            <label for="star5" title="Tuyệt vời">5 stars</label>
                            <input type="radio" id="star4" name="rate" class="rate" value="4" />
                            <label for="star4" title="Hài lòng">4 stars</label>
                            <input type="radio" id="star3" name="rate" class="rate" value="3" />
                            <label for="star3" title="Bình thường">3 stars</label>
                            <input type="radio" id="star2" name="rate" class="rate" value="2" />
                            <label for="star2" title="Tạm được">2 stars</label>
                            <input type="radio" id="star1" name="rate" class="rate" value="1" />
                            <label for="star1" title="Không thích">1 star</label>
                        </div>
                        <div class="rate-text uk-hidden">
                            * Bạn chưa chọn điểm đánh giá
                        </div>
                    </div>


                    <div class="review-form">
                        <div action="" class="uk-form form">
                            <div class="form-row">
                                <textarea name="" id="" class="review-textarea"
                                    placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                            </div>
                            <div class="uk-grid uk-grid-medium">
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="fullname"
                                            value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->name : '' }}"
                                            class="review-text" placeholder="Nhập vào họ tên" readonly>
                                    </div>
                                </div>
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="phone"
                                            value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->phone : '' }}"
                                            class="review-text" placeholder="Nhập vào số điện thoại" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <input type="text" name="email"
                                    value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->email : '' }}"
                                    class="review-text" placeholder="Nhập vào email" readonly>
                            </div>
                            <div class="uk-text-center">
                                <button type="submit" value="send" class="btn-send-review" name="create">Hoàn
                                    tất</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $model->id }}">
<input type="hidden" class="review_parent_id" value="0">
<input type="hidden" class="product_id" value="{{ $product->id }}">
<input type="hidden" class="customer_id"
    value="{{ auth()->guard('customer')->user() ? auth()->guard('customer')->user()->id : '' }}"> --}}


<div class="review-container">
    <div class="panel-head">
        <div class="review-statistic">
            <div class="row g-4 d-flex align-items-center">
                <div class="col-lg-4">
                    <div class="review-averate review-item">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                        <div class="star-rating">
                            <div class="stars" style="--star-width: {{ $starPercent }}%"></div>
                        </div>
                        <div class="total-rate">{{ $totalReviews }} đánh giá</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="progress-block review-item">
                        @for ($i = 5; $i >= 1; $i--)
                            @php
                                $countStar = $model->reviews()->where('score', $i)->count();
                                $starPercent = $countStar > 0 ? ($countStar / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="progress-item">
                                <div class="d-flex align-items-center">
                                    <span class="text">{{ $i }}</span>
                                    <i class="fa fa-star ms-2 me-2"></i>
                                    <div class="progress flex-grow-1 me-2">
                                        <div class="progress-bar" style="width: {{ $starPercent }}%"></div>
                                    </div>
                                    <span class="text">{{ $countStar }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                @if (auth()->guard('customer')->check() && $order_product == true)
                    <div class="col-lg-4">
                        <div class="review-action review-item">
                            <div class="text">Bạn đã dùng sản phẩm này?</div>
                            <button class="btn btn-review" data-bs-toggle="modal" data-bs-target="#review">Gửi đánh
                                giá</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="panel-body">
        <h2 class="review-heading">Đánh giá sản phẩm</h2>
        <div class="review-wrapper">
            @if (!is_null($product->reviews))
                @foreach ($product->reviews as $review)
                    @php
                        $avatar = $review->customer->image;
                        $name = $review->fullname;
                        $description = $review->description;
                        $score = max(1, min(5, $review->score));
                        $created_at = convertDateTime($review->created_at);
                        $replies = json_decode($review->replies, true);
                    @endphp
                    <div class="review-block-item">
                        <div class="review-general clearfix">
                            <div class="review-avatar">
                                <img id="image_review"
                                    src="{{ $avatar ? asset($avatar) : 'frontend/resources/img/no_image.png' }}">
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name d-flex align-items-center">
                                        <span>{{ $name }}</span>
                                        <span class="review-buy ms-2">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            Đã mua hàng tại {{ $system['homepage_brand'] }}
                                        </span>
                                    </div>
                                    <div class="review-star">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $score)
                                                <i class="fa fa-star"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="description">
                                        {{ $description }}
                                    </div>
                                    <div class="review-toolbox">
                                        <div class="d-flex align-items-center">
                                            <div class="created_at">Ngày {{ $created_at }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (!empty($replies))
                            @foreach ($replies as $reply)
                                <div class="review-block-item clearfix reply-block">
                                    <div class="review-avatar">
                                        <img id="image_review"
                                            src="{{ $system['homepage_logo'] ? asset($system['homepage_logo']) : 'frontend/resources/img/no_image.png' }}">
                                    </div>
                                    <div class="review-content-block">
                                        <div class="review-content">
                                            <div class="name d-flex align-items-center">
                                                <span>Nhân Viên chăm sóc KH</span>
                                            </div>
                                            <div class="description">
                                                {{ $reply['reply_text'] }}
                                            </div>
                                            <div class="review-toolbox">
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $formattedDate = \Carbon\Carbon::parse(
                                                            $reply['created_at'],
                                                        )->format('Y-m-d H:i:s');
                                                    @endphp
                                                    <div class="created_at">Ngày {{ convertDateTime($formattedDate) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="review" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close ms-auto mt-2 me-2" data-bs-dismiss="modal"
                aria-label="Close"></button>
            <div class="review-popup-wrapper">
                <div class="panel-head">Đánh giá sản phẩm</div>
                <div class="panel-body">
                    <div class="product-preview">
                        <span class="image img-scaledown"><img src="{{ image($model->image) }}"
                                alt="{{ $model->name }}"></span>
                        <div class="product-title text-center">{{ $model->name }}</div>
                        <div class="popup-rating clearfix text-center">
                            <div class="rate clearfix">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rate" class="rate"
                                        value="{{ $i }}" />
                                    <label for="star{{ $i }}">{{ $i }} stars</label>
                                @endfor
                            </div>
                            <div class="rate-text d-none">
                                * Bạn chưa chọn điểm đánh giá
                            </div>
                        </div>

                        <div class="review-form">
                            <div class="form">
                                <div class="form-row mb-3">
                                    <textarea class="review-textarea form-control" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="form-row">
                                            <input type="text" name="fullname"
                                                value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->name : '' }}"
                                                class="review-text form-control" placeholder="Nhập vào họ tên" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-row">
                                            <input type="text" name="phone"
                                                value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->phone : '' }}"
                                                class="review-text form-control" placeholder="Nhập vào số điện thoại"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row my-3">
                                    <input type="text" name="email"
                                        value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->email : '' }}"
                                        class="review-text form-control" placeholder="Nhập vào email" readonly>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-send-review"
                                        name="create">Hoàn tất</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $model->id }}">
<input type="hidden" class="review_parent_id" value="0">
<input type="hidden" class="product_id" value="{{ $product->id }}">
<input type="hidden" class="customer_id"
    value="{{ auth()->guard('customer')->user() ? auth()->guard('customer')->user()->id : '' }}">
