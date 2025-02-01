<div class="modal fade" role="dialog" id="modal_edit_outstanding">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Outstanding</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="barang_masuk_id">

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Outstanding</label>
                <input type="number" class="form-control" name="edit_outstanding" id="edit_outstanding" readonly>
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_outstanding"></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>In Transit</label>
                <input type="number" class="form-control" name="intransit" id="intransit">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-intransit"></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Received</label>
                <input type="number" class="form-control" name="received" id="received">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-received"></div>
              </div>
            </div>
          </div>

        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
        <button type="button" class="btn btn-primary" id="update">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>





