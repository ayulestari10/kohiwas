<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		#bigWrapper{
            width: 100%;
        }
        .header{
            text-align: center;
            font-size: 26px;
            margin-bottom: 50px;
            border-bottom: 5px double black;
            padding-bottom: 15px;
        }

        #logoo{
            margin-top: -210px;
            width: 100px;
            height: 170px;
            margin-left: 5px;
            margin-right: 40px; 
        }
        #logoo img{
            width: 130px;
            height: 80px;
        }
        .title{
            margin-left: 50px;
            margin-top: -190px;
        }
        .kontak{
            margin-top: 5px;
            font-size: 12px;
            text-align: center;
        }
        table,th,td{
            border: 1px solid black;
        }
        table {
            border-collapse: collapse;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2
        }
        tr:first-child{
            width: 40px;
        }
        th{
            background-color: #4CAF50;
            color: white;
            /*min-width: 100px;*/
        }
        td{
            padding: 2px;
            padding-left: 10px; 
            text-align: center;
        }
	</style>
</head>
<body >
	<div id="bigWrapper">
		<div class="header">
			<div id="logoo">
				<img src="<?= base_url('') ?>assets/logo/logo.jpg">
			</div>

			<div class="title">
				<strong>
					PERUSAHAAN DAERAH AIR MINUM <br>
					"TIRTA RANDIK"
				</strong>
				<div class="kontak">
					Jalan Merdeka No. 123 Serasan Jaya Sekayu Kabupaten.<br> Musi Banyuasin Sumatera Selatan
				</div>
			</div>
		</div>
		<div class="content" style="margin: 0 auto; width:100%;">
			<p style="margin-top: -30px; width: 100%; font-weight: bold; font-size: 22px; text-align: center; margin-bottom: 30px;">Laporan Data Permintaan</p>
            <table style="width: 100%;">
				<tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Bahan Baku</th>
                    <th colspan="6">Permintaan</th>
                </tr>
                <tr>
                	<th>Jumlah</th>
                	<th>Tanggal Permintaan</th>
                    <th>Batas Waktu</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
                <?php $i = 1; foreach($detailPermintaan as $rowp): ?>
                <tr>
            		<td><?= $i++ ?></td>
            		<td><?= $rowp->bahan_baku ?></td>
            		<td><?= $rowp->jumlah_permintaan ?></td>
            		<?php  
            			$id_permintaan = $rowp->id_permintaan;
            			$this->data['permintaan'] = $this->permintaan_bahan_baku_m->get(['id_permintaan' => $id_permintaan]);
            			$p = $this->data['permintaan'];
            			foreach($p as $row):
            		?>
            		<td><?= $row->tanggal_permintaan ?></td>
            		<td><?= $row->batas_waktu ?></td>
            		<?php 
            		  $total = 0;
            		  $bahan = $this->bahan_baku_m->get_row(['nama_bahan'=>$rowp->bahan_baku]);
                      if (isset($bahan)){
                        $total = $total + ($bahan->harga * $rowp->jumlah_permintaan); 
                      }
                    ?>
            		<td>
            			<?php 
	                        if (isset($total)){
	                            echo "Rp " . number_format($total,2,',','.'); 
	                        }	                    
	                    ?>
            		</td>
            		<td>
            			<?php if($row->approved == 1): ?>
            				Disetujui
            			<?php else: ?>
            				Tidak Disetujui
            			<?php endif; ?>
            		</td>
            		<td><?= $row->keterangan ?></td>
            		<?php endforeach; ?>
                </tr>
	            <?php endforeach; ?>
			</table>
		</div>
	</div>
</body>
</html>