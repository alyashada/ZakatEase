<?php
session_start();
require_once 'dbconn.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['user_id']) || !isset($_POST['message']) || !isset($_POST['qr_code_path']) || !isset($_POST['application_id'])) {
        echo 'User ID, message, QR code path, or application ID is missing.';
        exit;
    }

    $user_id = $_POST['user_id'];
    $message = $_POST['message'];
    $qr_code_path = $_POST['qr_code_path'];
    $application_id = $_POST['application_id'];

    // Check if user_id exists in registered_user table
    $userCheckQuery = "SELECT id FROM registered_user WHERE id = ?";
    $userCheckStmt = $conn->prepare($userCheckQuery);
    $userCheckStmt->bind_param("i", $user_id);
    $userCheckStmt->execute();
    $userCheckStmt->store_result();

    if ($userCheckStmt->num_rows === 0) {
        echo 'Invalid user ID.';
        exit;
    }

    $userCheckStmt->close();

    // Check if a notification has already been sent for this application
    $notificationCheckQuery = "SELECT notification_sent FROM interview_details WHERE application_id = ?";
    $notificationCheckStmt = $conn->prepare($notificationCheckQuery);
    $notificationCheckStmt->bind_param("i", $application_id);
    $notificationCheckStmt->execute();
    $notificationCheckStmt->bind_result($notification_sent);
    $notificationCheckStmt->fetch();
    $notificationCheckStmt->close();

    if ($notification_sent) {
        header('Location: listappinterviewer.php?error=Notification+has+already+been+sent');
        exit;
    }

    // Insert the notification
    $query = "INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $message);

    if ($stmt->execute()) {
        // Update the notification_sent flag in the interview_details table
        $updateQuery = "UPDATE interview_details SET notification_sent = 1 WHERE application_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $application_id);
        $updateStmt->execute();
        $updateStmt->close();

        header('Location: listappinterviewer.php?success=Notification+sent+successfully');
    } else {
        echo 'Failed to execute statement: ' . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
