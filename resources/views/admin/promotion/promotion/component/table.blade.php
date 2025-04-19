<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên chương trình</th>
                <th>Chiết khấu</th>
                <th>Thông tin</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        @php
            $index = 0;
        @endphp
        <tbody>
            @if (isset($promotions) && is_object($promotions))
                @foreach ($promotions as $key => $promotion)
                    @php
                        $startDate = convertDateTime($promotion->startDate);
                        $endDate = convertDateTime($promotion->endDate);
                        $status = '';
                        if ($promotion->endDate != null && strtotime($promotion->endDate) - strtotime(now()) <= 0) {
                            $status = '<span class="text-danger text-small">- Hết Hạn</span>';
                        }
                    @endphp
                    @php
                        $index += 1;
                    @endphp
                    <tr>
                        <td>{{ $index }}</td>
                        <td>
                            <div>{{ $promotion->name }} {!! $status !!} </div>
                            <div class="text-small text-success">Mã: {{ $promotion->code }}</div>
                        </td>
                        <td>
                            <div class="discount-information text-center">
                                @if ($promotion->method === 'product_and_quantity')
                                    @php
                                        $discountValue = $promotion->discountInformation['info']['discountValue'] ?? 0;
                                        $discountType =
                                            ($promotion->discountInformation['info']['discountType'] ?? '') ===
                                            'percent'
                                                ? '%'
                                                : 'đ';
                                    @endphp
                                    <span class="label label-success">{{ $discountValue }}{{ $discountType }}</span>
                                @else
                                    <div>
                                        <a href="{{ route('promotion.edit', $promotion->id) }}">Xem chi tiết</a>
                                    </div>
                                @endif
                            </div>

                        </td>

                        <td>
                            <div>{{ __('module.promotion')[$promotion->method] }}</div>
                        </td>
                        <td>
                            {{ $startDate }}
                        </td>
                        <td>
                            {{ $promotion->neverEndDate === 'accept' ? 'Không giới hạn' : $endDate }}
                        </td>
                        <td class="text-center js-switch-{{ $promotion->id }}">
                            <input type="checkbox" value="{{ $promotion->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $promotion->publish == 2 ? 'checked' : '' }} data-modelId="{{ $promotion->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('promotion.edit', $promotion->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deletePromotionModal-{{ $promotion->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Khuyến Mãi -->
                    <div class="modal fade" id="deletePromotionModal-{{ $promotion->id }}" tabindex="-1"
                        role="dialog" aria-labelledby="deletePromotionModalLabel-{{ $promotion->id }}"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('promotion.destroy', $promotion->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deletePromotionModalLabel-{{ $promotion->id }}">Xác
                                            nhận xóa khuyến mãi</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa khuyến mãi <strong>{{ $promotion->name }}</strong>
                                            không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên khuyến mãi</label>
                                            <input type="text" class="form-control" value="{{ $promotion->name }}"
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
    {{ $promotions->links('pagination::bootstrap-4') }}
</div>
