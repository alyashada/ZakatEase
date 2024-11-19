<?php
session_start();
require_once 'dbconn.php';
require_once 'phpqrcode/qrlib.php'; // Include the QR code library

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    $interviewerId = filter_input(INPUT_POST, 'interviewer_id', FILTER_VALIDATE_INT);

    if ($userId && $action) {
        // Update the status in the registered_user table
        $updateQuery = "UPDATE registered_user SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $action, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($action === 'accept' && $interviewerId) {

                // Get the application ID associated with the user
                $applicationIdQuery = "SELECT id FROM applicant_information WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $applicationIdQuery);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'i', $userId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $applicationData = mysqli_fetch_assoc($result);
                    mysqli_stmt_close($stmt);

                    if ($applicationData) {
                        $applicationId = $applicationData['id'];

                        // Check if interview details already exist for the application
                        $checkQuery = "SELECT id FROM interview_details WHERE application_id = ?";
                        $stmt = mysqli_prepare($conn, $checkQuery);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, 'i', $applicationId);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $existingInterview = mysqli_fetch_assoc($result);
                            mysqli_stmt_close($stmt);

                            if ($existingInterview) {
                                // Update existing interview details
                                $updateQuery = "UPDATE interview_details SET interviewer_id = ?, qr_code_path = ? WHERE application_id = ?";
                            } else {
                                // Insert new interview details
                                $updateQuery = "INSERT INTO interview_details (application_id, interviewer_id, qr_code_path) VALUES (?, ?, ?)";
                            }

                            $stmt = mysqli_prepare($conn, $updateQuery);
                            if ($stmt) {
                                if ($existingInterview) {
                                    mysqli_stmt_bind_param($stmt, 'isi', $interviewerId, $qrFilePath, $applicationId);
                                } else {
                                    mysqli_stmt_bind_param($stmt, 'iis', $applicationId, $interviewerId, $qrFilePath);
                                }
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);

                                $successMessage = "Status updated and interviewer assigned successfully.";
                                header("Location: listapplication.php?success=" . urlencode($successMessage));
                                exit();
                            } else {
                                error_log("Error preparing statement for interview details: " . mysqli_error($conn));
                                header("Location: listapplication.php?error=Failed to update interview details.");
                                exit();
                            }
                        } else {
                            error_log("Error preparing statement for checking interview details: " . mysqli_error($conn));
                            header("Location: listapplication.php?error=Failed to check interview details.");
                            exit();
                        }
                    } else {
                        error_log("No application data found for user ID: $userId");
                        header("Location: listapplication.php?error=No application data found for user.");
                        exit();
                    }
                } else {
                    error_log("Error preparing statement for fetching application ID: " . mysqli_error($conn));
                    header("Location: listapplication.php?error=Failed to fetch application data.");
                    exit();
                }
            } elseif ($action === 'reject') {

                // Get the application ID associated with the user
                $applicationIdQuery = "SELECT id FROM applicant_information WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $applicationIdQuery);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'i', $userId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $applicationData = mysqli_fetch_assoc($result);
                    mysqli_stmt_close($stmt);

                    if ($applicationData) {
                        $applicationId = $applicationData['id'];

                        // Check if interview details already exist for the application
                        $checkQuery = "SELECT id FROM interview_details WHERE application_id = ?";
                        $stmt = mysqli_prepare($conn, $checkQuery);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, 'i', $applicationId);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $existingInterview = mysqli_fetch_assoc($result);
                            mysqli_stmt_close($stmt);

                            if ($existingInterview) {
                                // Update existing interview details
                                $updateQuery = "UPDATE interview_details SET qr_code_path = ? WHERE application_id = ?";
                            } else {
                                // Insert new interview details
                                $updateQuery = "INSERT INTO interview_details (application_id, qr_code_path) VALUES (?, ?)";
                            }

                            $stmt = mysqli_prepare($conn, $updateQuery);
                            if ($stmt) {
                                if ($existingInterview) {
                                    mysqli_stmt_bind_param($stmt, 'si', $qrFilePath, $applicationId);
                                } else {
                                    mysqli_stmt_bind_param($stmt, 'is', $applicationId, $qrFilePath);
                                }
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);

                                $successMessage = "Status updated successfully.";
                                header("Location: listapplication.php?success=" . urlencode($successMessage));
                                exit();
                            } else {
                                error_log("Error preparing statement for interview details: " . mysqli_error($conn));
                                header("Location: listapplication.php?error=Failed to update interview details.");
                                exit();
                            }
                        } else {
                            error_log("Error preparing statement for checking interview details: " . mysqli_error($conn));
                            header("Location: listapplication.php?error=Failed to check interview details.");
                            exit();
                        }
                    } else {
                        error_log("No application data found for user ID: $userId");
                        header("Location: listapplication.php?error=No application data found for user.");
                        exit();
                    }
                } else {
                    error_log("Error preparing statement for fetching application ID: " . mysqli_error($conn));
                    header("Location: listapplication.php?error=Failed to fetch application data.");
                    exit();
                }
            } else {
                $successMessage = "Status updated successfully.";
                header("Location: listapplication.php?success=" . urlencode($successMessage));
                exit();
            }
        } else {
            error_log("Error preparing statement for updating status: " . mysqli_error($conn));
            header("Location: listapplication.php?error=Failed to update status.");
            exit();
        }
    } else {
        error_log("Invalid input data: user_id = " . var_export($userId, true) . ", action = " . var_export($action, true));
        header("Location: listapplication.php?error=Invalid input data.");
        exit();
    }
} else {
    error_log("Invalid request method");
    header("Location: listapplication.php?error=Invalid request method.");
    exit();
}
?>
