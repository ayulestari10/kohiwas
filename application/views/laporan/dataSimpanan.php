<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
        #bigWrapper{
            width: 100%;
        }
        #header{
            text-align: center;
            font-size: 26px;
            margin-bottom: 50px;
            border-bottom: 5px double black;
            padding-bottom: 15px;
            width: 1332px;
            margin: 0 auto;
            height: 100px;
        }

        .logoo{
            margin-top: -210px;
            width: 150px;
            height: 170px;
            margin-left: 5px;
            margin-right: 40px; 
        }
        .logoo img{
            width: 130px;
            height: 80px;
        }
        .title{
            margin: 0 auto;
            margin-top: -130px;
            width: 600px;
            font-size: 20px;
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
<body style="margin-top: 250px;">
	<div id="bigWrapper">
		<div id="header">
			<div class="logoo">
				<img src="<?= base_url('') ?>assets/img/logo.jpg">
			</div>

			<div class="title">
				<strong>
					PENGURUS DAN BADAN PENGAWAS<br> 
                    KOPERASI HIMPUNAN PENGAWAS (KOHIWAS) <br>
                    DINAS PENDIDIKAN KABUPATEN OKU  
				</strong>
			</div>
            <div class="logoo" style="margin-left:1000px; margin-top: -120px;">
                <img src="<?= base_url('') ?>assets/img/oku.png" width="130" height="80">
            </div>
		</div>
		<div class="content" style="margin: 0 auto; width:100%;">
			<p style="margin-top: 50px; width: 100%; font-weight: bold; font-size: 22px; text-align: center; margin-bottom: 30px;">Laporan Data Simpanan</p>
            <table style="width: 100%;">
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
</body>
</html>