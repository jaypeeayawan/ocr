<?php
header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['image'])) {
	echo json_encode(["error" => "No image received"]);
	exit;
}

$outputText = "";

// Decode the Base64 image
$imageData = $data['image'];
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = base64_decode($imageData);

// Save the image in the "uploads" directory
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0777, true); // Create folder if not exists
}
$targetFile = $uploadDir . time() . ".png";

if (file_put_contents($targetFile, $imageData)) {

	$tesseractPath = "C:\\Program Files\\Tesseract-OCR\\tesseract.exe";
	$outputFile = "output"; // Temporary output text file
	$command = "\"$tesseractPath\" \"$targetFile\" \"$outputFile\"";
	exec($command);

	if (file_exists("$outputFile.txt")) {
		$outputText = file_get_contents("$outputFile.txt");
		unlink("$outputFile.txt");
	} else {
		$outputText = "OCR failed. No text extracted.";
	}
	echo json_encode(["success" => true, "extractedText" => $outputText]);
} else {
	echo json_encode(["error" => "Failed to save image"]);
}
