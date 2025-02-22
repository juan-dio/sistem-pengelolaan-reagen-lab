<div class="modal fade" role="dialog" id="modal_tambah_barangKeluar">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang Keluar</h5>
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
                  <label>Tanggal Keluar</label>
                  <input type="text" class="form-control" name="tanggal_keluar" id="tanggal_keluar">
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal_keluar"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Pilih Alat</label>
                  <select class="js-example-basic-single" name="alat_id" id="alat_id" style="width: 100%">
                    <option selected>Pilih Alat</option>
                    @foreach ($alats as $alat)
                      <option value="{{ $alat->id }}">{{ $alat->alat}}</option>
                    @endforeach
                  </select>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-alat_id"></div>
                </div>
              </div>
            </div>

            <div class="row">
              {{-- <div class="col-md-6">
                <div class="form-group">
                  <label>Pilih Barang</label>
                    <select class="js-example-basic-single" name="barang_id" id="barang_id" style="width: 100%">
                      <option selected>Pilih Barang</option>
                      @foreach ($barangs as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                      @endforeach
                    </select>
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-barang_id"></div>
                </div>
              </div> --}}
              <div class="col-md-6">
                <div class="form-group">
                  <label>Scan Barcode/Pilih Kode Barang</label>
                  {{-- <input type="text" class="form-control" name="kode_barang" id="kode_barang" autofocus> --}}
                  <select class="js-example-basic-single" name="kode_barang" id="kode_barang" style="width: 100%">
                    {{-- <option selected value="">Klik untuk scan barcode/ketikkan kode barang</option> --}}
                    <option selected value="">Scan Barcode/Pilih Kode Barang</option>
                    @foreach ($barangs as $barang)
                      <option value="{{ $barang->kode_barang }}">{{ $barang->kode_barang }}</option>
                    @endforeach
                  </select>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-kode_barang"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Barang</label>
                  <input type="text" class="form-control" name="nama_barang" id="nama_barang" readonly>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-nama_barang"></div>
                  <input type="hidden" name="barang_id" id="barang_id">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Stok Saat Ini</label>
                  <input type="number" class="form-control" name="stok" id="stok" style="width: 75%;" disabled>
                  <div class="input-group-append" style="width: 25%;">
                    <input type="text" class="satuan form-control" name="satuan" disabled>
                  </div>
                  <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-stok"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jumlah Keluar</label>
                  <div class="input-group">
                    <input type="number" class="form-control" name="jumlah_keluar" id="jumlah_keluar" min="0" style="width: 75%;">
                    <div class="input-group-append" style="width: 25%;">
                      <input type="text" class="satuan form-control" name="satuan" disabled>
                    </div>
                    <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-jumlah_keluar" style="width: 100%;"></div>
                  </div>
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





