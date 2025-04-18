<!-- Modal Delete -->
<div class="modal fade" id="deleteModal-{{ $stock->id }}" tabindex="-1" role="dialog"
    aria-labelledby="deleteModalLabel-{{ $stock->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('stock.stock-taking.destroy', $stock->id) }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-content">
                <div class="ibox-title">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa phiếu kiểm kê <strong>{{ $stock->code }}</strong> không?</p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </div>
        </form>
    </div>
</div>
