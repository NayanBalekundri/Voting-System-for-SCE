<?php
  session_start();
  $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

  // Check if user is logged in as admin
  if (!isset($_SESSION['adminlogin'])) {
      echo '<script>
              alert("Please login as admin to delete candidates");
              location = "AdminLogin.php";
            </script>';
      exit();
  }

  // Get candidate ID from URL
  $candidate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  if ($candidate_id <= 0) {
      echo '<script>
              alert("Invalid candidate ID");
              location = "AdminDashboard.php";
            </script>';
      exit();
  }

  // Get candidate photo before deletion
  $photo_query = mysqli_query($conn, "SELECT photo FROM addcandidate WHERE id = '$candidate_id'");
  $photo = mysqli_fetch_assoc($photo_query)['photo'];

  // Start transaction
  mysqli_begin_transaction($conn);

  try {
      // Delete candidate positions
      mysqli_query($conn, "DELETE FROM candidate_positions WHERE candidate_id = '$candidate_id'");
      
      // Delete candidate
      mysqli_query($conn, "DELETE FROM addcandidate WHERE id = '$candidate_id'");
      
      // Delete photo file if exists
      if ($photo && file_exists("Image/$photo")) {
          unlink("Image/$photo");
      }
      
      mysqli_commit($conn);
      echo '<script>
              alert("Candidate deleted successfully");
              location = "AdminDashboard.php";
            </script>';
  } catch (Exception $e) {
      mysqli_rollback($conn);
      echo '<script>
              alert("Error deleting candidate: ' . $e->getMessage() . '");
              location = "AdminDashboard.php";
            </script>';
  }
?> 