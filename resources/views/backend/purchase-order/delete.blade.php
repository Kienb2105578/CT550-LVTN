@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
<form action="{{ route('purchase-order.destroy', $purchaseOrder->id) }}" method="post" class="box">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mã phiếu nhập
                                        <span class="text-danger">(*)</span></label>
                                    <input type="text" name="code"
                                        value="{{ old('code', $purchaseOrder->code ?? '') }}" class="form-control"
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
