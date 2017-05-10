<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Angsuran <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Angsuran</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <!-- <h3 class="box-title">Data Supplier</h3>   -->
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <?= $this->session->flashdata('msg') ?>
                        <table class="table table-striped">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Angsuran</th>
                                <th>Jumlah dibayar</th>
                                <th>Sisa Angsuran</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>08082016</td>
                                <td>Cantiks</td>
                                <td>nak tau nian</td>
                                <td>Hatimu</td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick=""><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick=""><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="add">
      <div class="modal-dialog" role="document">
        <?= form_open('admin/') ?>
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Angsuran</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Angsuran">Tanggal Angsuran *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="alamat" required></textarea>
                </div>
                <div class="form-group">
                    <label for="Jumlah dibayar">Jumlah dibayar *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Sisa Angsuran">Sisa Angsuran *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
          </div>
          <?= form_close() ?>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="edit">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?= form_open('admin/bahan_baku') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Angsuran</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Angsuran">Tanggal Angsuran *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="alamat" required></textarea>
                </div>
                <div class="form-group">
                    <label for="Jumlah dibayar">Jumlah dibayar *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
                <div class="form-group">
                    <label for="Sisa Angsuran">Sisa Angsuran *</label>
                    <input type="text" class="form-control" name="" required>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <input type="submit" name="edit" value="Edit" class="btn btn-primary">
          </div>
          <?= form_close() ?>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->