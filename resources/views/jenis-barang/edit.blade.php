<div class="modal fade" tabindex="-1" role="dialog" id="modal_edit_jenis_barang">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Jenis Barang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form enctype="multipart/form-data">

        <div class="modal-body">
            <input type="hidden" id="jenis_id">
            <div class="form-group">
                <label>Nama Jenis Barang</label>
                <input type="text" class="form-control" name="jenis_barang" id="edit_jenis_barang">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_jenis_barang"></div>
            </div>
        </div>

        <div class="modal-footer bg-whitesmoke br">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="update">Edit</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>



