<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Data Anggota
            <small style="font-size: 14px;"><a href="<?= base_url('direktur/cetakBahanBaku') ?>"><i class="fa fa-download"></i> Cetak</a></small>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Total Stok</th>
                                                    <th>Total Permintaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php  
                                                    $bahan = ["Tawas", "Soda", "Kaporit"];
                                                    $total_permintaan = [];
                                                    $tmp_pr = [];
                                                    foreach ($permintaan as $row)
                                                    {
                                                        $tmp_pr []= $row->bahan_baku;
                                                        $total_permintaan[$row->bahan_baku] = $row->total_permintaan;
                                                    }
                                                    $temp = [];
                                                    $tmp = [];
                                                    $tpm = [];
                                                    foreach ($total_stok as $row)
                                                    {
                                                        $tmp []= $row->nama_bahan; 
                                                        $temp[$row->nama_bahan] = $row->total_stok . ' ' . $row->satuan;
                                                        $tpm[$row->nama_bahan] = $row->total_stok; 
                                                    }
                                                    
                                                    $minimum = [];
                                                    foreach ($bahan as $row)
                                                    {
                                                        if (!isset($temp[$row]))
                                                            $minimum[$row] = 1;
                                                        else
                                                        {
                                                            if (isset($total_permintaan[$row]))
                                                                if ($tpm[$row] - $total_permintaan[$row] <= 10)
                                                                    $minimum[$row] = 1;
                                                                else
                                                                    $minimum[$row] = 0;
                                                            else
                                                            {
                                                                if ($temp[$row] <= 10)
                                                                    $minimum[$row] = 1;
                                                                else
                                                                    $minimum[$row] = 0;
                                                            }
                                                        }
                                                    }

                                                    foreach ($bahan as $row):
                                                ?>
                                                <tr <?php if ($minimum[$row] == 1) echo 'style="background-color: #F77"' ?>>
                                                    <td><b><?= $row ?></b></td>
                                                    <td><?= in_array($row, $tmp) ? $temp[$row] : 0 ?></td>    
                                                    <td><?= in_array($row, $tmp_pr) ? $total_permintaan[$row] : 0 ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <table class="table table-striped">
                            <tr>
                                <th>No</th>
                                <th>Supplier</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Tanggal</th>
                            </tr>
                            <?php $i = 0; foreach ($bahan_baku as $row): ?>
                            <tr>
                                <td><?= ++$i ?></td>
                                <td><?= $this->supplier_m->get_row(['id_suplier' => $row->id_suplier])->nama_suplier ?></td>
                                <td><?= $row->nama_bahan ?></td>
                                <td><?= $row->jenis_bahan ?></td>
                                <td><?= $row->stok ?></td>
                                <td><?= $row->satuan ?></td>
                                <td><?= "Rp " . number_format($row->harga,2,',','.') ?></td>
                                <td><?= $row->tanggal ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>