<?php

	$img = $_POST['image'];

	$folderPath = "uploads/";

	

	$image_parts = explode(";base64,", $img);
	// echo '<pre>';
	// print_r($image_parts);
	// die;

	$image_type_aux = explode("image/", $image_parts[0]);

	$image_type = $image_type_aux[1];

	$image_base64 = base64_decode($image_parts[1]);

	$filename = uniqid().'.png';
	$file = $folderPath.$filename;

	file_put_contents($file, $image_base64)

?>