<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Khách hàng</th>
                <th>Số điện thoại</th>
                <th style="width: 300px;">Nội dung</th>
                <th>Rate</th>
                <th>Đối tượng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($reviews) && is_object($reviews))
                @foreach ($reviews as $review)
                    @php
                        $index = $index + 1;
                    @endphp
                    <tr>
                        <td>{{ $index }}</td>
                        <td>{{ $review->fullname }}</td>
                        <td>{{ $review->phone }}</td>
                        <td>{{ $review->description }}</td>
                        <td class="text-center">
                            <div class="text-navy">{{ $review->score }}</div>
                        </td>
                        <td>
                            <a href="{{ write_url($review->product_canonical) }}" target="_blank">Xem bình luận</a>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-outline reply-btn" data-toggle="modal"
                                data-target="#replyReviewModal-{{ $review->id }}"
                                data-review-id="{{ $review->id }}"><i class="fa fa-reply"></i> Phản hồi</button>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteReviewModal-{{ $review->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>

                    </tr>

                    <!-- Modal Xóa Đánh Giá -->
                    <div class="modal fade" id="deleteReviewModal-{{ $review->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteReviewModalLabel-{{ $review->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('review.destroy', $review->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteReviewModalLabel-{{ $review->id }}">Xác
                                            nhận xóa đánh giá</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa đánh giá của khách hàng
                                            <strong>{{ $review->fullname }}</strong> không?
                                        </p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Nội dung đánh
                                                giá</label>
                                            <input type="text" class="form-control"
                                                value="{{ $review->description }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Số sao</label>
                                            <input type="text" class="form-control" value="{{ $review->score }} sao"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    </div>
                                </div>



                            </form>
                        </div>
                    </div>

                    <!-- Modal Phản hồi Đánh Giá -->
                    <div class="modal fade" id="replyReviewModal-{{ $review->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="replyReviewModalLabel-{{ $review->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="ibox-title">
                                    <h5 class="modal-title" id="replyReviewModalLabel-{{ $review->id }}">Phản hồi
                                        đánh giá của khách hàng
                                        <strong>{{ $review->fullname }}</strong>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="replyReviewForm-{{ $review->id }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="replyText-{{ $review->id }}" class="control-label">Nội dung
                                                phản hồi</label>
                                            <textarea class="form-control" id="replyText-{{ $review->id }}" rows="4"
                                                placeholder="Nhập nội dung phản hồi...">{{ $review->reply_text ?? 'Cảm ơn bạn đã để lại đánh giá. Chúng tôi rất trân trọng ý kiến của bạn và sẽ xem xét cẩn thận. Nếu bạn có bất kỳ câu hỏi nào hoặc cần hỗ trợ thêm, đừng ngần ngại liên hệ với chúng tôi. Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất.' }}</textarea>
                                        </div>
                                        <input type="hidden" id="review_id-{{ $review->id }}"
                                            value="{{ $review->id }}">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary"
                                        id="submitReplyBtn-{{ $review->id }}">Gửi phản hồi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $reviews->links('pagination::bootstrap-4') }}
</div>
<script>
    $(document).ready(function() {
        $('[id^=submitReplyBtn-]').on('click', function() {
            var reviewId = $(this).attr('id').split('-')[1];
            var replyText = $('#replyText-' + reviewId).val().trim();

            if (replyText === '') {
                replyText =
                    "Cảm ơn bạn đã để lại đánh giá. Chúng tôi rất trân trọng ý kiến của bạn và sẽ xem xét cẩn thận. Nếu bạn có bất kỳ câu hỏi nào hoặc cần hỗ trợ thêm, đừng ngần ngại liên hệ với chúng tôi. Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất."; // Nội dung mẫu dài
            }

            $.ajax({
                url: '/ajax/review/reply',
                type: 'POST',
                data: {
                    review_id: reviewId,
                    reply_text: replyText,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Phản hồi đã được gửi!');
                        $('#replyReviewModal-' + reviewId).modal('hide');
                        $('#replyText-' + reviewId).val('');
                    } else {
                        toastr.error('Đã có lỗi xảy ra. Vui lòng thử lại!');
                    }
                },
                error: function() {
                    toastr.error('Lỗi kết nối. Vui lòng thử lại sau.');
                }
            });
        });
    });
</script>
