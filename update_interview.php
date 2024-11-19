<?php
session_start();
require_once 'dbconn.php';
require_once 'phpqrcode/qrlib.php'; // Include the QR code library

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

// Handle form submission to update interview status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_qr'])) {
    if (isset($_POST['application_id']) && !empty($_POST['application_id'])) {
        $interviewer_id = $_SESSION['user_id'];
        $application_id = $_POST['application_id'];
        $interviewTime = $_POST['interview_time'] ?? null;
        $interviewDate = $_POST['interview_date'] ?? null;
        $interviewLocation = $_POST['interview_location'] ?? null;
        $additionalNotes = $_POST['additional_notes'] ?? null;

        // Retrieve existing QR code path
        $query_qr = "SELECT qr_code_path FROM interview_details WHERE application_id = ?";
        $stmt_qr = $conn->prepare($query_qr);
        $stmt_qr->bind_param("i", $application_id);
        $stmt_qr->execute();
        $result_qr = $stmt_qr->get_result();
        $row_qr = $result_qr->fetch_assoc();
        $old_qr_code_path = $row_qr['qr_code_path'] ?? null;
        $stmt_qr->close();

        if (!$old_qr_code_path) {
            error_log("Failed to retrieve old QR code path for application ID $application_id");
        }

        // Update interview details in the database
        $update_query = "UPDATE interview_details SET interview_time = ?, interview_date = ?, interview_location = ?, additional_notes = ? WHERE application_id = ? AND interviewer_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssii", $interviewTime, $interviewDate, $interviewLocation, $additionalNotes, $application_id, $interviewer_id);

        if ($update_stmt->execute()) {
            // Generate QR code data
            $interview_data = json_encode([
                'Application ID' => $application_id,
                'Interview Date' => $interviewDate,
                'Interview Time' => $interviewTime,
                'Interview Location' => $interviewLocation,
                'Interviewer ID' => $interviewer_id,
                'Additional Notes' => $additionalNotes
            ], JSON_PRETTY_PRINT);

            // Generate QR code image as PNG
            $qr_filename_png = 'qr_codes/interview_qr_' . $application_id . '.png';
            QRcode::png($interview_data, $qr_filename_png);

            // Convert PNG to JPG
            $qr_filename_jpg = 'qr_codes/interview_qr_' . $application_id . '.jpg';
            $image = imagecreatefrompng($qr_filename_png);

            if ($image === false) {
                error_log("Failed to create image from PNG for application ID $application_id");
            }

            if (!imagejpeg($image, $qr_filename_jpg, 100)) {
                error_log("Failed to convert PNG to JPG for application ID $application_id");
            }

            imagedestroy($image);

            // Update the interview_details table with the new JPG QR code file path
            $update_qr_query = "UPDATE interview_details SET qr_code_path = ? WHERE application_id = ?";
            $update_qr_stmt = $conn->prepare($update_qr_query);
            $update_qr_stmt->bind_param("si", $qr_filename_jpg, $application_id);

            if ($update_qr_stmt->execute()) {
                // Redirect to listappinterviewer.php with success message
                header('Location: listappinterviewer.php?success=Interview+details+updated+successfully+and+QR+code+generated');
                exit();
            } else {
                error_log('Error updating QR code path: ' . $conn->error);
                $response = [
                    'success' => false,
                    'error' => 'Error updating QR code path: ' . $conn->error
                ];
            }

            $update_qr_stmt->close();
        } else {
            error_log('Error updating interview status: ' . $conn->error);
            $response = [
                'success' => false,
                'error' => 'Error updating interview status: ' . $conn->error
            ];
        }

        $update_stmt->close();
    } else {
        $response = [
            'success' => false,
            'error' => 'Application ID is missing.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response); 
    exit;
}
?>
