<div class="modal fade" role="dialog" id="modal_print_one">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cetak Barcode 1 Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/print-barcode/print-one" method="POST" enctype="multipart/form-data" id="form-print-one">
        @csrf
        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Pilih Item</label>
                <select class="js-example-basic-single" name="barang_id" id="barang_id" style="width: 100%">
                  <option selected>Pilih Item</option>
                  @foreach ($barangs as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama_barang}}</option>
                  @endforeach
                </select>
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-one-barang_id"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Jumlah</label>
                <input type="number" class="form-control" name="jumlah" id="jumlah" placeholder="Jumlah" value="1" min="1">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-one-jumlah"></div>
              </div>
            </div>
          </div> 
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="print-one">Print</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>





