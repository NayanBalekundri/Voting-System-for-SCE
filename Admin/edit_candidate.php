<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

    // Check if user is logged in as admin
    if (!isset($_SESSION['adminlogin'])) {
        echo '<script>
                    alert("Please login as admin to edit candidates");
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

    // Get candidate details
    $candidate_query = mysqli_query($conn, "SELECT * FROM addcandidate WHERE id = '$candidate_id'");
    $candidate = mysqli_fetch_assoc($candidate_query);

    if (!$candidate) {
        echo '<script>
                    alert("Candidate not found");
                    location = "AdminDashboard.php";
                </script>';
        exit();
    }

    // Get candidate positions
    $positions_query = mysqli_query($conn, "SELECT position FROM candidate_positions WHERE candidate_id = '$candidate_id'");
    $selected_positions = [];
    while ($pos = mysqli_fetch_assoc($positions_query)) {
        $selected_positions[] = $pos['position'];
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cname = $_POST['cname'];
        $positions = $_POST['positions'];
        $description = $_POST['description'];

        // Handle photo upload
        $photo = $candidate['photo']; // Keep existing photo by default
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photo = $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], "images/$photo");
        }

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Update candidate details
            mysqli_query($conn, "UPDATE addcandidate SET cname = '$cname', description = '$description', photo = '$photo' WHERE id = '$candidate_id'");

            // Delete existing positions
            mysqli_query($conn, "DELETE FROM candidate_positions WHERE candidate_id = '$candidate_id'");

            // Insert new positions
            foreach ($positions as $position) {
                mysqli_query($conn, "INSERT INTO candidate_positions (candidate_id, position) VALUES('$candidate_id', '$position')");
            }

            mysqli_commit($conn);
            echo '<script>
                        alert("Candidate updated successfully");
                        location = "AdminDashboard.php";
                    </script>';
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error updating candidate: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Candidate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .nav-item a {
            font-family: sans-serif;
            color: mediumblue;
        }

        .nav-item a:hover {
            background: red;
            color: white;
            border-radius: 7px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Edit Candidate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="AdminDashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Edit Candidate</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>

                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Candidate Name</label>
                                <input type="text" class="form-control" name="cname" value="<?php echo $candidate['cname']; ?>" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Positions</label>
                                <select name="positions[]" class="form-control" multiple required>
                                    <option value="GS" <?php echo in_array('GS', $selected_positions) ? 'selected' : ''; ?>>General Secretary (GS)</option>
                                    <option value="LR" <?php echo in_array('LR', $selected_positions) ? 'selected' : ''; ?>>Literary Representative (LR)</option>
                                    <option value="Sports Secretary" <?php echo in_array('Sports Secretary', $selected_positions) ? 'selected' : ''; ?>>Sports Secretary</option>
                                    <option value="Cultural Activity" <?php echo in_array('Cultural Activity', $selected_positions) ? 'selected' : ''; ?>>Cultural Activity</option>
                                    <option value="Other Activity" <?php echo in_array('Other Activity', $selected_positions) ? 'selected' : ''; ?>>Other Activity</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple positions</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"><?php echo $candidate['description']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Photo</label>
                                <input type="file" class="form-control" name="photo" />
                                <small class="text-muted">Leave empty to keep current photo</small>
                                <?php if ($candidate['photo']) { ?>
                                    <div class="mt-2">
                                        <img src="images/<?php echo $candidate['photo']; ?>" width="200" />
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Update Candidate</button>
                                <a href="AdminDashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>