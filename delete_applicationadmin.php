<?php
session_start();
require_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    error_log("Deleting application for user_id: $userId");

    $deleteAppQuery1 = "DELETE FROM parent_details WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt1 = mysqli_prepare($conn, $deleteAppQuery1);
    mysqli_stmt_bind_param($stmt1, 'i', $userId);

    $deleteAppQuery2 = "DELETE FROM depend_head_fam WHERE applicant_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt2 = mysqli_prepare($conn, $deleteAppQuery2);
    mysqli_stmt_bind_param($stmt2, 'i', $userId);

    $deleteAppQuery3 = "DELETE FROM sibling_details WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt3 = mysqli_prepare($conn, $deleteAppQuery3);
    mysqli_stmt_bind_param($stmt3, 'i', $userId);

    $deleteAppQuery4 = "DELETE FROM accommodation_information WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt4 = mysqli_prepare($conn, $deleteAppQuery4);
    mysqli_stmt_bind_param($stmt4, 'i', $userId);

    $deleteAppQuery5 = "DELETE FROM details_of_aid WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt5 = mysqli_prepare($conn, $deleteAppQuery5);
    mysqli_stmt_bind_param($stmt5, 'i', $userId);
    
    $deleteAppQuery7 = "DELETE FROM interview_feedback WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt7 = mysqli_prepare($conn, $deleteAppQuery7);
    mysqli_stmt_bind_param($stmt7, 'i', $userId);

    $deleteAppQuery6 = "DELETE FROM interview_details WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt6 = mysqli_prepare($conn, $deleteAppQuery6);
    mysqli_stmt_bind_param($stmt6, 'i', $userId);

    $deleteAppQuery8 = "DELETE FROM notifications WHERE user_id = ?";
    $stmt8 = mysqli_prepare($conn, $deleteAppQuery8);
    mysqli_stmt_bind_param($stmt8, 'i', $userId);

    $deleteAppQuery9 = "DELETE FROM emails WHERE user_id = ?";
    $stmt9 = mysqli_prepare($conn, $deleteAppQuery9);
    mysqli_stmt_bind_param($stmt9, 'i', $userId);

    $deleteAppQuery10 = "DELETE FROM applicant_statement WHERE application_id IN (SELECT id FROM applicant_information WHERE user_id = ?)";
    $stmt10 = mysqli_prepare($conn, $deleteAppQuery10);
    mysqli_stmt_bind_param($stmt10, 'i', $userId);

    $deleteAppQuery11 = "DELETE FROM applicant_information WHERE user_id = ?";
    $stmt11 = mysqli_prepare($conn, $deleteAppQuery11);
    mysqli_stmt_bind_param($stmt11, 'i', $userId);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);
    $result3 = mysqli_stmt_execute($stmt3);
    $result4 = mysqli_stmt_execute($stmt4);
    $result5 = mysqli_stmt_execute($stmt5);
    $result7 = mysqli_stmt_execute($stmt7); 
    $result6 = mysqli_stmt_execute($stmt6); 
    $result8 = mysqli_stmt_execute($stmt8);
    $result9 = mysqli_stmt_execute($stmt9);
    $result10 = mysqli_stmt_execute($stmt10);
    $result11 = mysqli_stmt_execute($stmt11);

    if (!$result1) error_log('Failed to delete from parent_details: ' . mysqli_stmt_error($stmt1));
    if (!$result2) error_log('Failed to delete from depend_head_fam: ' . mysqli_stmt_error($stmt2));
    if (!$result3) error_log('Failed to delete from sibling_details: ' . mysqli_stmt_error($stmt3));
    if (!$result4) error_log('Failed to delete from accommodation_information: ' . mysqli_stmt_error($stmt4));
    if (!$result5) error_log('Failed to delete from details_of_aid: ' . mysqli_stmt_error($stmt5));
    if (!$result6) error_log('Failed to delete from interview_details: ' . mysqli_stmt_error($stmt6));
    if (!$result7) error_log('Failed to delete from interview_feedback: ' . mysqli_stmt_error($stmt7));
    if (!$result8) error_log('Failed to delete from notifications: ' . mysqli_stmt_error($stmt8));
    if (!$result9) error_log('Failed to delete from emails: ' . mysqli_stmt_error($stmt9));
    if (!$result10) error_log('Failed to delete from applicant_statement: ' . mysqli_stmt_error($stmt10));
    if (!$result11) error_log('Failed to delete from applicant_information: ' . mysqli_stmt_error($stmt11));

    if ($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7 && $result8 && $result9 && $result10 && $result11) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    mysqli_close($conn);
    exit();
} else {
    echo json_encode(['status' => 'error']);
    exit();
}
?>
