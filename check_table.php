<?php
require_once 'includes/db_connect.php';
$result = mysqli_query($conn, "SHOW TABLES LIKE 'Reread_History'");
if (mysqli_num_rows($result) == 0) {
    echo "Reread_History table does not exist\n";
} else {
    echo "Reread_History table exists\n";
    $columns = mysqli_query($conn, "DESCRIBE Reread_History");
    echo "Columns in Reread_History table:\n";
    while ($row = mysqli_fetch_assoc($columns)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
    }
}
?>
