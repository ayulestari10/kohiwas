                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Admin Panel</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?= base_url('admin') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><?= count($anggota) ?></h3>
                                    <p>
                                        Daftar Anggota
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="<?= base_url('admin/data_anggota') ?>" class="small-box-footer">
                                    Selengkapnya <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                        <?= count($simpanan) ?> <!-- <sup style="font-size: 20px">%</sup> -->
                                    </h3>
                                    <p>
                                        Data Simpanan
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="<?= base_url('admin/data_simpanan') ?>" class="small-box-footer">
                                    Selengkapnya <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        <?= count($pinjaman) ?>
                                    </h3>
                                    <p>
                                        Data Pinjaman
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="<?= base_url('admin/data_pinjaman') ?>" class="small-box-footer">
                                    Selengkapnya <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        <?= count($angsuran) ?>
                                    </h3>
                                    <p>
                                        Data Angsuran
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="<?= base_url('admin/data_angsuran') ?>" class="small-box-footer">
                                    Selengkapnya<i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
