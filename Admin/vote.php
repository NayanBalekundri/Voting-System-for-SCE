<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $votes = isset($_POST['gvotes']) ? (int)$_POST['gvotes'] : null;
        $gid = isset($_POST['gid']) ? (int)$_POST['gid'] : null;
        $position = isset($_POST['position']) ? $_POST['position'] : null;
        $uid = isset($_SESSION['voterdata']['id']) ? (int)$_SESSION['voterdata']['id'] : null;

        if ($votes === null || $gid === null || $uid === null || $position === null) {
            echo '<script>
                    alert("Error: Missing data. Please try again.");
                    location="../Dashboard/dashboard.php";
                </script>';
            exit();
        }

        // Check if user has already voted for this position
        $check_vote = mysqli_query($conn, "SELECT * FROM votes WHERE voter_id = '$uid' AND position = '$position'");
        if (mysqli_num_rows($check_vote) > 0) {
            echo '<script>
                    alert("You have already voted for this position!");
                    location="../Dashboard/dashboard.php";
                </script>';
            exit();
        }

        $total_votes = $votes + 1;

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Update candidate votes
            $update_votes = mysqli_query($conn, "UPDATE addcandidate SET votes ='$total_votes' WHERE id = '$gid'");
            
            // Record the vote
            $record_vote = mysqli_query($conn, "INSERT INTO votes (voter_id, position) VALUES('$uid', '$position')");
            
            // Update user status if they've voted for all positions
            $all_positions = mysqli_query($conn, "SELECT DISTINCT position FROM candidate_positions");
            $voted_positions = mysqli_query($conn, "SELECT position FROM votes WHERE voter_id = '$uid'");
            
            if (mysqli_num_rows($voted_positions) >= mysqli_num_rows($all_positions)) {
                mysqli_query($conn, "UPDATE studentsregistration SET status = 1 WHERE id = '$uid'");
                $_SESSION['voterdata']['status'] = 1;
            }

            if ($update_votes && $record_vote) {
                mysqli_commit($conn);
                echo '<script>
                        alert("Voting Successful");
                        location="../Dashboard/dashboard.php";
                    </script>';
            } else {
                throw new Exception("Error updating votes");
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo '<script>
                    alert("Some Error Occurred. Please try again.");
                    location="../Dashboard/dashboard.php";
                </script>';
        }
    } else {
        echo '<script>
                alert("Invalid Request: Please submit the form properly.");
                location="../Dashboard/dashboard.php";
            </script>';
    }
?>
