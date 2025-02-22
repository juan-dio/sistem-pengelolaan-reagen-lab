<div class="modal fade" tabindex="-1" role="dialog" id="modal_edit_barang">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="barang_id">

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" class="form-control" name="nama_barang" id="edit_nama_barang">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_nama_barang"></div>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label>Kode Barang</label>
                <input type="text" class="form-control" name="kode_barang" id="edit_kode_barang">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_kode_barang"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>Jenis Barang</label>
                <select class="form-control" name="jenis_id" id="edit_jenis_id">
                  @foreach ($jenis_barangs as $jenis)
                    @if (old('jenis_id', $jenis->jenis_barang) == $jenis->id)
                      <option value="{{ $jenis->id }}" selected>{{ $jenis->jenis_barang }}</option>
                    @else
                      <option value="{{ $jenis->id }}">{{ $jenis->jenis_barang }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label>Satuan Barang</label>
                <select class="form-control" name="satuan_id" id="edit_satuan_id">
                  @foreach ($satuans as $satuan)
                    @if (old('satuan', $satuan->satuans) == $satuan->id)
                      <option value="{{ $satuan->id }}" selected>{{ $satuan->satuan }}</option>
                    @else
                      <option value="{{ $satuan->id }}">{{ $satuan->satuan }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>Stok Minimum</label>
                <input type="number" class="form-control" name="stok_minimum" id="edit_stok_minimum">
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_stok_minimum"></div>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label>Test Group</label>
                <select class="form-control" name="test_group" id="edit_test_group">
                  @foreach ($test_group as $key => $value)
                    @if (old('test_group') == $key)
                      <option value="{{ $key }}" selected>{{ $value }}</option>
                    @else
                      <option value="{{ $key }}">{{ $value }}</option>
                    @endif
                  @endforeach
                </select>
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_test_group"></div>
              </div>
            </div>
          </div>

        
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="edit_deskripsi"></textarea>
                <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-edit_deskripsi"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-whitesmoke br">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="button" class="btn btn-primary" id="update">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>
