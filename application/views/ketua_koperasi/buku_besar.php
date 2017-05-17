<style type="text/css">
  table,th, td{text-align: center;border: 1px solid black;}

</style>
<!-- Content Header (Page header) -->
<div class="container">
    <section class="content-header">
        <h1>
            Buku Besar
            <small style="font-size: 14px;"><a href="<?= base_url('direktur/cetakBukuBesar') ?>"><i class="fa fa-download"></i> Cetak</a></small>
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
                                    <table class="table table-striped">
                                        Nama Akun : Kas
                                        <tr>
                                          <th rowspan="2">Tanggal</th>
                                          <th rowspan="2">Keterangan</th>
                                          <th rowspan="2">Ref</th>
                                          <th rowspan="2">Debit</th>
                                          <th rowspan="2">Kredit</th>
                                          <th colspan="2">Saldo</th>
                                        </tr>
                                        <tr>
                                          <th>Debit</th>
                                          <th>Kredit</th>
                                        </tr>
                                        <?php $i = 0; foreach ($detail as $rowd): ?>
                                        <tr>
                                            <?php 
                                              $permintaan = $this->permintaan_bahan_baku_m->get(['id_permintaan' => $rowd->id_permintaan]);
                                              foreach($permintaan as $rowp): 
                                            ?>
                                            <td><?= $rowp->tanggal_permintaan ?></td>
                                            <td><?= $rowp->keterangan ?></td>
                                            <td>Ref</td>
                                            <td>
                                              <?php  
                                                $debit = $this->rencana_pembelian_m->get(['tanggal' => $rowp->tanggal_permintaan]);
                                                if (isset($debit->dana)){
                                                    echo "Rp " . number_format($debit->dana,2,',','.'); 
                                                }
                                              ?>
                                            </td>
                                            <td>
                                                <?php 
                                                  $total = 0;
                                                  $bahan = $this->bahan_baku_m->get_row(['nama_bahan'=>$rowp->nama]);
                                                      if (isset($bahan)){
                                                        $total = $total + ($bahan->harga * $rowd->jumlah_permintaan);
                                                      }
                                                      if (isset($total)){
                                                          echo "Rp " . number_format($total,2,',','.'); 
                                                      }
                                                ?>
                                            </td>
                                          <?php endforeach; ?>
                                          </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>

                            <div class="box">
                                <div class="box-header">
                                    <!-- <h3 class="box-title">Data Supplier</h3>   -->
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table class="table table-striped">
                                        Nama Akun : Penjualan
                                        <tr>
                                          <th rowspan="2">Tanggal</th>
                                          <th rowspan="2">Keterangan</th>
                                          <th rowspan="2">Ref</th>
                                          <th rowspan="2">Debit</th>
                                          <th rowspan="2">Kredit</th>
                                          <th colspan="2">Saldo</th>
                                        </tr>
                                        <tr>
                                          <th>Debit</th>
                                          <th>Kredit</th>
                                        </tr>
                                        <?php $i = 0; foreach ($detail as $rowd): ?>
                                        <tr>
                                            <?php 
                                              $permintaan = $this->permintaan_bahan_baku_m->get(['id_permintaan' => $rowd->id_permintaan]);
                                              foreach($permintaan as $rowp): 
                                            ?>
                                            <td><?= $rowp->tanggal_permintaan ?></td>
                                            <td><?= $rowp->keterangan ?></td>
                                            <td>Ref</td>
                                            <td>
                                              <?php  
                                                $debit = $this->rencana_pembelian_m->get(['tanggal' => $rowp->tanggal_permintaan]);
                                                if (isset($debit->dana)){
                                                    echo "Rp " . number_format($debit->dana,2,',','.'); 
                                                }
                                              ?>
                                            </td>
                                            <td>
                                                <?php 
                                                  $total = 0;
                                                  $bahan = $this->bahan_baku_m->get_row(['nama_bahan'=>$rowp->nama]);
                                                      if (isset($bahan)){
                                                        $total = $total + ($bahan->harga * $rowd->jumlah_permintaan);
                                                      }
                                                      if (isset($total)){
                                                          echo "Rp " . number_format($total,2,',','.'); 
                                                      }
                                                ?>
                                            </td>
                                          <?php endforeach; ?>
                                          </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>


                            <div class="box">
                                <div class="box-header">
                                    <!-- <h3 class="box-title">Data Supplier</h3>   -->
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table class="table table-striped">
                                        Nama Akun : Pembelian
                                        <tr>
                                          <th rowspan="2">Tanggal</th>
                                          <th rowspan="2">Keterangan</th>
                                          <th rowspan="2">Ref</th>
                                          <th rowspan="2">Debit</th>
                                          <th rowspan="2">Kredit</th>
                                          <th colspan="2">Saldo</th>
                                        </tr>
                                        <tr>
                                          <th>Debit</th>
                                          <th>Kredit</th>
                                        </tr>
                                        <?php $i = 0; foreach ($detail as $rowd): ?>
                                        <tr>
                                            <?php 
                                              $permintaan = $this->permintaan_bahan_baku_m->get(['id_permintaan' => $rowd->id_permintaan]);
                                              foreach($permintaan as $rowp): 
                                            ?>
                                            <td><?= $rowp->tanggal_permintaan ?></td>
                                            <td><?= $rowp->keterangan ?></td>
                                            <td>Ref</td>
                                            <td>
                                              <?php  
                                                $debit = $this->rencana_pembelian_m->get(['tanggal' => $rowp->tanggal_permintaan]);
                                                if (isset($debit->dana)){
                                                    echo "Rp " . number_format($debit->dana,2,',','.'); 
                                                }
                                              ?>agafaaa
                                            </td>
                                            <td>
                                                <?php 
                                                  $total = 0;
                                                  $bahan = $this->bahan_baku_m->get_row(['nama_bahan'=>$rowp->nama]);
                                                      if (isset($bahan)){
                                                        $total = $total + ($bahan->harga * $rowd->jumlah_permintaan);
                                                      }
                                                      if (isset($total)){
                                                          echo "Rp " . number_format($total,2,',','.'); 
                                                      }
                                                ?>
                                            </td>
                                          <?php endforeach; ?>
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