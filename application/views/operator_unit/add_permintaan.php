<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Add Permintaan Bahan Baku</h3>
                                </div><!-- /.box-header -->
                                <!-- form start -->
                                <?php $id = $_GET['id'] ?>
                                <?= form_open('operator/detail_permintaan/'.$id) ?>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="jumlah">Jumlah</label>
                                            <input type="number" class="form-control" name="jumlah_permintaan" placeholder="Jumlah">
                                        </div>
                                        <input type="hidden" name="id_permintaan" value="<?=$id?>">
                                        <div class="form-group">
                                            <label>Bahan</label>
                                            <?php
                                                $supp_dropdown = [];
                                                foreach ($bahan_baku as $key){
                                                  $supp_dropdown[$key->id_bahan_baku] = $key->nama_bahan;
                                                }
                                                echo form_dropdown('id_bahan_baku', $supp_dropdown, '', ['class' => 'form-control']);
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="Keterangan">Keterangan *</label>
                                            <textarea class="form-control" rows="3" name="keterangan" required></textarea>
                                        </div>
                                    </div><!-- /.box-body -->

                                    <div class="box-footer">
                                        <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
                                    </div>
                                <?= form_close() ?>
                            </div><!-- /.box -->
    </div>
  </div>
</section>
