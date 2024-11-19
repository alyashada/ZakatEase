<?php
// Include the database connection file
require_once 'dbconn.php';

// Check if form data has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $id = $_POST['id'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $fullName = $_POST['fullName'];
    $mobileNumber = $_POST['mobileNumber'];
    $addressLine1 = $_POST['addressLine1'];
    $addressLine2 = $_POST['addressLine2'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];

    // Update the student's information in the database
    $query = "UPDATE registered_user SET email = ?, username = ?, full_name = ?, mobile_number = ?, address_line_1 = ?, address_line_2 = ?, postcode = ?, state = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    // Bind the parameters
    $stmt->bind_param('ssssssssi', $email, $username, $fullName, $mobileNumber, $addressLine1, $addressLine2, $postcode, $state, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the main page after editing with success message
    // After updating the student information successfully
   // Assuming the update is successful
    $successMessage = "Student information successfully updated.";
    header("Location: userliststu.php?success=" . urlencode($successMessage));
    exit();


}

// Close the database connection
mysqli_close($conn);
?>
