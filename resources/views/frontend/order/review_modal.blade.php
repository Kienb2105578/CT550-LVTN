<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<div id="review-{{ $product->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
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
                                <span class="image img-scaledown">
                                    <img src="{{ image($product->image) }}" alt="{{ $product->name }}">
                                </span>
                                <div class="product-title text-center">{{ $product->name }}
                                </div>
                                <div class="popup-rating clearfix text-center">
                                    <div class="rate clearfix">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}-{{ $product->id }}"
                                                name="rate-{{ $product->id }}" class="rate"
                                                value="{{ $i }}" />
                                            <label
                                                for="star{{ $i }}-{{ $product->id }}">{{ $i }}
                                                sao</label>
                                        @endfor
                                    </div>
                                    <div class="rate-text d-none">* Bạn chưa chọn điểm đánh giá
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
                                    <button type="button" class="btn btn-login custom-upload-btn"
                                        style="font-size: 14px;">Chọn ảnh đánh giá</button>
                                    <input type="file" name="images[]" class="review-images d-none" multiple
                                        accept="image/*">
                                    <div class="preview-images mt-3 d-flex flex-wrap gap-2">
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <input type="hidden" name="fullname"
                                            value="{{ auth()->guard('customer')->user()->name ?? '' }}"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="hidden" name="phone"
                                            value="{{ auth()->guard('customer')->user()->phone ?? '' }}"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-row my-3">
                                    <input type="hidden" name="email"
                                        value="{{ auth()->guard('customer')->user()->email ?? '' }}"
                                        class="form-control" readonly>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-send-review"
                                        data-product-id="{{ $product->id }}">Hoàn
                                        tất</button>
                                </div>

                                <input type="hidden" class="product_id" value="{{ $product->id }}">
                                <input type="hidden" class="customer_id"
                                    value="{{ auth()->guard('customer')->user()->id ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.custom-upload-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = btn.nextElementSibling;
                if (input) input.click();
            });
        });

        document.querySelectorAll('.review-images').forEach(function(input) {
            input.addEventListener('change', function(event) {
                const previewContainer = input.nextElementSibling;
                previewContainer.innerHTML = '';
                [...event.target.files].forEach(file => {
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
            });
        });
    });
</script>
