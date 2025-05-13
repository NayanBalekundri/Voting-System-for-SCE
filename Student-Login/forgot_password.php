<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');
$message = "";
$otp_sent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    $roll_number = $_POST['roll_number'] ?? null;
    $email = $_POST['email'] ?? null;

    if (!$roll_number || !$email) {
        $message = "Please enter both Roll Number and Email.";
    } else {
        $query = "SELECT * FROM studentsregistration WHERE roll_number = ? AND email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $roll_number, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['roll_number'] = $roll_number;
            $_SESSION['email'] = $email;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'akashmanolkar85@gmail.com';
                $mail->Password = 'irvatmaxmftfukfq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('election25@gmail.com', 'Election Team');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "OTP Verification for Password Reset";
                $mail->Body    = "<h2>Hello,</h2><p>Your OTP is:</p><h3>$otp</h3><p>Valid for 10 minutes.</p>";

                $mail->send();
                $otp_sent = true;
                $message = "OTP has been sent to your email!";
            } catch (Exception $e) {
                $message = "Failed to send OTP: " . $mail->ErrorInfo;
            }
        } else {
            $message = "Roll Number or Email is incorrect.";
        }
    }
}

if (isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];
    if ($user_otp == $_SESSION['otp']) {
        header("Location: reset_password.php?roll_number=" . $_SESSION['roll_number']);
        exit;
    } else {
        $message = "Incorrect OTP. Please try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            /* background: #ecf0f3; */
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.9);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="submit"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #083350;
        }
        .message {
            margin-top: 15px;
            text-align: center;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <form method="post">
        <label>Roll Number:</label>
        <input type="text" name="roll_number" value="<?= $_SESSION['roll_number'] ?? '' ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" required>

        <input type="submit" name="send_otp" value="Send OTP">
    </form>

    <?php if ($otp_sent || isset($_SESSION['otp'])): ?>
        <form method="post">
            <label>Enter OTP:</label>
            <input type="text" name="otp" required>
            <input type="submit" name="verify_otp" value="Verify OTP">
        </form>
    <?php endif; ?>

    <div class="message"><?= $message ?></div>
</div>
</body>
</html>
