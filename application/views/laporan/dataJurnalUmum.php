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
            width: 100px;
            height: 70px;
            margin-left: 5px;
            margin-right: 40px; 
        }
        .logoo img{
            width: 100px;
            height: 50px;
        }
        .title{
            margin: 0 auto;
            margin-top: -80px;
            width: 600px;
            font-size: 18px;
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
        #logo2{
            padding-left: 1000px;
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
            <div class="logoo" id="logo2" style="margin-top: -80px;">
                <!-- <img src="<?= base_url('') ?>assets/img/oku.png" width="70" height="100"> -->
            </div>
        </div>
        <div class="content" style="margin: 0 auto; width:100%;">
            <p style="margin-top: 50px; width: 100%; font-weight: bold; font-size: 18px; text-align: center; margin-bottom: 30px;">Laporan Data Anggota</p>
            <?php if (!isset($periode)): ?>
                <span><b>Periode: All</b></span>
            <?php else: ?>
                <span><b>Periode: <?= $periode['min'] . ' - ' . $periode['max'] ?></b></span>
            <?php endif; ?>
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody id="data-view">
                    <?php foreach ($jurnal_umum as $row): ?>
                    <tr>
                        <td><?= $row->tgl ?></td>
                        <td><?= $row->ket ?></td>
                        <td><?= "Rp " . number_format($row->debit,2,',','.') ?></td>
                        <td><?= "Rp " . number_format($row->kredit,2,',','.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>Total Debit</b></td>
                        <td><?= "Rp " . number_format($total->total_debit,2,',','.') ?></td>
                        <td><b>Total Kredit</b></td>
                        <td><?= "Rp " . number_format($total->total_kredit,2,',','.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>