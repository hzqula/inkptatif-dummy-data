<?php
$servername = "localhost";
$username = "root";
$password = "@IlooqstrasiHZ0113";
$dbname = "inkptatif_v4";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

// Get all table names from the database
$tables = $conn->query("SHOW TABLES");

if ($tables->num_rows > 0) {
    while ($table = $tables->fetch_array()) {
        $tableName = $table[0];
        echo "<h2>Table: $tableName</h2>";

        // Get the table's columns and data
        $result = $conn->query("SELECT * FROM $tableName");

        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            // Output table headers
            echo "<tr>";
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr>";

            // Output table rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $data) {
                    echo "<td>" . htmlentities($data) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data found in the table.<br>";
        }
    }
} else {
    echo "No tables found in the database.";
}

$conn->close();
?>