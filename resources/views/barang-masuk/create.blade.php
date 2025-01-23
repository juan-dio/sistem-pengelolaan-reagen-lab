<div class="modal fade" role="dialog" id="modal_tambah_barangMasuk">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang Masuk</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form enctype="multipart/form-data">
          <div class="modal-body">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Transaksi</label>
                  <input type="text" class="form-control" name="kode_transaksi" id="kode_transaksi" readonly>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-kode_transaksi"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Lot</label>
                  <input type="text" class="form-control" name="lot" id="lot">
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-lot"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Masuk (yyyy-mm-dd)</label>
                    <input type="text" class="form-control" name="tanggal_masuk" id="tanggal_masuk">
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal_masuk"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Expired (yyyy-mm-dd)</label>
                    <input type="text" class="form-control" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa">
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal_kadaluarsa"></div>
                  </div>
                </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Pilih Barang</label>
                  <select class="js-example-basic-single" name="order_id" id="order_id" style="width: 100%">
                    <option selected>Pilih Barang</option>
                    @foreach ($orders as $order)
                      <option value="{{ $order->id }}">{{ $order->barang->nama_barang }} {{ $order->kode_transaksi }}</option>
                    @endforeach
                  </select>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-order_id"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Supplier</label>
                  <select class="js-example-basic-single" name="supplier_id" id="supplier_id" style="width: 100%">
                    <option selected>Pilih Supplier</option>
                    @foreach ($suppliers as $supplier)
                      <option value="{{ $supplier->id }}">{{ $supplier->supplier}}</option>
                    @endforeach
                  </select>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-supplier_id"></div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Stok Saat Ini</label>
                  <input type="number" class="form-control" name="stok" id="stok" disabled>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-stok"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah Masuk</label>
                  <div class="input-group">
                    <input type="number" class="form-control" name="jumlah_masuk" id="jumlah_masuk" min="0" style="width: 75%;">
                    <div class="input-group-append" style="width: 25%;">
                      <input type="text" class="form-control" name="satuan" id="satuan_id" disabled>
                    </div>
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-jumlah_masuk" style="width: 100%;"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label>Lokasi</label>
                  <textarea class="form-control" name="lokasi" id="lokasi"></textarea>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-lokasi"></div>
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





