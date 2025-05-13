<?php
    session_start();

    $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

    $name = $_POST['name'];
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT * FROM adminlogin WHERE name='$name' AND password='$password' ");

    if (mysqli_num_rows($check)>0) {
        $_SESSION['adminlogin'] = true;
        $_SESSION['admin_name'] = $name;
        echo '
            <script>
                location = "AdminDashboard.php";
            </script>
        ';
    }
    else{
        echo '
            <script>
                alert("Incorrect information !! You are not Admin ");
                location = "../dashboard.php";
            </script>
        ';
    }
?>