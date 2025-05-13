<?php
$conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_number = $_POST['roll_number'] ?? null;
    $new_password = $_POST['password'] ?? null;
    $confirm_password = $_POST['cpassword'] ?? null;

    if (!$roll_number || !$new_password || !$confirm_password) {
        echo '
        <script>
            alert("All fields are required!"); 
            location="reset_password.php";
        </script>';
        exit();
    }

    if ($new_password != $confirm_password) {
        echo '
        <script>
            alert("Passwords do not match!"); 
            location="reset_password.php";
        </script>';
        exit();
    }

    // Update password
    $update_query = "UPDATE studentsregistration SET password = ?, cpassword = ? WHERE roll_number = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sss", $new_password, $confirm_password, $roll_number);

    if ($stmt->execute()) {
        echo '<script>alert("Password Reset Successful!"); location="index.html";</script>';
    } else {
        echo '<script>alert("Error! Try again."); location="reset_password.php";</script>';
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
</head>
<style>
    .container {
        height: 400px;
        width: 450px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        
        margin:100px auto;
        padding: 0 30px 20px 30px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.9);
    }
    p label{
        font-size:1.4rem;
        /* font-weight: bold; */
    }

    p input{
        width: 94%;
        padding: 10px;
        border: 2px solid #cccc;
        border-radius: 4px;
        font-size: 1rem;
        margin-top: 4px ;    
    }
    h2{
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .reset{
        background-color: #3498db;
        color: white;
        padding: 12px 20px;
        border: none;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        border-radius: 5px;
    }
    .reset:hover{
        background-color: #083350;
    }
</style>

<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form id="resetForm" action="reset_password.php" method="post">
            <input type="hidden" name="roll_number" value="<?php echo $_GET['roll_number']; ?>"/>
            <p>
                <label>New Password</label>
                <input type="password" id="password" name="password" placeholder="ENter New Password" required />
            </p>
            <p>
                <label>Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" placeholder="Enter Comform Password" required />
            </p>
            <small style="color: red; font-size: 0.9rem;">
                Password must be at least 8 characters and include uppercase, lowercase, number, 
                and special character.
            </small>
            <p>
                <input type="submit" value="Reset Password" class="reset" />
            </p>
            
        </form>
    </div>
    
    <script>
    document.getElementById('resetForm').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const cpassword = document.getElementById('cpassword').value;

        const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!strongPasswordRegex.test(password)) {
            alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
            e.preventDefault();
        } else if (password !== cpassword) {
            alert("Passwords do not match.");
            e.preventDefault();
        }
    });
</script>
</body>

</html>