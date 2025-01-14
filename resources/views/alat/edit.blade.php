<div class="modal fade" tabindex="-1" role="dialog" id="modal_edit_alat">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Alat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form enctype="multipart/form-data">
          <div class="modal-body">

            <input type="hidden" id="alat_id">
            <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" class="form-control" name="alat" id="edit_alat">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_alat"></div>
            </div>

        </div>
        <div class="modal-footer bg-whitesmoke br">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="update">Tambah</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>



