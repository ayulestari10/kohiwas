<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Supplier <button id="add" class="btn btn-success" data-toggle="modal" data-target="#addSupplier"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Supplier</li>
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
                                <th>Alamat</th>
                                <th width="150"></th>
                            </tr>
                            <?php $i = 0; foreach ($supplier as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->nama_suplier ?></td>
                                <td><?= $row->alamat ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#editSupplier" onclick="get_supplier(<?= $row->id_suplier ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_supplier(<?= $row->id_suplier ?>)"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="addSupplier">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?= form_open('admin/supplier') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Supplier</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="nama_suplier" required>
                </div>
                <div class="form-group">
                    <label for="Alamat">Alamat *</label>
                    <textarea class="form-control" rows="3" name="alamat" required></textarea>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="editSupplier">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?= form_open('admin/supplier') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Data Supplier</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama</label>
                    <input type="text" class="form-control" name="edit_nama" id="edit_nama" value="" required>
                </div>
                <input type="hidden" name="edit_id_suplier" id="edit_id_suplier">
                <div class="form-group">
                    <label for="Alamat">Alamat</label>
                    <textarea class="form-control" rows="3" name="edit_alamat" id="edit_alamat" required></textarea>
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
        function delete_supplier(id_supplier) {
            $.ajax({
                url: '<?= base_url('admin/supplier') ?>',
                type: 'POST',
                data: {
                    id_suplier: id_supplier,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/supplier') ?>';
                }
            });
        }

        function get_supplier(id_supplier) {
            $.ajax({
                url: '<?= base_url('admin/supplier') ?>',
                type: 'POST',
                data: {
                    id_suplier: id_supplier,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#edit_id_suplier').val(response.id_suplier);
                    $('#edit_nama').val(response.nama_suplier);
                    $('#edit_alamat').val(response.alamat);
                }
            });   
        }
    </script>