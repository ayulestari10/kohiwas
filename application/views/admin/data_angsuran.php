<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Angsuran <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
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
                                <th>ID Pinjaman</th>
                                <th>Tanggal Angsuran</th>
                                <th>Jumlah dibayar</th>
                                <th>Sisa Angsuran</th>
                                <th></th>
                            </tr>
                            <?php $i = 0; foreach ($angsuran as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->id_pinjaman ?></td>
                                <td><?= $row->tgl_angsuran ?></td>
                                <td><?= "Rp " . number_format($row->jlh_dibayar,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->sisa_angsuran,2,',','.') ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick="get_angsuran(<?= $row->id_angsuran ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_angsuran(<?= $row->id_angsuran ?>)"><i class="fa fa-trash-o"></i></button>
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
        <?= form_open('admin/data_angsuran') ?>
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Angsuran</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">ID Pinjaman *</label>
                    <select class="form-control" name="id_pinjaman">
                        <?php foreach ($pinjaman as $row): ?>
                            <option value="<?= $row->id_pinjaman ?>"><?= $row->id_pinjaman ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Tanggal Angsuran">Tanggal Angsuran *</label>
                    <input type="text" class="form-control" name="tgl_angsuran" required>
                </div>
                <div class="form-group">
                    <label for="Jumlah dibayar">Jumlah dibayar *</label>
                    <input type="text" class="form-control" name="jlh_dibayar" required>
                </div>
                <div class="form-group">
                    <label for="Sisa Angsuran">Sisa Angsuran *</label>
                    <input type="text" class="form-control" name="sisa_angsuran" required>
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
        <?= form_open('admin/data_angsuran') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Angsuran</h4>
          </div>
          <div class="modal-body">
                <input type="hidden" name="edit_id_angsuran" id="edit_id_angsuran">
                <div class="form-group">
                    <label for="Nama">ID Pinjaman *</label>
                    <div id="edit_id_pinjaman"></div>
                </div>
                <div class="form-group">
                    <label for="Tanggal Angsuran">Tanggal Angsuran *</label>
                    <input type="text" class="form-control" name="edit_tgl_angsuran" id="edit_tgl_angsuran" required>
                </div>
                <div class="form-group">
                    <label for="Jumlah dibayar">Jumlah dibayar *</label>
                    <input type="text" class="form-control" name="edit_jlh_dibayar" id="edit_jlh_dibayar" required>
                </div>
                <div class="form-group">
                    <label for="Sisa Angsuran">Sisa Angsuran *</label>
                    <input type="text" class="form-control" name="edit_sisa_angsuran" id="edit_sisa_angsuran" required>
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
        function get_angsuran(id_angsuran) {
            $.ajax({
                url: '<?= base_url('admin/data_angsuran') ?>',
                type: 'POST',
                data: {
                    id_angsuran: id_angsuran,
                    get: true
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    $('#edit_id_angsuran').val(response.id_angsuran);
                    $('#edit_id_pinjaman').html(response.dropdown);
                    $('#edit_tgl_angsuran').val(response.tgl_angsuran);
                    $('#edit_jlh_dibayar').val(response.jlh_dibayar);
                    $('#edit_sisa_angsuran').val(response.sisa_angsuran);
                }
            });
        }

        function delete_angsuran(id_angsuran) {
            $.ajax({
                url: '<?= base_url('admin/data_angsuran') ?>',
                type: 'POST',
                data: {
                    id_angsuran: id_angsuran,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/data_angsuran') ?>';
                }
            });   
        }
    </script>
