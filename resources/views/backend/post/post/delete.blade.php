@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
<form action="{{ route('post.destroy', $post->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Tên bài viết bạn đang muốn xóa là: <span class="text-danger">{{ $post->name }}</span></p>
                        <p>{{ __('messages.generalDescription') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên bài viết <span
                                            class="text-danger">(*)</span></label>
                                    <input type="text" name="name" value="{{ old('name', $post->name ?? '') }}"
                                        class="form-control" placeholder="" autocomplete="off" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mb15">
            <button class="btn btn-danger" type="submit" name="send"
                value="send">{{ __('messages.deleteButton') }}</button>
        </div>
    </div>
</form>
