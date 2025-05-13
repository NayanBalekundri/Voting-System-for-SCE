<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT * FROM adminlogin WHERE name='$name' AND password='$password'");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['adminlogin'] = true;
        $_SESSION['admin_name'] = $name;
        echo '<script>
                location = "AdminDashboard.php";
              </script>';
    } else {
        echo '<script>
                alert("Incorrect information! You are not an Admin");
                location = "../Student-Login/index.html";
              </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.9);
            width: 100%;
            max-width: 500px;
            height: 400px;
            
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            font-size: 24px;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .btn:hover{
            background-color: #083350;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
        </div>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" class="form-control" name="name" placeholder="Username" required>
            </div><br>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div><br>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="back-link">
            <a href="../Student-Login/index.html">Back to Student Login</a>
        </div>
    </div>
</body>
</html> 