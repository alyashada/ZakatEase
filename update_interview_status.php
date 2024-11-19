<?php
session_start();
require_once 'dbconn.php';

// Check if the user is logged in and is an interviewer
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

// Validate input and ensure it's not empty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['interview_status'])) {
    $application_id = $_POST['application_id'];
    $interview_status = $_POST['interview_status'];

    // Prepare and execute the SQL statement
    $query = "UPDATE interview_details SET interview_status = ? WHERE application_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("ss", $interview_status, $application_id);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            echo 'Success';
        } else {
            echo 'Error updating interview status';
        }

        // Close the statement
        $stmt->close();
    } else {
        echo 'Error preparing statement';
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle cases where the required parameters are not provided
    echo 'Missing application ID or interview status';
}
?>
