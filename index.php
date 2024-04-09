<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passap-ify-r'Us!</title>
    <style>
        table {
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }
        pre {
            font-family: monospace;
        }
    </style>
</head>
<body>
<CENTER>
<IMG SRC=passaplol.jpg>
</CENTER>


<?php
function convertToFourColors($imagePath, $newWidth) {
    $image = imagecreatefromstring(file_get_contents($imagePath));

    $aspectRatio = $newWidth / imagesx($image);
    $newHeight = imagesy($image) * $aspectRatio;
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($image), imagesy($image));

    imagetruecolortopalette($resizedImage, false, 4);

    $pixelValues = [];
    for ($y = 0; $y < $newHeight; $y++) {
        $row = [];
        for ($x = 0; $x < $newWidth; $x++) {
            $pixelColor = imagecolorat($resizedImage, $x, $y);
            $closestColor = imagecolorsforindex($resizedImage, $pixelColor);
            $row[] = ($closestColor['red'] % 4 + 1); 
        }
        $pixelValues[] = $row;
    }

    
    echo "<P>Okay, so this is the thing..<br><img src='$imagePath' width='$newWidth' /><br></P>";

    // Display pixel values as a table
    echo "<table>";
    foreach ($pixelValues as $row) {
        echo "<tr>";
        foreach ($row as $pixel) {
            echo "<td><font size=1>$pixel</font></td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    // Generate CSV content
    $csvContent = '';
    foreach ($pixelValues as $row) {
        $csvContent .= implode(',', $row) . "\n";
    }

    // Generate download link for CSV file
    $csvFileName = "passap_diarrea.csv";
    $csvFileLink = "data:text/csv;charset=utf-8," . urlencode($csvContent);
    echo "<a href='$csvFileLink' download='$csvFileName'>You can totally download the fucking file here :)</a>";

    // Free memory
    imagedestroy($image);
    imagedestroy($resizedImage);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && isset($_POST["width"])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        echo "mime check - " . $check["mime"] . " all hunkydory.<br>";
        $uploadOk = 1;
    } else {
        echo "Da fuc bro, not a image.<br>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your pp is too large. Less than 5 mb, dawg<br>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "With the risk of sounding racist, only JPG, JPEG, PNG & GIF files are allowed here!<br>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Something went to fuck and your file is like not uploaded at all. I know, suck dick.<br>";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            echo "Okay, got the damn file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " and i huffed and puffed on it. What a great day!<br>";

            // Convert image to 4 colors and display
            $newWidth = $_POST["width"];
            convertToFourColors($targetFile, $newWidth);
        } else {
            echo "Sorry, your file is fucked. No fucking clue what happened.<br>";
        }
    }
}
?>
<font size=3>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    Select a file you want to passapify:
    <input type="file" name="image" id="image">
    <br>
    How many columns wide do you need this mofo to be?
    <input type="text" name="width" id="width">
    <br>
    <input type="submit" value="Lets fucking go" name="submit">
</form>
Am i the only one with a rockhard erection? (c) 2024
</body>
</html>

