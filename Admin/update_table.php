<?php
    $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "ALTER TABLE addcandidate ADD COLUMN description TEXT";

    if (mysqli_query($conn, $sql)) {
        echo "Column 'description' added successfully";
    } else {
        echo "Error adding column: " . mysqli_error($conn);
    }

    mysqli_close($conn);
?> 