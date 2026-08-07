<?php

$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$cashValue = $_SESSION['cash_value'];

// Define the directory where the file will be uploaded
$uploadDirectory = './CashShop/';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if a file is uploaded
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Check if the uploaded file is in CSV format
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($fileExtension === 'csv') {
            // Move the uploaded file to the upload directory
            $uploadFile = $uploadDirectory . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Read the CSV file
                $fileHandle = fopen($uploadFile, 'r');
                $headerRow = fgetcsv($fileHandle); // Read the header row

                // Prepare the data array
                $data = [];
                while (($rowData = fgetcsv($fileHandle)) !== false) {
                    $item = [];
                    foreach ($headerRow as $index => $header) {
                        if (isset($rowData[$index])) {
                            $item[$header] = $rowData[$index];
                        } else {
                            $item[$header] = null;
                        }
                    }
                    $data[] = $item;
                }
                fclose($fileHandle);

                // Convert the data array to JSON
                $json = json_encode($data, JSON_PRETTY_PRINT);

                // Save the JSON data to a file
                $jsonFile = './CashShop/itemData.json';
                file_put_contents($jsonFile, $json);

                // Convert the data array to JavaScript format
                $jsContent = "const itemData = " . json_encode($data, JSON_PRETTY_PRINT) . ";\n\n";
                $jsContent .= "export default itemData;";

                // Save the JavaScript code to a file
                $jsFile = './CashShop/itemData.js';
                file_put_contents($jsFile, $jsContent);

                echo 'File uploaded and converted to JSON and JavaScript successfully.';
            } else {
                echo 'Failed to move the uploaded file to the server.';
            }
        } else {
            echo 'Please upload a valid CSV file.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload CSV and Convert</title>
</head>
<body>
    <form class = "upload" action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" name="submit" value="Upload and Convert">
    </form>
</body>
</html>