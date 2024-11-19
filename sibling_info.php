<?php
// sibling_info.php

// Start session
session_start();

include_once 'dbconn.php';

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$application_id = "";
// Fetch application_id using user_id
$application_check_sql = "SELECT id FROM applicant_information WHERE user_id = '$user_id'";
$application_check_result = mysqli_query($conn, $application_check_sql);

if (mysqli_num_rows($application_check_result) > 0) {
    $row = mysqli_fetch_assoc($application_check_result);
    $application_id = $row['id'];
} 


// Check if form is submitted to add new sibling
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit']) && !isset($_POST['delete']) && !isset($_POST['update'])) {
    // Retrieve form data
    $name = $_POST['siblingName'];
    $relationship = $_POST['siblingRelationship'];
    $age = $_POST['siblingAge'];
    $working_status = $_POST['siblingWorkingStatus'];
    $marital_status = $_POST['siblingMaritalStatus'];
    $position = $_POST['siblingPosition'];
    $employer = $_POST['siblingEmployer'];
    $additional_info = $_POST['siblingAdditionalInfo'];

    // Insert data into database
    $sql = "INSERT INTO sibling_details (application_id, name, relationship, age, working_status, marital_status, position, employer, add_info) 
            VALUES ('$application_id', '$name', '$relationship', '$age', '$working_status', '$marital_status', '$position', '$employer', '$additional_info')";
if (mysqli_query($conn, $sql)) {
    header("Location: zakatappstuuu.php?page=form-page-8&success=" . urlencode("Sibling information added successfully."));
        exit();
} else {
    header("Location: zakatappstuuu.php?page=form-page-8&error=" . urlencode("Error: " . $conn->error));
        exit();
}
exit();
    
}

// Check if form is submitted to delete a sibling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $sibling_id = $_POST['sibling_id'];

    // Delete data from database
    $sql = "DELETE FROM sibling_details WHERE id='$sibling_id' AND application_id='$application_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: zakatappstuuu.php?page=form-page-8&success=" . urlencode("Sibling information deleted successfully."));
        exit();
    } else {
        header("Location: zakatappstuuu.php?page=form-page-8&error=" . urlencode("Error: " . $conn->error));
        exit();
    }
    exit();
    
}

// Check if form is submitted to edit a sibling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $sibling_id = $_POST['sibling_id'];

    // Retrieve the sibling's data
    $sql = "SELECT * FROM sibling_details WHERE id='$sibling_id' AND application_id='$application_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $sibling = mysqli_fetch_assoc($result);
        echo json_encode($sibling);
    } else {
        header("Location: zakatappstuuu.php?page=form-page-8&error=" . urlencode("Sibling not found."));
        exit();
    }
    exit();
}

// Update sibling information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $sibling_id = $_POST['sibling_id'];
    $name = $_POST['siblingName'];
    $relationship = $_POST['siblingRelationship'];
    $age = $_POST['siblingAge'];
    $working_status = $_POST['siblingWorkingStatus'];
    $marital_status = $_POST['siblingMaritalStatus'];
    $position = $_POST['siblingPosition'];
    $employer = $_POST['siblingEmployer'];
    $additional_info = $_POST['siblingAdditionalInfo'];

    $sql = "UPDATE sibling_details 
            SET name='$name', relationship='$relationship', age='$age', working_status='$working_status', marital_status='$marital_status', position='$position', employer='$employer', add_info='$additional_info' 
            WHERE id='$sibling_id' AND application_id='$application_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: zakatappstuuu.php?page=form-page-8&success=" . urlencode("Sibling information updated successfully."));
        exit();
    } else {
        header("Location: zakatappstuuu.php?page=form-page-8&error=" . urlencode("Error: " . $conn->error));
        exit();
    }
    exit();
    
}

// Fetch sibling details to display in the table
$sibling_details_sql = "SELECT * FROM sibling_details WHERE application_id = '$application_id'";
$sibling_details_result = mysqli_query($conn, $sibling_details_sql);

?>