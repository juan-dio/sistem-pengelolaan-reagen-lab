<div class="modal fade" role="dialog" id="modal_tambah_order">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Order</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label>Kode Transaksi</label>
                  <input type="text" class="form-control" name="kode_transaksi" id="kode_transaksi" readonly>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-kode_transaksi"></div>
                </div>
              </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Order (yyyy-mm-dd)</label>
                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Pilih Item</label>
                    <select class="js-example-basic-single" name="barang_id" id="barang_id" style="width: 100%">
                      <option selected value="">Pilih Item</option>
                      @foreach ($barangs as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                      @endforeach
                    </select>
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-barang_id"></div>
                  </div>
                </div>
            </div>

          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="store">Tambah</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>





