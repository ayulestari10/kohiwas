<!-- Content Header (Page header) -->
<?php $approved = $this->permintaan_bahan_baku_m->get_row(['id_permintaan'=> $this->uri->segment(3)])->approved ?>
<div class="container">
    <section class="content-header">
        <h1>
            Detail Permintaan
            <?php if ($approved!=1): ?>
              <a href="<?=base_url('operator/addpermintaan/?id='.$this->uri->segment(3))?>"><button class="btn btn-success"><i class="fa fa-plus"></i></button></a>
            <?php endif; ?>
            <small></small>
        </h1>
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
                              <td></td>
                              <td><strong>Rp. <?=$total?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

    <div class="modal fade" tabindex="-1" role="dialog" id="editSupplier">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?= form_open('operator/detail_permintaan/'.$this->uri->segment(3)) ?>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Jumlah</h4>
          </div>
          <div class="modal-body">
                  <div class="form-group">
                      <label for="Nama">Jumlah *</label>
                      <input type="number" class="form-control" name="jumlah" id='edit_jumlah' required>
                  </div>
                  <input type="hidden" name="id_detail_permintaan" id='edit_id'>
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
                url: '<?= base_url('operator/detail_permintaan/') ?>',
                type: 'POST',
                data: {
                    id_detail_permintaan: id,
                    delete: true
                },
                success: function() {
                    window.location = '<?= base_url('operator/detail_permintaan/'.$this->uri->segment(3)) ?>';
                }
            });
        }

        function get_permintaan(id) {
            $.ajax({
                url: '<?= base_url('operator/detail_permintaan')?>',
                type: 'POST',
                data: {
                    id_detail_permintaan: id ,
                    get: true
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#edit_jumlah').val(response.jumlah_permintaan);
                    $('#edit_id').val(response.id_detail_permintaan);
                }
            });
        }
    </script>
