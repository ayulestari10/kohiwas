<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Direktur | PDAM TIRTA RANDIK</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

        <!-- jQuery 2.0.2 -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- jQuery UI 1.10.3 -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
        <!-- Bootstrap -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/bootstrap.min.js" type="text/javascript"></script>

        <!-- Ionicons -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="<?= base_url('') ?>assets/AdminLTE/css/AdminLTE.css" rel="stylesheet" type="text/css" />
         <!-- Bootstrap time Picker -->
        <link href="<?= base_url('assets/AdminLTE/css/timepicker/bootstrap-timepicker.min.css') ?>" rel="stylesheet"/>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?=base_url('direktur')?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                Direktur
            </a>
        <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?="Nama Direktur"?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="<?=base_url('logout')?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Content -->
        <div class="container">
            <section class="content-header">
                <h1>
                    Permintaan Bahan Baku
                    <small></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                      <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Permintaan</a></li>
                                <li><a href="#tab_2" data-toggle="tab">Permintaan Terkonfirmasi</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
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
                                                <th>Unit</th>
                                                <th>Tanggal Permintaan</th>
                                                <th>Batas Waktu</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                              </tr>
                                              <?php $i = 0; foreach ($permintaan as $row): ?>
                                              <tr>
                                                  <td><?= ++$i ?></td>
                                                  <td><?=$row->nama?></td>
                                                  <td><?= $this->unit_m->get_row(['id_unit'=>$row->id_unit])->nama_unit ?></td>
                                                  <td><?= $row->tanggal_permintaan ?></td>
                                                  <td><?= $row->batas_waktu ?></td>
                                                  <td><?=$row->keterangan?></td>
                                                  <td>
                                                      <a href="<?=base_url('direktur/detail_permintaan/'.$row->id_permintaan)?>"><button class="btn btn-primary">Lihat Detail</button></a>
                                                  </td>
                                              </tr>
                                              <?php endforeach; ?>
                                          </table>
                                      </div>
                                  </div>
                                </div><!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2">
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
                                                <th>Unit</th>
                                                <th>Tanggal Permintaan</th>
                                                <th>Batas Waktu</th>
                                                <th>Keterangan</th>
                                                <td>Aksi</td>
                                              </tr>
                                              <?php $i = 0; foreach ($permintaan_approved as $row): ?>
                                              <tr>
                                                  <td><?= ++$i ?></td>
                                                  <td><?=$row->nama?></td>
                                                  <td><?= $this->unit_m->get_row(['id_unit'=>$row->id_unit])->nama_unit ?></td>
                                                  <td><?= $row->tanggal_permintaan ?></td>
                                                  <td><?= $row->batas_waktu ?></td>
                                                  <td><?=$row->keterangan?></td>
                                                  <td>
                                                    <a href="<?=base_url('direkturdetail_permintaan/'.$row->id_permintaan)?>"><button class="btn btn-primary">Lihat Detail</button></a>
                                                  </td>
                                              </tr>
                                              <?php endforeach; ?>
                                          </table>
                                      </div>
                                  </div>
                                </div><!-- /.tab-pane -->
                            </div><!-- /.tab-content -->
                        </div><!-- nav-tabs-custom -->
                    </div>
                </div>
            </section>

            </div>

    <!--/Content-->



        <!-- Morris.js charts -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- Sparkline -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- fullCalendar -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <!-- jQuery Knob Chart -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
        <!-- daterangepicker -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <!-- Time Picker-->
        <script src="<?= base_url('assets/AdminLTE/js/plugins/input-mask/jquery.inputmask.js') ?>" type="text/javascript"></script>
        <script src="<?= base_url('assets/AdminLTE/js/plugins/input-mask/jquery.inputmask.date.extensions.js') ?>" type="text/javascript"></script>
        <script src="<?= base_url('assets/AdminLTE/js/plugins/input-mask/jquery.inputmask.extensions.js') ?>" type="text/javascript"></script>
        <script src="<?= base_url('assets/AdminLTE/js/plugins/timepicker/bootstrap-timepicker.min.js') ?>" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/AdminLTE/app.js" type="text/javascript"></script>
        
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="<?= base_url('') ?>assets/AdminLTE/js/AdminLTE/dashboard.js" type="text/javascript"></script>
    </body>
    </html>
