<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard Admin
            <small><?=$title?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Data Operator</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No Pegawai</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>No. Telepon</th>
                                    <th>Unit</th>
                                    <th>Login Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($operator as $row): ?>
                              <?php $unit = $this->unit_m->get_row(['id_unit'=> $row->id_unit]); ?>
                                <tr>
                                  <td><?= $no ?></td>
                                  <td><?= $row->no_pegawai ?></td>
                                  <td><?= $row->username ?></td>
                                  <td><?= $row->nama ?></td>
                                  <td><?= $row->no_telp ?></td>
                                  <td><?= $this->unit_m->get_row(['id_unit'=> $row->id_unit])->nama_unit ?></td>
                                  <td>coming soon</td>
                                  <td>
                                      <button class="btn btn-primary" data-toggle="modal" data-target="#change-password"><i class="fa fa-edit"></i></button>
                                      <button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                  </td>
                                </tr>
                                <?php $no++ ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#add-new"><i class="fa fa-edit"></i> Tambah User</button>
                    </div>
                </div>
            </div>
      </div>

      <div class="modal fade" id="add-new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= form_open('admin/daftar_operator') ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add New Operator</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                      <label for="nopeg">No. Pegawai</label>
                      <input required type="text" name="nopeg" class="form-control">
                  </div>
                  <div class="form-group">
                      <label for="nama">Nama Operator</label>
                      <input required type="text" name="nama" class="form-control">
                  </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input required type="text" name="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input required type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <?php
                            $unit = [];
                            foreach ($list_unit as $row)
                                $unit[$row->id_unit] = $row->nama_unit;
                            echo form_dropdown('unit', $unit, '', ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="add" value="Add" class="btn btn-primary">
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
    </section>
<script type="text/javascript">
function deleteData(id,nama) {
      swal({
        title: "Apakah anda ingin menghapus operator ini?",
        text: nama,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: true,
        closeOnCancel: true
      },
      function(isConfirm){
        if (isConfirm) {
          $.ajax({
              url: '<?= base_url('admin/daftar_operator/') ?>',
              type: 'POST',
              data: {
                  delete: true,
                  email: id
              },
              success: function(data) {
                  // alert(data);
                  window.location = '<?= base_url('admin/daftar_operator/') ?>';
              }
          });
        }
      });
}
</script>
