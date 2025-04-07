@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{ route('attribute.catalogue.destroy', $attributeCatalogue->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Loại thuộc tính bạn đang muốn xóa là: <span class="text-danger">
                                {{ $attributeCatalogue->name }}</span></p>
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
                                    <label for="" class="control-label text-left">Tên loại thuộc tính
                                        <span class="text-danger">(*)</span></label>
                                    <input type="text" name="name"
                                        value="{{ old('name', $attributeCatalogue->name ?? '') }}" class="form-control"
                                        placeholder="" autocomplete="off" readonly>
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
