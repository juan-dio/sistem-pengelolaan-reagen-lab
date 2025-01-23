<div class="modal fade" role="dialog" id="modal_edit_order">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form enctype="multipart/form-data">
        <input type="hidden" id="order_id">
        <div class="modal-body">
          <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Pilih Status</label>
                  <select class="js-example-basic-single" name="status" id="status" style="width: 100%">
                    <option selected value="proses kirim">Proses Kirim</option>
                    <option selected value="dikirim">Dikirim</option>
                    <option selected value="terkirim">Terkirim</option>
                    <option selected value="approved">Approved</option>
                    <option selected value="batal">Batal</option>
                  </select>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-status"></div>
                </div>
              </div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
        <button type="button" class="btn btn-primary" id="update">Ubah</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>





