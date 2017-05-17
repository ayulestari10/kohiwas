<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Angsuran 
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakAngsuran') ?>"><i class="fa fa-download"></i> Cetak</a></small>
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
                            </tr>
                            <?php $i = 0; foreach ($angsuran as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $row->id_pinjaman ?></td>
                                <td><?= $row->tgl_angsuran ?></td>
                                <td><?= "Rp " . number_format($row->jlh_dibayar,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->sisa_angsuran,2,',','.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>