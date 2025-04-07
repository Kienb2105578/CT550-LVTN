<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>Tên nhóm</th>
                <th>Từ khóa</th>
                <th>Danh sách Hình Ảnh</th>
                <th class="text-center">Tình Trạng</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($slides) && is_object(value: $slides))
                @foreach ($slides as $slide)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $slide->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>{{ $slide->name }}</td>
                        <td>{{ $slide->keyword }}</td>
                        <td>
                            <div class="sortui ui-sortable table-slide clearfix">
                                @foreach ($slide->item[$config['language']] as $item)
                                    <li class="ui-state-default">
                                        <span class="image img-cover"><img src="{{ image($item['image']) }}"
                                                alt=""></span>
                                        <div class="hidden">
                                            <input type="text" name="slide[id][]" value="{{ $slide->id }}">
                                            <input type="text" name="slide[name][]" value="{{ $item['name'] }}">
                                            <input type="text" name="slide[image][]" value="{{ $item['image'] }}">
                                            <input type="text" name="slide[alt][]" value="{{ $item['alt'] }}">
                                            <input type="text" name="slide[description][]"
                                                value="{{ $item['description'] }}">
                                            <input type="text" name="slide[canonical][]"
                                                value="{{ $item['canonical'] }}">
                                            <input type="text" name="slide[window][]" value="{{ $item['window'] }}">
                                        </div>
                                    </li>
                                @endforeach
                            </div>
                        </td>

                        <td class="text-center js-switch-{{ $slide->id }}">
                            <input type="checkbox" value="{{ $slide->publish }}" class="js-switch status "
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $slide->publish == 2 ? 'checked' : '' }} data-modelId="{{ $slide->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('slide.edit', $slide->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            <!-- Nút Xóa mở Modal -->
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deleteSlideModal-{{ $slide->id }}"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Slide -->
                    <div class="modal fade" id="deleteSlideModal-{{ $slide->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deleteSlideModalLabel-{{ $slide->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('slide.destroy', $slide->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deleteSlideModalLabel-{{ $slide->id }}">Xác nhận
                                            xóa Slide</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa Slide có tên là
                                            <strong>{{ $slide->name }}</strong> không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label for="" class="control-label text-left">Tên Slide</label>
                                            <input type="text" class="form-control" value="{{ $slide->name }}"
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
    {{ $slides->links('pagination::bootstrap-4') }}
</div>
