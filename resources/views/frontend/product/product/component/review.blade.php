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
                @foreach ($product->reviews->sortByDesc('created_at') as $review)
                    @php
                        $avatar = $review->customer->image;
                        $name = $review->fullname;
                        $description = $review->description;
                        $score = max(1, min(5, $review->score));
                        $created_at = convertDateTime($review->created_at);
                        $replies = json_decode($review->replies, true);
                        $images = json_decode($review->images, true);
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
                                    @if (!empty($images))
                                        <div class="review-images">
                                            <div class="image-gallery">
                                                @foreach ($images as $image)
                                                    <div class="image-item">
                                                        <img src="{{ asset($image) }}" alt="Review Image">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="review-toolbox">
                                        <div class="d-flex align-items-center">
                                            <div class="created_at">Ngày {{ $created_at }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .review-images {
                                margin-top: 20px;
                            }

                            .image-gallery {
                                display: flex;
                                gap: 10px;
                                flex-wrap: wrap;
                            }

                            .image-item {
                                width: 90px;
                                height: 90px;
                                overflow: hidden;
                            }

                            .image-item img {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                            }
                        </style>
                        @if (!empty($replies))
                            @foreach ($replies as $reply)
                                <div class="review-block-item clearfix reply-block">
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="review-popup-wrapper">
                <div class="panel-head d-flex justify-content-between align-items-center">
                    <div>Đánh giá sản phẩm</div>
                    <button type="button" class="btn-close mt-2 me-2" style="font-size: 18px" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="panel-body">
                    <div class="product-preview">
                        <div class="row mt-5">
                            <div class="col-lg-6">
                                <span class="image img-scaledown"><img src="{{ image($model->image) }}"
                                        alt="{{ $model->name }}"></span>
                                <div class="product-title text-center">{{ $model->name }}</div>
                                <div class="popup-rating clearfix text-center">
                                    <div class="rate clearfix">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rate"
                                                class="rate" value="{{ $i }}" />
                                            <label for="star{{ $i }}">{{ $i }} stars</label>
                                        @endfor
                                    </div>
                                    <div class="rate-text d-none">
                                        * Bạn chưa chọn điểm đánh giá
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="form-row mb-3 me-4">
                                    <textarea class="review-textarea form-control" rows="10" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="review-form">
                            <div class="form">

                                <div class="form-row mb-3">
                                    <button type="button" id="custom-upload-btn" class="btn btn-login"
                                        style="font-size: 14px;">Chọn ảnh đánh giá</button>
                                    <input type="file" name="images[]" id="review-images" class="d-none" multiple
                                        accept="image/*">
                                    <div id="preview-images" class="mt-3 d-flex flex-wrap gap-2"></div>
                                </div>


                                <script>
                                    document.getElementById('custom-upload-btn').addEventListener('click', function() {
                                        document.getElementById('review-images').click();
                                    });

                                    document.getElementById('review-images').addEventListener('change', function(event) {
                                        const previewContainer = document.getElementById('preview-images');
                                        previewContainer.innerHTML = '';

                                        const files = event.target.files;
                                        if (files) {
                                            [...files].forEach(file => {
                                                if (file.type.startsWith('image/')) {
                                                    const reader = new FileReader();
                                                    reader.onload = function(e) {
                                                        const img = document.createElement('img');
                                                        img.src = e.target.result;
                                                        img.classList.add('img-thumbnail');
                                                        img.style.width = '100px';
                                                        img.style.height = '100px';
                                                        img.style.objectFit = 'cover';
                                                        previewContainer.appendChild(img);
                                                    };
                                                    reader.readAsDataURL(file);
                                                }
                                            });
                                        }
                                    });
                                </script>

                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="form-row">
                                            <input type="hidden" name="fullname"
                                                value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->name : '' }}"
                                                class="review-text form-control" placeholder="Nhập vào họ tên"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-row">
                                            <input type="hidden" name="phone"
                                                value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->phone : '' }}"
                                                class="review-text form-control" placeholder="Nhập vào số điện thoại"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row my-3">
                                    <input type="hidden" name="email"
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
