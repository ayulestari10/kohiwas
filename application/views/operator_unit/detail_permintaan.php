<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Detail Permintaan<button id="add" class="btn btn-success" data-toggle="modal" data-target="#addDetail"><i class="fa fa-plus"></i></button>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Detail Permintaan</li>
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
                              <th>Bahan Baku</th>
                              <th>Satuan</th>
                              <th>Harga</th>
                              <th>Jumlah Permintaan</th>
                              <th>Keterangan</th>
                              <?php $approved = $this->permintaan_bahan_baku_m->get_row(['id_permintaan'=> $this->uri->segment(3)])->approved ?>
                              <?php if($approved != 1): ?>
                                <th>Aksi</th>
                              <?php endif ?>
                            </tr>
                            <?php $total = 0; $i = 0; foreach ($detail as $row): ?>
                              <?php $bahan = $this->bahan_baku_m->get_row(['id_bahan_baku'=>$row->id_bahan_baku]) ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?=$bahan->nama_bahan?></td>
                                <td><?=$bahan->satuan?></td>
                                <td><?=$bahan->harga ?></td>
                                <td><?= $row->jumlah_permintaan ?></td>
                                <?php $total = $total + ($bahan->harga * $row->jumlah_permintaan); ?>
                                <td><?=$row->keterangan?></td>
                                <?php $approved = $this->permintaan_bahan_baku_m->get_row(['id_permintaan'=> $this->uri->segment(3)])->approved ?>
                                <?php if($approved != 1): ?>
                                <td>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#editSupplier" onclick="get_permintaan(<?= $row->id_detail_permintaan ?>)"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger" onclick="delete_permintaan(<?= $row->id_detail_permintaan ?>)"><i class="fa fa-trash-o"></i></button>
                                </td>
                                <?php endif ?>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                              <td><strong>Total Harga</strong></td>
                              <td></td>
                              <td><strong>Rp. <?=$total?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="addDetail">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?= form_open('operator/detail_permintaan/') ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Tambah Data Permintaan Bahan</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="bahan_baku">Bahan *</label>
                <?php
                    $supp_dropdown = [];
                    foreach ($bahan as $key)
                        $supp_dropdown[$key->id_bahan_baku] = $key->nama_bahan;
                ?>
                <?= form_dropdown('id_bahan_baku', $supp_dropdown, '', ['class' => 'form-control']) ?>
            </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah Permintaan *</label>
                    <input type="number" class="form-control" name="jumlah_permintaan" required>
                </div>
                <input type="hidden" name="id_permintaan" value="<?=$this->uri->segment(3)?>">
                <div class="form-group">
                    <label for="Keterangan">Keterangan *</label>
                    <textarea class="form-control" rows="3" name="keterangan" required></textarea>
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
                      <label for="Nama">Nama *</label>
                      <input type="text" class="form-control" name="nama" id='edit_nama' required>
                  </div>
                  <input type="hidden" name="id_permintaan" id="edit_id_permintaan">
                  <div class="form-group">
                      <label for="batas_waktu">Batas Waktu *</label>
                      <div class="input-group">
                      <div class="input-group-addon">
                         <i class="fa fa-calendar"></i>
                      </div>
                      <input type="date" name='batas_waktu' class="form-control"/>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="Keterangan">Keterangan *</label>
                      <textarea class="form-control" rows="3" name="keterangan" id='edit_keterangan' required></textarea>
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
        function delete_permintaan(id) {
            $.ajax({
                url: '<?= base_url('operator/permintaan') ?>',
                type: 'POST',
                data: {
                    id_permintaan: id,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('operator/permintaan') ?>';
                }
            });
        }

        function get_permintaan(id) {
            $.ajax({
                url: '<?= base_url('operator/permintaan') ?>',
                type: 'POST',
                data: {
                    id_permintaan: id ,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#edit_id_permintaan').val(response.id_permintaan);
                    $('#edit_nama').val(response.nama);
                    $('#edit_keterangan').val(response.keterangan);
                }
            });
        }
    </script>
