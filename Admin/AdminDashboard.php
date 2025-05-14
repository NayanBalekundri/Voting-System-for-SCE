<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['adminlogin']) || $_SESSION['adminlogin'] !== true) {
	echo '<script>
				alert("Please login as admin to access this page");
				location = "../Student-Login/index.html";
			</script>';
	exit();
}

include '../connect.php';

$query = "SELECT c.*, GROUP_CONCAT(cp.position) as positions 
			FROM addcandidate c 
			LEFT JOIN candidate_positions cp ON c.id = cp.candidate_id 
			GROUP BY c.id";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Dashboard</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>

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

		.table-bordered {
			border: 1px solid #dee2e6;
		}

		.table-bordered th,
		.table-bordered td {
			border: 1px solid #dee2e6;
			padding: 8px;
			vertical-align: middle;
		}

		.table-bordered thead th {
			background-color: #f8f9fa;
			border-bottom-width: 2px;
		}

		/* Fixed header and navbar styles */
		.header-container {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 1000;
		}

		.navbar {
			position: fixed;
			top: 60px;
			left: 0;
			right: 0;
			z-index: 999;
		}

		body {
			padding-top: 120px;
		}

		.navbar-nav {
			padding-left: 170px;
		}

		.candidate-image {
			max-width: 100%;
			height: auto;
			object-fit: cover;
			border-radius: 5px;
		}

		.candidate-image-container {
			width: 100%;
			max-width: 200px;
			margin: 0 auto;
		}

		/* Responsive styles */
		@media (max-width: 992px) {
			.navbar-nav {
				padding-left: 0;
			}

			.container-fluid {
				padding: 20px;
			}

			.col-sm-8 h2 {
				margin-left: 0 !important;
				text-align: center;
			}

			.candidate-image-container {
				max-width: 150px;
			}
		}

		@media (max-width: 768px) {
			body {
				padding-top: 100px;
			}

			.navbar {
				top: 50px;
			}

			.table-responsive {
				overflow-x: auto;
			}

			.table th,
			.table td {
				white-space: nowrap;
				min-width: 100px;
			}

			.candidate-image-container {
				max-width: 120px;
			}

			.btn-sm {
				padding: 0.25rem 0.5rem;
				font-size: 0.75rem;
			}
		}

		@media (max-width: 576px) {
			body {
				padding-top: 90px;
			}

			.navbar {
				top: 40px;
			}

			.header-container h1 {
				font-size: 1.5rem;
			}

			.candidate-image-container {
				max-width: 100px;
			}

			.table th,
			.table td {
				padding: 4px;
				font-size: 0.9rem;
			}

			.btn-sm {
				padding: 0.2rem 0.4rem;
				font-size: 0.7rem;
			}
		}

		/* Form styles */
		.form-control {
			margin-bottom: 1rem;
		}

		select[name="positions[]"] {
			width: 100%;
			padding: 0.375rem 0.75rem;
			border: 1px solid #ced4da;
			border-radius: 0.25rem;
			margin-bottom: 1rem;
		}
		
	</style>
</head>

