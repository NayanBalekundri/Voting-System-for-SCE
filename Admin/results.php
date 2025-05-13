<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

    // Check if user is logged in as admin
    if (!isset($_SESSION['adminlogin'])) {
        echo '<script>
                    alert("Please login as admin to view results");
                    location = "admin_login.php";
                </script>';
        exit();
    }

    $positions = ['GS', 'LR', 'Sports Secretary', 'Cultural Activity', 'Other Activity'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Election Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
        }

        .nav-item a {
            font-family: sans-serif;
            color: mediumblue;
        }

        .nav-item a:hover {
            background: red;
            color: white;
            border-radius: 7px;
        }

        .result-card {
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .winner {
            background-color: #e8f5e9;
        }

        .runner-up {
            background-color: #fff3e0;
        }

        .position-title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .container {
            padding-top: 80px;
            max-width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }

        .navbar-brand {
            cursor: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table th, .table td {
            padding: 12px 8px;
            vertical-align: middle;
        }

        .badge {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding-top: 70px;
            }

            .position-title {
                font-size: 1.5rem;
            }

            .table th, .table td {
                padding: 8px 4px;
                font-size: 0.9rem;
            }

            .badge {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding-top: 60px;
            }

            .position-title {
                font-size: 1.2rem;
            }

            .table th, .table td {
                padding: 6px 3px;
                font-size: 0.85rem;
            }

            .badge {
                padding: 4px 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Election Results</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="AdminDashboard.php">Dashboard</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="adminlogout.php">Logout</a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Election Results</h2>

        <?php foreach ($positions as $position) {
            // Get candidates for this position
            $query = "SELECT ac.* FROM addcandidate ac 
                      INNER JOIN candidate_positions cp ON ac.id = cp.candidate_id 
                      WHERE cp.position = '$position' ORDER BY ac.votes DESC";
            $result = mysqli_query($conn, $query);
            $candidates = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if (!empty($candidates)) {
                $max_votes = $candidates[0]['votes'];
                $total_candidates = count($candidates);
                
                // Get runner-up votes (second highest)
                $runner_up_votes = 0;
                if ($total_candidates > 1) {
                    $runner_up_votes = $candidates[1]['votes'];
                }
        ?>
                <div class="card result-card">
                    <div class="card-header">
                        <h3 class="position-title text-center"><?php echo $position; ?></h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Candidate Name</th>
                                    <th>Votes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidates as $index => $candidate) {
                                    $is_winner = false;
                                    $is_runner_up = false;
                                    
                                    if ($total_candidates === 1) {
                                        $is_winner = true;
                                    } else {
                                        if ($candidate['votes'] == $max_votes) {
                                            $is_winner = true;
                                        } elseif ($candidate['votes'] == $runner_up_votes) {
                                            $is_runner_up = true;
                                        }
                                    }
                                    
                                    $row_class = '';
                                    if ($is_winner) {
                                        $row_class = 'winner';
                                    } elseif ($is_runner_up) {
                                        $row_class = 'runner-up';
                                    }
                                ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td><?php echo $candidate['cname']; ?></td>
                                        <td><?php echo $candidate['votes']; ?></td>
                                        <td>
                                            <?php if ($is_winner) { ?>
                                                <span class="badge bg-success">Winner</span>
                                            <?php } elseif ($is_runner_up) { ?>
                                                <span class="badge bg-warning text-dark">Runner-up</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Participant</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php
            }
        } ?>
    </div>

</body>

</html>