<?php

if (!isset($_SESSION['user_id'])) {
   header("Location: dashboard.php");
   exit;
}
require 'config.php';
$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$startCharIdx = $usernum * 8;
$endCharIdx = $startCharIdx + 5;
$welcome = "Welcome " . $userID . "!";

// Function to query WarExp value for a given characterIdx
function getWarExpValue($charIdx) {
    try {
        // Establish MSSQL connection (assumes it's defined in Server01Config.php)
        global $connServer01; // Assuming the connection is stored in the $conn variable
        
        // Query the cabal_WarExp_Table table
        $query = "SELECT WarExp FROM dbo.cabal_WarExp_Table WHERE CharacterIdx = ?";
        $params = array($charIdx);
        $stmt = sqlsrv_query($connServer01, $query, $params);
        
        if ($stmt === false) {
            throw new Exception("Error while querying the cabal_WarExp_Table table: " . sqlsrv_errors());
        }
        
        // Fetch the WarExp value
        if (sqlsrv_fetch($stmt) === false) {
            throw new Exception("No matching record found for the characterIdx '$charIdx'");
        }
        
        $warExpValue = sqlsrv_get_field($stmt, 0);
        
        // Close the statement
        sqlsrv_free_stmt($stmt);
        
        // Return the WarExp value
        return $warExpValue;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
$totalWarExp=0;

for ($charIdx = $startCharIdx; $charIdx <= $endCharIdx; $charIdx++) {
    $warExpValue = getWarExpValue($charIdx); // Assuming there is a function to get the war experience value for a given character index
    $totalWarExp += $warExpValue; // Add the war experience value to the total
}
$_SESSION['WarExp'] = $totalWarExp;
?>


