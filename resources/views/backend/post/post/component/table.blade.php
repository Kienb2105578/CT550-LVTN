<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:50px;">
                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                </th>
                <th>{{ __('messages.tableName') }}</th>

                <th class="text-center" style="width:100px;">{{ __('messages.tableStatus') }}</th>
                <th class="text-center" style="width:100px;">{{ __('messages.tableAction') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($posts) && is_object($posts))
                @foreach ($posts as $post)
                    <tr id="{{ $post->id }}">
                        <td>
                            <input type="checkbox" value="{{ $post->id }}" class="input-checkbox checkBoxItem">
                        </td>
                        <td>
                            <div class="uk-flex uk-flex-middle">
                                <div class="image mr5">
                                    <div class="img-cover image-post"><img src="{{ image($post->image) }}"
                                            alt=""></div>
                                </div>
                                <div class="main-info">
                                    <div class="name"><span class="maintitle">{{ $post->name }}</span></div>
                                    <div class="catalogue">
                                        <span class="text-danger">{{ __('messages.tableGroup') }} </span>
                                        @foreach ($post->array_post_catalogue_name as $catalogue)
                                            <a href="{{ route('post.index', ['post_catalogue_id' => $catalogue['id']]) }}"
                                                title="">
                                                {{ $catalogue['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="text-center js-switch-{{ $post->id }}">
                            <input type="checkbox" value="{{ $post->publish }}" class="js-switch status"
                                data-field="publish" data-model="{{ $config['model'] }}"
                                {{ $post->publish == 2 ? 'checked' : '' }} data-modelId="{{ $post->id }}" />
                        </td>
                        <td class="text-center">
                            <a href="{{ route('post.edit', $post->id) }}" class="btn btn-info btn-outline"><i
                                    class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-danger btn-outline" data-toggle="modal"
                                data-target="#deletePostModal-{{ $post->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Xóa Bài Viết -->
                    <div class="modal fade" id="deletePostModal-{{ $post->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deletePostModalLabel-{{ $post->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('post.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="ibox-title">
                                        <h5 class="modal-title" id="deletePostModalLabel-{{ $post->id }}">Xác nhận
                                            xóa bài viết</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa bài viết <strong>{{ $post->name }}</strong>
                                            không?</p>
                                        <p><span class="text-danger">Lưu ý:</span> Thao tác này không thể hoàn tác.</p>
                                        <div class="form-group">
                                            <label>Tên bài viết</label>
                                            <input type="text" class="form-control" value="{{ $post->name }}"
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
    {{ $posts->links('pagination::bootstrap-4') }}
</div>
