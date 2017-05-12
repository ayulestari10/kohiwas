<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Anggota <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Anggota</li>
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
                                <th>NLP</th>
                                <th>Nama</th>
                                <th>Tanggal Mendaftar</th>
                                <th>Alamat</th>
                                <th>Simpanan Pokok</th>
                                <th></th>
                            </tr>
                            <?php $i = 0; foreach ($anggota as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->nlp ?></td>
                                <td><?= $row->nama ?></td>
                                <td><?= $row->tgl_mendaftar ?></td>
                                <td><?= $row->alamat ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_pokok,2,',','.') ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick="get_anggota(<?= $row->id_anggota ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_anggota(<?= $row->id_anggota ?>)"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="add">
      <div class="modal-dialog" role="document">
        <?= form_open('admin') ?>
       <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Anggota</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="NLP">NLP *</label>
                    <input type="text" class="form-control" name="nlp" required>
                </div>
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Mendaftar">Tanggal Mendaftar *</label>
                    <input type="text" class="form-control" name="tgl_mendaftar" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="alamat" required></textarea>
                </div>
                <div class="form-group">
                    <label for="Simpanan Pokok">Simpanan Pokok *</label>
                    <input type="number" class="form-control" name="simpanan_pokok" required>
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
        <?= form_open('admin') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Anggota</h4>
          </div>
          <div class="modal-body">
                <input type="hidden" name="edit_id_anggota" id="edit_id_anggota">
                <div class="form-group">
                    <label for="NLP">NLP *</label>
                    <input type="text" class="form-control" name="edit_nlp" id="edit_nlp" required>
                </div>
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="edit_nama" id="edit_nama" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Mendaftar">Tanggal Mendaftar *</label>
                    <input type="text" class="form-control" name="edit_tgl_mendaftar" id="edit_tgl_mendaftar" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="edit_alamat" id="edit_alamat" required></textarea>
                </div>
                <div class="form-group">
                    <label for="Simpanan Pokok">Simpanan Pokok *</label>
                    <input type="text" class="form-control" name="edit_simpanan_pokok" id="edit_simpanan_pokok" required>
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

    <script type="text/javascript">
        function get_anggota(id_anggota) {
            $.ajax({
                url: '<?= base_url('admin') ?>',
                type: 'POST',
                data: {
                    id_anggota: id_anggota,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#edit_id_anggota').val(response.id_anggota);
                    $('#edit_nlp').val(response.nlp);
                    $('#edit_nama').val(response.nama);
                    $('#edit_tgl_mendaftar').val(response.tgl_mendaftar);
                    $('#edit_alamat').val(response.alamat);
                    $('#edit_simpanan_pokok').val(response.simpanan_pokok);
                }
            });
        }

        function delete_anggota(id_anggota) {
            $.ajax({
                url: '<?= base_url('admin') ?>',
                type: 'POST',
                data: {
                    id_anggota: id_anggota,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin') ?>';
                }
            });   
        }
    </script>