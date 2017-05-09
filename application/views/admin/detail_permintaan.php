<!-- Content Header (Page header) -->
<?php $approved = $this->permintaan_bahan_baku_m->get_row(['id_permintaan'=> $this->uri->segment(3)])->approved ?>
<div class="container">
    <section class="content-header">
        <h1>
            Detail Permintaan
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
