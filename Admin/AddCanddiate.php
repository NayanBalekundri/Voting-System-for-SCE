<?php

	
	$conn = mysqli_connect('localhost', 'root', '', 'studentdatabase');

	$cname = $_POST['cname'];
	$roll_number = $_POST['roll_number'];
	$positions = $_POST['positions']; // This will be an array of selected positions
	$description = $_POST['description'];

	$image = $_FILES['photo']['name'];
	$tmp_name1 = $_FILES['photo']['tmp_name'];
	
	// Check if the roll number already exists
	$check_roll = mysqli_query($conn, "SELECT * FROM addcandidate WHERE roll_number = '$roll_number'");

	if (mysqli_num_rows($check_roll) > 0) {
		echo '<script>
					alert("Roll Number already exists!");
					location = "AdminDashboard.php #Add Candidate"; // Redirect back to add candidate page
				</script>';
	} else {
		
	// First insert the candidate
	$insert = mysqli_query($conn, "INSERT INTO addcandidate (cname, roll_number, description, photo)
	VALUES('$cname', '$roll_number', '$description', '$image')");

	if ($insert) {
		$candidate_id = mysqli_insert_id($conn);
		
		// Insert each selected position
		foreach ($positions as $position) {
			mysqli_query($conn, "INSERT INTO candidate_positions (candidate_id, position) VALUES('$candidate_id', '$position')");
		}
		
		// Move the uploaded photo to the images directory
		move_uploaded_file($tmp_name1, "images/$image");
		
		echo '<script>
					alert("Candidate Added Successfully");
					location = "AdminDashboard.php #Add Candidate";
			  </script>';
	}
	else{
		echo "Some Error";
	}
}

?>