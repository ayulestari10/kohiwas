<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Simpanan 
            <small style="font-size: 14px;"><a href="<?= base_url('ketua_koperasi/cetakSimpanan') ?>"><i class="fa fa-download"></i> Cetak</a></small>
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
                        <table class="table table-striped">
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Tanggal Simpanan</th>
                                <th>Simpanan Wajib</th>
                                <th>Simpanan Sukarela</th>
                            </tr>
                            <?php $i = 0; foreach($simpanan as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->anggota_m->get_row(['id_anggota' => $row->id_anggota])->nama ?></td>
                                <td><?= $row->tgl_simpanan ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_wajib,2,',','.') ?></td>
                                <td><?= "Rp " . number_format($row->simpanan_sukarela,2,',','.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>