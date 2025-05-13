<?php

    //database connection
    session_start();
    $conn=new mysqli('localhost','root','','studentdatabase');
    if($conn->connect_error) {
        die("connection failed: " . $conn->connect_error);
    }
    // else{
    //     echo "database connection";
    // }


    $roll_number=$_POST['roll_number'];
    $password=$_POST['password'];

    $check=mysqli_query($conn, "SELECT * FROM studentsregistration WHERE roll_number = '$roll_number' AND password = '$password'");
    
    if (mysqli_num_rows($check)>0) {
        $voterdata = mysqli_fetch_array($check);
        $_SESSION['voterdata']=$voterdata;

        echo '

            <script>
                location="../Dashboard/dashboard.php";
                // alert("Login Successful");
                
            </script>
        ';
    }
    else{
        echo '
        <script>
            alert("Incorrect Roll-Number or Password");
            location="index.html";
        </script>
        ';
    }

?>