<?php
session_start();
include 'dbconn.php';

// Function to retrieve parent details for a given user ID
function getParentDetails($userId, $conn) {
    // Fetch parent details for the current user
    $sql_fetch_parent_details = "SELECT * FROM parent_details WHERE application_id = (
        SELECT id FROM applicant_information WHERE user_id = ?
    )";
    $stmt = mysqli_prepare($conn, $sql_fetch_parent_details);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

// Retrieve the user's ID from the session or any other source
$userId = $_SESSION['user_id']; // Assuming the user's ID is stored in the session

// Retrieve parent details for the current user
$parentDetails = getParentDetails($userId, $conn);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fatherName = $_POST["fatherName"];
    $fatherRelationship = $_POST["fatherRelationship"];
    $fatherStatus = $_POST["fatherStatus"];
    $fatherPassedAwayDate = !empty($_POST["fatherPassedAwayDate"]) ? $_POST["fatherPassedAwayDate"] : '0000-00-00';
    $fatherAge = $_POST["fatherAge"];
    $fatherOccupation = $_POST["fatherOccupation"];
    $fatherEmployer = $_POST["fatherEmployer"];
    $fatherPhoneNo = $_POST["fatherPhoneNo"];
    $fatherHealthInfo = $_POST["fatherHealthInfo"];

    $motherName = $_POST["motherName"];
    $motherRelationship = $_POST["motherRelationship"];
    $motherStatus = $_POST["motherStatus"];
    $motherPassedAwayDate = !empty($_POST["motherPassedAwayDate"]) ? $_POST["motherPassedAwayDate"] : '0000-00-00';
    $motherAge = $_POST["motherAge"];
    $motherOccupation = $_POST["motherOccupation"];
    $motherEmployer = $_POST["motherEmployer"];
    $motherPhoneNo = $_POST["motherPhoneNo"];
    $motherHealthInfo = $_POST["motherHealthInfo"];

    $otherInfo = $_POST["otherInfo"];
    
    // Validate and format income fields
    $fatherIncome = !empty($_POST["fatherIncome"]) ? floatval($_POST["fatherIncome"]) : 0.0;
    $motherIncome = !empty($_POST["motherIncome"]) ? floatval($_POST["motherIncome"]) : 0.0;
    $otherIncome = !empty($_POST["otherIncome"]) ? floatval($_POST["otherIncome"]) : 0.0;
    $totalIncome = $fatherIncome + $motherIncome + $otherIncome;

    // Retrieve the application ID from the applicant_information table based on the user's ID
    $sql_get_application_id = "SELECT id FROM applicant_information WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql_get_application_id);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the application ID is retrieved successfully
    if ($row = mysqli_fetch_assoc($result)) {
        $applicationId = $row['id'];

        // Check if record exists for the current application
        $sql_check_existence = "SELECT * FROM parent_details WHERE application_id = ?";
        $stmt = mysqli_prepare($conn, $sql_check_existence);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $result_existence = mysqli_stmt_get_result($stmt);

        // Perform insert or update based on existence of record
        if (mysqli_num_rows($result_existence) > 0) {
            // Update existing record
            $sql = "UPDATE parent_details SET father_name=?, father_relationship=?, father_status=?, father_passed_away_date=?, father_age=?, father_occupation=?, father_employer=?, father_phone_no=?, father_health_info=?, mother_name=?, mother_relationship=?, mother_status=?, mother_passed_away_date=?, mother_age=?, mother_occupation=?, mother_employer=?, mother_phone_no=?, mother_health_info=?, other_info=?, father_income=?, mother_income=?, other_income=?, total_income=? WHERE application_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssissssssssisssssssssi', $fatherName, $fatherRelationship, $fatherStatus, $fatherPassedAwayDate, $fatherAge, $fatherOccupation, $fatherEmployer, $fatherPhoneNo, $fatherHealthInfo, $motherName, $motherRelationship, $motherStatus, $motherPassedAwayDate, $motherAge, $motherOccupation, $motherEmployer, $motherPhoneNo, $motherHealthInfo, $otherInfo, $fatherIncome, $motherIncome, $otherIncome, $totalIncome, $applicationId);
        } else {
            // Insert new record
            $sql = "INSERT INTO parent_details (application_id, father_name, father_relationship, father_status, father_passed_away_date, father_age, father_occupation, father_employer, father_phone_no, father_health_info, mother_name, mother_relationship, mother_status, mother_passed_away_date, mother_age, mother_occupation, mother_employer, mother_phone_no, mother_health_info, other_info, father_income, mother_income, other_income, total_income) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'issssissssssssisssssssss', $applicationId, $fatherName, $fatherRelationship, $fatherStatus, $fatherPassedAwayDate, $fatherAge, $fatherOccupation, $fatherEmployer, $fatherPhoneNo, $fatherHealthInfo, $motherName, $motherRelationship, $motherStatus, $motherPassedAwayDate, $motherAge, $motherOccupation, $motherEmployer, $motherPhoneNo, $motherHealthInfo, $otherInfo, $fatherIncome, $motherIncome, $otherIncome, $totalIncome);
        }

        // Execute SQL query
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the next page with a success message
            header("Location: zakatappstuuu.php?page=form-page-6&success=" . urlencode("User data updated successfully!"));
            exit();
        } else {
            // Redirect to the current page with an error message
            header("Location: zakatappstuuu.php?page=form-page-6&error=" . urlencode("Error updating data: " . mysqli_error($conn)));
            exit();
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        // Redirect to the current page with an error message
        header("Location: zakatappstuuu.php?page=form-page-6&error=" . urlencode("No application found for the user."));
        exit();
    }

    // Close database connection
    mysqli_close($conn);
}
?>
