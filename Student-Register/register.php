<?php
$conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? null;
    $roll_number = $_POST['roll_number'] ?? null;
    $email = $_POST['email'] ?? null;
    $mobile = $_POST['mobile'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $image = $_FILES['photo']['name'] ?? null;
    $tmp_name = $_FILES['photo']['tmp_name'] ?? null;
    $password = $_POST['password'] ?? null;
    $cpassword = $_POST['cpassword'] ?? null;

    if (!$name || !$roll_number || !$gender || !$image || !$password || !$cpassword) {
        echo '
        <script>
            alert("All fields are required.");
            location = "register.html";
        </script>';
        exit();
    }

    if ($password != $cpassword) {
        echo '
        <script>
            alert("Password and Confirm Password Do Not Match!!");
            location = "register.html";
        </script>';
        exit();
    }

    // Check if the roll number already exists
    $check_query = "SELECT * FROM studentsregistration WHERE roll_number = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $roll_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '
        <script>
            alert("Error: Roll number already exists. Please enter a correct roll number");
            location = "register.html";
        </script>';
    } else {
        move_uploaded_file($tmp_name, "../VoterImg/$image");

        $insert_query = "INSERT INTO studentsregistration(name, roll_number, email,mobile, gender, photo, password, cpassword, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssssssss", $name, $roll_number,$email, $mobile, $gender, $image, $password, $cpassword);

        if ($stmt->execute()) {
            echo '
            <script>
                alert("Form Submitted Successfully");
                location = "../Student-Login/index.html";
            </script>';
        } else {
            echo '
            <script>
                alert("Error: Could not register. Please try again.");
                location = "register.html";
            </script>';
        }
    }

    $stmt->close();
}
$conn->close();
?>
