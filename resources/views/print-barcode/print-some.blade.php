<div class="modal fade" role="dialog" id="modal_print_some">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cetak Barcode Beberapa Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/print-barcode/print-one" method="POST" enctype="multipart/form-data" id="form-print-one">
        @csrf
        <div class="modal-body">

          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label>Pilih Item</label>
                <select class="js-example-basic-single" name="some_barang_id" id="some_barang_id" style="width: 100%">
                  <option selected>Pilih Item</option>
                  @foreach ($barangs as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama_barang}}</option>
                  @endforeach
                </select>
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-some_barang_id"></div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label>Jumlah</label>
                <input type="number" class="form-control" name="some_jumlah" id="some_jumlah" placeholder="Jumlah" value="1" min="1">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-some_jumlah"></div>
              </div>
            </div>
            <div class="col-md-2 mt-4">
              <div class="form-group">
                <button type="button" class="btn btn-lg btn-primary" id="add-item">Tambah</button>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <table class="table table-bordered" >
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Item</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="table-item">
                  <tr>
                    <td colspan="4" class="text-center">Belum ada item yang dipilih</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="print-some">Print</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>





