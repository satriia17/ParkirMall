<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">

	<title>Hitung Biaya Parkir Mall</title>
</head>

<body>
	<div class="container border">
		<p><img style="float:left;" src="img/logo.png" alt="Logo Mall" width="50" height="50">
		<h2>Hitung Biaya Parkir Mall</h2>
		</p>
		<br>
		<form action="index.php" method="post" id="formPerhitungan">
			<div class="row">
				<div class="col-lg-2"><label for="nama">Plat Nomor Kendaraan:</label></div>
				<div class="col-lg-2"><input type="text" id="plat" name="plat"></div>
			</div>

			<div class="row">
				<div class="col-lg-2"><label for="tipe">Jenis Kendaraan:</label></div>
				<div class="col-lg-2">
					<?php
					$jenis_kendaraan = array('Truck', 'Mobil', 'Motor');
					sort($jenis_kendaraan);
					foreach ($jenis_kendaraan as $kendaraan) {
						echo "<input type='radio' name='kendaraan' value='$kendaraan'>$kendaraan<br>";
					}
					?>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-2"><label for="nomor">Jam Masuk [jam]:</label></div>
				<div class="col-lg-2">
					<select id="masuk" name="masuk">
						<option value="">- Jam Masuk -</option>
						<?php
						for ($i = 1; $i <= 24; $i++) {
							echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-2"><label for="nomor">Jam Keluar [jam]:</label></div>
				<div class="col-lg-2">
					<select id="keluar" name="keluar">
						<option value="">- Jam Keluar -</option>
						<?php
						for ($i = 1; $i <= 24; $i++) {
							echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-2"><label for="tipe">Keanggotaan:</label></div>
				<div class="col-lg-2">
					<input type='radio' name='member' value='Member'> Member <br>
					<input type='radio' name='member' value='Non-Member'> Non Member <br>

				</div>
			</div>

			<div class="row">
				<div class="col-lg-2"><button class="btn btn-primary" type="submit" form="formPerhitungan" value="hitung" name="hitung">Hitung</button></div>
				<div class="col-lg-2"></div>
			</div>
		</form>
	</div>

	<?php

	if (isset($_POST['hitung'])) {
		$durasi = $_POST['keluar'] - $_POST['masuk']; // Variabel durasi akan mengambil variabel keluar dan masuk setelah tombol submit dihitung

		function hitung_parkir($durasi, $kendaraan) //Pada function ini membutuhkan 2 parameter yaitu $durasi dan $kendaraaan
		{
			if ($kendaraan == 'Mobil') {
				$biaya = 5000 + ($durasi - 1) * 3000; //Jika kendaraan yang dipilih oleh user adalah mobil maka $biaya akan mengambil parameter durasi untuk perhitungannya
			} elseif ($kendaraan == 'Motor') {
				$biaya = 2000 + ($durasi - 1) * 1000;
			} elseif ($kendaraan == 'Truck') { //Jika kendaraan yang dipilih oleh user adalah truck maka biaya hanya akan menghitung biaya awal dikali durasi
				$biaya = 6000 * $durasi;
			}
			return $biaya;
		}
		$biaya_parkir = hitung_parkir($durasi, $_POST['kendaraan']); //variabel biaya parkir akan menjalankan fungsi dari hitung parkir dan akan mengambil variabel durasi dan kendaraan

		$biaya_akhir = $biaya_parkir; // Jika user memilih member maka biaya parkir akan mendapatkan diskon sebesar 10% tapi jika tidak maka program ini tidak akan dijalankan
		if ($_POST['member'] == 'Member') {
			$diskon = $biaya_parkir * 0.1;
			$biaya_akhir = $biaya_parkir - $diskon;
		}

		$dataParkir = array(
			'plat' => $_POST['plat'],
			'kendaraan' => $_POST['kendaraan'],
			'masuk' => $_POST['masuk'],
			'keluar' => $_POST['keluar'],
			'durasi' => $durasi,
			'member' => $_POST['member'],
		);

		$berkas = "data/data.json"; //variabel berkas akan cek file data.json
		$dataJson = json_encode($dataParkir, JSON_PRETTY_PRINT); //dalam file json data akan di encode
		file_put_contents($berkas, $dataJson); // Memasukkan data yang sudah di input kedalam dataJson lalu dimasukkan kembali ke file data.json
		$dataJson = file_get_contents($berkas); // Mengambil isi dari file Json
		$dataParkir = json_decode($dataJson, true); //Mengubah data menjadi Array


		//	Menampilkan data parkir kendaraan.
		echo "
		<br/>
		<div class='container'>
		<div class='row'>
		<!-- Menampilkan Plat Nomor Kendaraan. -->
		<div class='col-lg-2'>Plat Nomor Kendaraan:</div>
		<div class='col-lg-2'>" . $dataParkir['plat'] . "</div>
		</div>
		<div class='row'>
		<!-- Menampilkan Jenis Kendaraan. -->
		<div class='col-lg-2'>Jenis Kendaraan:</div>
		<div class='col-lg-2'>" . $dataParkir['kendaraan'] . "</div>
		</div>
		<div class='row'>
		<!-- Menampilkan Durasi Parkir. -->
		<div class='col-lg-2'>Durasi Parkir:</div>
		<div class='col-lg-2'>" . $dataParkir['durasi'] . " jam</div>
		</div>
		<div class='row'>
		<!-- Menampilkan Jenis Keanggotaan. -->
		<div class='col-lg-2'>Keanggotaan:</div>
		<div class='col-lg-2'>" . $dataParkir['member'] . " </div>
		</div>
		<div class='row'>
		<!-- Menampilkan Total Biaya Parkir. -->
		<div class='col-lg-2'>Total Biaya Parkir:</div>
		<div class='col-lg-2'>Rp" . number_format($biaya_akhir, 0, ".", ".") . ",-</div>
		</div>

		</div>
		";
	}
	?>

</body>

</html>