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
    <title>OCR Image Upload</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/webcam.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>
<body>

    <div class="container" style="margin-top: 5em;">
        
        <div class="row">
            <div class="col-md-12">
                
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#upload_img" aria-controls="upload_img" role="tab" data-toggle="tab">Upload Image</a></li>
                <li role="presentation"><a href="#use_camera" aria-controls="use_camera" role="tab" data-toggle="tab">Use Camera</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content">

                <div role="tabpanel" class="tab-pane active" id="upload_img">
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

                <div role="tabpanel" class="tab-pane" id="use_camera">
                    <h2 class="text-left">Use Camera for OCR</h2>
                    <form method="post" action="capture.php">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="text-info">Camera</h3>
                                <div class="form-group" style="background-color: #fff; border: 1px solid #ddd; padding: 2px">
                                    <div id="camera"></div>
                                </div>
                                <input type="button" class="btn btn-default" value="Take Snapshot" onclick="take_snapshot()">
                                <input type="hidden" name="image" class="capture-img">
                            </div>

                            <div class="col-md-4">
                                <h3 class="text-info">Captured Image</h3>
                                <div id="captured-img"></div>
                            </div>

                            <div class="col-md-4">
                                <h3 class="text-info">Extracted Text</h3>
                                <textarea rows="10" cols="60"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

              </div>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        Webcam.set({
            width:350,
            height: 310,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        // attach webcam
        Webcam.attach("#camera");

        // take snapshot
        function take_snapshot() {
            Webcam.snap(function(data_url) {
                $(".capture-img").val(data_url);
                document.getElementById("captured-img").innerHTML = '<div class="form-group"><img src="'+data_url+'" class="img-thumbnail"></div><button type="submit" class="btn btn-default">Submit</button>';
            });
        }
    </script>
</body>
</html>
