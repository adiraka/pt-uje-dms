<?php  

	require_once '../app.php';

	session_start();

	$conn = koneksi();
	$data = [];

	$supir_id = sanitizeThis($_POST['supir_id']);
	$plate = sanitizeThis($_POST['plate']);
	$merk = sanitizeThis($_POST['merk']);
	$jenis = sanitizeThis($_POST['jenis']);
	$gross = sanitizeThis($_POST['gross']);

	// upload foto mobil
	
	$file_type = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
	$file_name = sanitizeThis($_FILES['foto']['name']);
	$file_size = $_FILES['foto']['size'];
	$target_dir = '../../assets/img/mobil/';
	$check = getimagesize($_FILES['foto']['tmp_name']);
	if ($check == false) {
		$data['status'] = 'ERROR';
		$data['message'] = 'Foto Mobil yang diinputkan bukan merupakan file gambar!';
		echo json_encode($data);
		die();
	}
	if ($file_type != 'jpeg' && $file_type != 'jpg' && $file_type != 'png' && $file_type != 'JPEG' && $file_type != 'JPG' && $file_type != 'PNG') {
		$data['status'] = 'ERROR';
		$data['message'] = 'Hanya file gambar dengan ekstensi jpeg, jpg, dan png yang diizinkan!';
		echo json_encode($data);
		die();
	}
	if ($file_size > 2000000) {
		$data['status'] = 'ERROR';
		$data['message'] = 'Ukuran file Foto Mobil maksimal 2MB!';
		echo json_encode($data);
		die();
	}
	$new_file_name = substr(sha1(time()), 0, 20).'.'.$file_type;
	$new_target_file = $target_dir.$new_file_name;
	$upload_file = move_uploaded_file($_FILES['foto']['tmp_name'], $new_target_file);
	if (!$upload_file) {
		$data['status'] = 'ERROR';
		$data['message'] = 'Telah terjadi kesalahan dalam mengupload Foto Mobil!';
		echo json_encode($data);
		die();
	}

	$query = "INSERT INTO tb_mobil (supir_id, plate, merk, jenis, gross, status, foto) VALUES('$supir_id', '$plate', '$merk', '$jenis', '$gross', '0', '$new_file_name')";
	$process = mysqli_query($conn, $query);

	if ($process) {
		$data['status'] = 'OK';
		$data['message'] = 'Data Supir berhasil ditambahkan!'; 
	} else {
		$data['status'] = 'ERROR';
		$data['message'] = 'Telah terjadi sebuah kesalahan!';
	}

	echo json_encode($data);

?>