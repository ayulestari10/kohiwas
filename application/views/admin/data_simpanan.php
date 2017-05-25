<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Simpanan <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">

            <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Simpanan</li>
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
                                <th>Nama Anggota</th>
                                <th>Tanggal Simpanan</th>
                                <th>Simpanan Wajib</th>
                                <th>Simpanan Sukarela</th>
                                <th></th>
                            </tr>
                            <?php $i = 0; foreach($simpanan as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->anggota_m->get_row(['id_anggota' => $row->id_anggota])->nama ?></td>
                                <td><?= $row->tgl_simpanan ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_wajib,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_sukarela,2,',','.') ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick="get_simpanan(<?= $row->id_simpanan ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_simpanan(<?= $row->id_simpanan ?>)"><i class="fa fa-trash-o"></i></button>
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
        <?= form_open('admin/data_simpanan') ?>
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Simpanan</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama Anggota">Nama Anggota *</label>
                   <select class="form-control" name="id_anggota">
                        <?php foreach ($anggota as $row): ?>
                            <option value="<?= $row->id_anggota ?>"><?= $row->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Tanggal Simpanan">Tanggal Simpanan *</label>
                    <!-- <input type="text" class="form-control" name="tgl_simpanan" required> -->
                    <div class="input-group date">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input type="text" name="tgl_simpanan" id="tgl_awal" class="form-control" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Simpanan Wajib">Simpanan Wajib *</label>
                    <input type="text" class="form-control" name="simpanan_wajib" required>
                </div>
                <div class="form-group">
                    <label for="Simpanan Sukarela">Simpanan Sukarela *</label>
                    <input type="text" class="form-control" name="simpanan_sukarela" required>
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
        <?= form_open('admin/data_simpanan') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Simpanan</h4>
          </div>
          <div class="modal-body">
                <input type="hidden" name="edit_id_simpanan" id="edit_id_simpanan">
                <div class="form-group">
                    <label for="Nama Anggota">Nama Anggota *</label>
                    <div id="edit_nama_anggota"></div>
                </div>
                <div class="form-group">
                    <label for="Tanggal Simpanan">Tanggal Simpanan *</label>
                    <!-- <input type="text" class="form-control" name="edit_tgl_simpanan" id="edit_tgl_simpanan" required> -->
                    <div class="input-group date">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input type="text" name="edit_tgl_simpanan" id="edit_tgl_simpanan" class="form-control" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Simpanan Wajib">Simpanan Wajib *</label>
                    <input type="text" class="form-control" name="edit_simpanan_wajib" id="edit_simpanan_wajib" required>
                </div>
                <div class="form-group">
                    <label for="Simpanan Sukarela">Simpanan Sukarela *</label>
                    <input type="text" class="form-control" name="edit_simpanan_sukarela" id="edit_simpanan_sukarela" required>
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
         $(document).ready(function() {
            $('.input-group.date').datepicker({format: "yyyy-mm-dd"});
        });

        function get_simpanan(id_simpanan) {
            $.ajax({
                url: '<?= base_url('admin/data_simpanan') ?>',
                type: 'POST',
                data: {
                    id_simpanan: id_simpanan,
                    get: true
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    $('#edit_id_simpanan').val(response.id_simpanan);
                    $('#edit_nama_anggota').html(response.dropdown);
                    $('#edit_tgl_simpanan').val(response.tgl_simpanan);
                    $('#edit_simpanan_wajib').val(response.simpanan_wajib);
                    $('#edit_simpanan_sukarela').val(response.simpanan_sukarela);
                }
            });
        }

        function delete_simpanan(id_simpanan) {
            $.ajax({
                url: '<?= base_url('admin/data_simpanan') ?>',
                type: 'POST',
                data: {
                    id_simpanan: id_simpanan,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/data_simpanan') ?>';
                }
            });   
        }
    </script>