<body>
	<div class="header-container">
		<ul class="nav justify-content-center bg-dark" style="padding: 10px;">
			<li class="nav-item">
				<h1 style="font-family: sans;color: lawngreen;margin: 0;">Online Voting System</h1>
			</li>
		</ul>
		<nav class="navbar navbar-expand-lg bg-light">
			<div class="container-fluid">
				<a class="navbar-brand" href="#" style="cursor:auto"> <img src="images/Admin.png" width="20%"> <b
						style="color: darkcyan;">Admin Panel</b> </a>
				<button class="navbar-toggler navbar-light" type="button" data-bs-toggle="collapse"
					data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
					aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse " id="navbarNav">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="#Add Candidate">Add Candidate</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="#Total">Total Candidate</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="results.php">View Results</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="adminlogout.php">Logout</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</div>

	<hr style="border: 3px solid black;">
	<br>
	<div class="container-fluid" id="Add Candidate" style="padding: 40px;">
		<div class="row">
			<div class="col-sm-8">
				<h2 style="text-align: center; margin-left: 500px;"> <span
						style="background: mediumblue;color: whitesmoke;padding: 10px;border-radius: 10px;">Add
						Candidate for Election</span> </h2>
				<br><br><br>
				<hr /><br />
				<div class="row">
					<div class="col-sm-6">
						<form action="AddCanddiate.php" method="post" enctype="multipart/form-data">
							<div class="mb-3">
								<label for="exampleInputEmail1" class="form-label">Candidate Name</label>
								<input type="text" class="form-control" id="exampleInputEmail1"
									aria-describedby="emailHelp" name="cname" required />
							</div>
							<div class="mb-3">
								<label for="exampleInputEmail1" class="form-label">Roll number</label>
								<input type="text" class="form-control" id="InputEmail1" aria-describedby="emailHelp"
									placeholder="Enter Your roll number" name="roll_number" required />
							</div>
							<div class="mb-3">
								<label>Positions:</label>
								<select name="positions[]" required>
									<option value="GS">General Secretary (GS)</option>
									<option value="LR">Literary Representative (LR)</option>
									<option value="Sports Secretary">Sports Secretary</option>
									<option value="Cultural Activity">Cultural Activity</option>
									<option value="Other Activity">Other Activity</option>
								</select>
							</div>
					</div>
					<div class="col-sm-6">
						<div class="mb-3">
							<label for="exampleInputPassword1" class="form-label">Select Photo</label>
							<input type="file" class="form-control" id="exampleInputPassword1" name="photo" required />
						</div>
						<div class="mb-3">
							<label for="exampleInputPassword1" class="form-label">Discription</label>
							<input type="text" class="form-control" id="InputPassword1" name="description" />
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
			<div class="col-sm-4"><br><br><br><br><br>
				<img src="images/header.jfif" width="100%" />
			</div>
		</div>
	</div>

	<br><br><br>
	<hr style="border: 3px solid black;">
	<br><br>

	<div class="container" id="Total">
		<div class="row justify-content-center">
			<div class="col-sm-10">
				<h2 style="text-align: center;"> <span
						style="background: mediumblue;color: whitesmoke;padding: 10px;border-radius: 10px;">Total List
						of Candidate</span> </h2><br><br>
				<table class="table table-bordered">
					<thead style="text-align: center;">
						<tr>
							<th>Candidate Name</th>
							<th>Position</th>
							<th>Roll Number</th>
							<th>Photo</th>
							<th>Action</th>
						</tr>
					</thead>
					<?php

					while ($row = mysqli_fetch_assoc($result)) {
						$candidate_id = $row['id'];
						$positions_query = mysqli_query($conn, "SELECT position FROM candidate_positions WHERE candidate_id = '$candidate_id'");
						$positions = [];
						while ($pos = mysqli_fetch_assoc($positions_query)) {
							$positions[] = $pos['position'];
						}
						?>
						<tbody style="text-align: center;">
							<tr>
								<td><?php echo $row['cname']; ?></td>
								<td><?php echo implode(', ', $positions); ?></td>
								<td><?php echo $row['roll_number']; ?></td>
								<td>
									<div class="candidate-image-container">
										<img src="images/<?php echo $row['photo']; ?>" class="candidate-image"
											alt="Candidate Photo">
									</div>
								</td>
								<td>
									<a href="edit_candidate.php?id=<?php echo $candidate_id; ?>"
										class="btn btn-primary btn-sm">Edit</a>
									<a href="delete_candidate.php?id=<?php echo $candidate_id; ?>"
										class="btn btn-danger btn-sm"
										onclick="return confirm('Are you sure you want to delete this candidate?')">Delete</a>
								</td>
							</tr>

							<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</body>

</html>