<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Unit <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Unit</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-10">
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
                                <th>Nomor Kantor</th>
                                <th width="150"></th>
                            </tr>
                            <?php $i = 0; foreach ($unit as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->nama_unit ?></td>
                                <td><?= $row->alamat_kantor ?></td>
                                <td>
                                    <button class="btn btn-info" onclick="get_unit(<?= $row->id_unit ?>)" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_unit(<?= $row->id_unit ?>)"><i class="fa fa-trash-o"></i></button>
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
        <div class="modal-content">
        <?= form_open('admin/unit') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Unit</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="nama_unit" required>
                </div>
                <div class="form-group">
                    <label for="alamat_kantor">Alamat Kantor *</label>
                    <input type="text" class="form-control" name="alamat_kantor" required>
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
        <?= form_open('admin/unit') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Bahan Baku</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="edit_nama" id="edit_nama" required>
                </div>
                <input type="hidden" name="edit_id_unit" id="edit_id_unit">
                <div class="form-group">
                    <label for="Nomor Kantor">Alamat Kantor *</label>
                    <input type="text" class="form-control" name="edit_kantor" id="edit_kantor" required>
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
        function delete_unit(id_unit) {
            $.ajax({
                url: '<?= base_url('admin/unit') ?>',
                type: 'POST',
                data: {
                    id_unit: id_unit,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/unit') ?>';
                }
            });
        }

        function get_unit(id_unit) {
            $.ajax({
                url: '<?= base_url('admin/unit') ?>',
                type: 'POST',
                data: {
                    id_unit: id_unit,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    console.log(response);
                    $('#edit_id_unit').val(response.id_unit);
                    $('#edit_nama').val(response.nama_unit);
                    $('#edit_kantor').val(response.alamat_kantor);
                }
            });   
        }
    </script>