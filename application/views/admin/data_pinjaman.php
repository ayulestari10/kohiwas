<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Pinjaman <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">

            <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Pinjaman</li>
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
                                <th>Tanggal Pinjaman</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Bunga</th>
                                <th>TTL Pinjaman</th>
                                <th>Lama Pinjaman</th>
                                <th>Angsuran</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                           <?php $i = 0; foreach ($pinjaman as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->anggota_m->get_row(['id_anggota' => $row->id_anggota])->nama ?></td>
                                <td><?= $row->tgl_pinjaman ?></td>
                                <td><?= "Rp " . number_format($row->jlh_pinjaman,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->bunga,2,',','.') ?></td>
                                <td><?= $row->ttl_pinjaman ?></td>
                                <td><?= $row->lama_pinjaman ?></td>
                                <td><?= $row->angsuran ?></td>
                                <td><?= $row->status ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick="get_pinjaman(<?= $row->id_pinjaman ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_pinjaman(<?= $row->id_pinjaman ?>)"><i class="fa fa-trash-o"></i></button>
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
        <?= form_open('admin/data_pinjaman') ?>
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Pinjaman</h4>
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
                    <label for="Tanggal Pinjaman">Tanggal Pinjaman *</label>
                    <!-- <input type="text" class="form-control" name="tgl_pinjaman" required> -->
                    <div class="input-group date">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input type="text" name="tgl_pinjaman" id="tgl_awal" class="form-control" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Jumlah Pinjaman">Jumlah Pinjaman *</label>
                    <input type="text" class="form-control" name="jlh_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Bunga">Bunga *</label>
                    <input type="text" class="form-control" name="bunga" required>
                </div>
                <div class="form-group">
                    <label for="TTL Pinjaman">TTL Pinjaman *</label>
                    <input type="text" class="form-control" name="ttl_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Lama Pinjaman">Lama Pinjaman *</label>
                    <input type="text" class="form-control" name="lama_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Angsuran">Angsuran *</label>
                    <input type="text" class="form-control" name="angsuran" required>
                </div>
                <div class="form-group">
                    <label for="Status">Status *</label>
                    <input type="text" class="form-control" name="status" required>
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
        <?= form_open('admin/data_pinjaman') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Pinjaman</h4>
          </div>
          <div class="modal-body">
                <input type="hidden" name="edit_id_pinjaman" id="edit_id_pinjaman">
                <div class="form-group">
                    <label for="Nama Anggota">Nama Anggota *</label>
                    <div id="edit_id_anggota"></div>
                </div>
                <div class="form-group">
                    <label for="Tanggal Pinjaman">Tanggal Pinjaman *</label>
                    <!-- <input type="text" class="form-control" name="edit_tgl_pinjaman" id="edit_tgl_pinjaman" required> -->
                    <div class="input-group date">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input type="text" name="edit_tgl_pinjaman" id="edit_tgl_pinjaman" class="form-control" placeholder="YYYY-MM-DD" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Jumlah Pinjaman">Jumlah Pinjaman *</label>
                    <input type="text" class="form-control" name="edit_jlh_pinjaman" id="edit_jlh_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Bunga">Bunga *</label>
                    <input type="text" class="form-control" name="edit_bunga" id="edit_bunga" required>
                </div>
                <div class="form-group">
                    <label for="TTL Pinjaman">TTL Pinjaman *</label>
                    <input type="text" class="form-control" name="edit_ttl_pinjaman" id="edit_ttl_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Lama Pinjaman">Lama Pinjaman *</label>
                    <input type="text" class="form-control" name="edit_lama_pinjaman" id="edit_lama_pinjaman" required>
                </div>
                <div class="form-group">
                    <label for="Angsuran">Angsuran *</label>
                    <input type="text" class="form-control" name="edit_angsuran" id="edit_angsuran" required>
                </div>
                <div class="form-group">
                    <label for="Status">Status *</label>
                    <input type="text" class="form-control" name="edit_status" id="edit_status" required>
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

        function get_pinjaman(id_pinjaman) {
            $.ajax({
                url: '<?= base_url('admin/data_pinjaman') ?>',
                type: 'POST',
                data: {
                    id_pinjaman: id_pinjaman,
                    get: true
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    $('#edit_id_pinjaman').val(response.id_pinjaman);
                    $('#edit_id_anggota').html(response.dropdown);
                    $('#edit_tgl_pinjaman').val(response.tgl_pinjaman);
                    $('#edit_jlh_pinjaman').val(response.jlh_pinjaman);
                    $('#edit_bunga').val(response.bunga);
                    $('#edit_ttl_pinjaman').val(response.ttl_pinjaman);
                    $('#edit_lama_pinjaman').val(response.lama_pinjaman);
                    $('#edit_angsuran').val(response.angsuran);
                    $('#edit_status').val(response.status);
                }
            });
        }

        function delete_pinjaman(id_pinjaman) {
            $.ajax({
                url: '<?= base_url('admin/data_pinjaman') ?>',
                type: 'POST',
                data: {
                    id_pinjaman: id_pinjaman,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/data_pinjaman') ?>';
                }
            });   
        }
    </script>
