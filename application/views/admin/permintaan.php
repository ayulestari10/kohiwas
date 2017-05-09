<!-- Content Header (Page header) -->
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
                                                          <a href="<?=base_url('admin/detail_permintaan/'.$row->id_permintaan)?>"><button class="btn btn-primary">Lihat Detail</button></a>
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
