<div class="modal fade" tabindex="-1" role="dialog" id="modal_tambah_barang_excel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Reagen Excel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>
                  File Excel <br>
                  Import file excel dengan kolom kode_barang, nama_barang, stok_minimum, test_group, deskripsi, jenis (dingin, kering), satuan (mL, pcs)
                </label>
                <label>
                  <a href="/storage/excel/template.xlsx">Download Template Excel</a>
                  {{-- <a href="javascript:void(0)" id="button_template_excel">Download Template Excel</a> --}}
                </label>
                <input type="file" class="form-control" name="excel" id="excel">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-excel"></div>
              </div>
            </div>
          </div>

      </div>
      <div class="modal-footer bg-whitesmoke br">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
        <button type="button" class="btn btn-primary" id="import">Import</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>



