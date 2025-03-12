<?php
$outputText = "";
$uploadedImage = "";

if (isset($_POST['submitUpload']) && isset($_FILES["inputImage"])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($_FILES["inputImage"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "jpeg", "png", "bmp", "gif", "tiff"];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["inputImage"]["tmp_name"], $targetFile)) {
            $uploadedImage = $targetFile;


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
        } else {
            $outputText = "File upload failed!";
        }
    } else {
        $outputText = "Invalid file type! Please upload an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>OCR | Image Upload</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<body>

    <div class="container" style="margin-top: 5em;">

        <div class="row">
            <div class="col-md-12">

                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="index.php">Upload Image</a></li>
                    <li role="presentation"><a href="camera.php">Use Camera</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active">
                        <h2 class="text-left">Upload an Image for OCR</h2>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="inputImage">File input</label>
                                <input type="file" name="inputImage" required>
                            </div>
                            <button type="submit" name="submitUpload" class="btn btn-default">Upload & Extract Text</button>
                        </form>

                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($uploadedImage)): ?>
                                    <h3 class="text-info">Uploaded Image:</h3>
                                    <img src="<?php echo $uploadedImage; ?>" alt="Uploaded Image" class="img-responsive img-thumbnail" style="max-width: 80%; height: auto;">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($outputText)): ?>
                                    <h3 class="text-info">Extracted Text:</h3>
                                    <textarea rows="10" cols="60"><?php echo htmlspecialchars($outputText); ?></textarea>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</body>

</html>