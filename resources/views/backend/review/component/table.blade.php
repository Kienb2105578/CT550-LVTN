<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Họ Tên</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th style="width: 400px;">Nội dung</th>
                <th>Rate</th>
                <th>Đối tượng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($reviews) && is_object($reviews))
                @foreach ($reviews as $review)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $review->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            {{ $review->fullname }}
                        </td>
                        <td>
                            {{ $review->phone }}
                        </td>
                        <td>
                            {{ $review->email }}
                        </td>
                        <td>
                            {{ $review->description }}
                        </td>
                        <td class="text-center">
                            <div class="text-navy">{{ $review->score }}</div>
                        </td>
                        <td>
                            <a href="{{ write_url($review->product_canonical) }}" target="_blank">Click để xem đối
                                tượng</a>
                        </td>
                        <td class="text-center">
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
                @endforeach

            @endif
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $reviews->links('pagination::bootstrap-4') }}
</div>
