<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Bahan Baku <button class="btn btn-success" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Bahan Baku</li>
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
                                <th>Supplier</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Stok</th>
                                <th>Stok Minimum</th>
                                <th>Harga</th>
                                <th width="150"></th>
                            </tr>
                            <?php $i = 0; foreach ($bahan_baku as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->supplier_m->get_row(['id_suplier' => $row->id_suplier])->nama_suplier ?></td>
                                <td><?= $row->nama_bahan ?></td>
                                <td><?= $row->jenis_bahan ?></td>
                                <td><?= $row->stok ?></td>
                                <td><?= $row->stok_min ?></td>
                                <td><?= $row->satuan ?></td>
                                <td><?= $row->harga ?></td>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#edit" onclick="get_bahan_baku(<?= $row->id_bahan_baku ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_bahan_baku(<?= $row->id_bahan_baku ?>)"><i class="fa fa-trash-o"></i></button>
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
        <?= form_open('admin/bahan_baku') ?>
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Bahan Baku</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama Bahan *</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="supplier">Supplier *</label>
                    <?php  
                        $supp_dropdown = [];
                        foreach ($supplier as $row)
                            $supp_dropdown[$row->id_suplier] = $row->nama_suplier;
                    ?>
                    <?= form_dropdown('id_suplier', $supp_dropdown, '', ['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <label for="Jenis">Jenis *</label>
                    <input type="text" class="form-control" name="jenis" required>
                </div>
                <div class="form-group">
                    <label for="Jenis">Stok *</label>
                    <input type="text" class="form-control" name="stok" id="stok" required>
                </div>
                <div class="form-group">
                    <label for="Jenis">Stok Minimum *</label>
                    <input type="text" class="form-control" name="stok_min" id="stok_min" required>
                </div>
                <div class="form-group">
                    <label for="Satuan">Satuan *</label>
                    <input type="text" class="form-control" name="satuan" required>
                </div>
                <div class="form-group">
                    <label for="Harga">Harga *</label>
                    <input type="text" class="form-control" name="harga" required>
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
            <h4 class="modal-title">Edit Data Bahan Baku</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label for="Nama">Nama *</label>
                    <input type="text" class="form-control" name="edit_nama" id="edit_nama" required>
                </div>
                <input type="hidden" name="edit_id_bahan_baku" id="edit_id_bahan_baku">
                <div class="form-group">
                    <label for="supplier">Supplier *</label>
                    <div id="edit_supplier"></div>
                </div>
                <div class="form-group">
                    <label for="Jenis">Jenis *</label>
                    <input type="text" class="form-control" name="edit_jenis" id="edit_jenis" required>
                </div>
                <div class="form-group">
                    <label for="Jenis">Stok *</label>
                    <input type="text" class="form-control" name="edit_stok" id="edit_stok" required>
                </div>
                <div class="form-group">
                    <label for="Jenis">Stok Minimum *</label>
                    <input type="text" class="form-control" name="edit_stok_min" id="edit_stok_min" required>
                </div>
                <div class="form-group">
                    <label for="Satuan">Satuan *</label>
                    <input type="text" class="form-control" name="edit_satuan" id="edit_satuan" required>
                </div>
                <div class="form-group">
                    <label for="Harga">Harga *</label>
                    <input type="text" class="form-control" name="edit_harga" id="edit_harga" required>
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
        function delete_bahan_baku(id_bahan_baku) {
            $.ajax({
                url: '<?= base_url('admin/bahan_baku') ?>',
                type: 'POST',
                data: {
                    id_bahan_baku: id_bahan_baku,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('admin/bahan_baku') ?>';
                }
            });
        }

        function get_bahan_baku(id_bahan_baku) {
            $.ajax({
                url: '<?= base_url('admin/bahan_baku') ?>',
                type: 'POST',
                data: {
                    id_bahan_baku: id_bahan_baku,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    console.log(response);
                    $('#edit_id_bahan_baku').val(response.id_bahan_baku);
                    $('#edit_nama').val(response.nama_bahan);
                    $('#edit_jenis').val(response.jenis_bahan);
                    $('#edit_stok').val(response.stok);
                    $('#edit_stok_min').val(response.stok_min);
                    $('#edit_satuan').val(response.satuan);
                    $('#edit_harga').val(response.harga);
                    $('#edit_supplier').html(response.dropdown);
                }
            });   
        }
    </script>