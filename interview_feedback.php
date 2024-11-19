<?php
session_start();
require_once 'dbconn.php';

// Check if user is logged in as interviewer
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

$feedback_submitted = false;
$application_id = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get logged-in interviewer's user_id
    $interviewer_id = $_SESSION['user_id'];

    // Sanitize and retrieve form data
    $application_id = htmlspecialchars($_POST['application_id']);
    $rely_on_zakat = isset($_POST['rely_on_zakat']) ? htmlspecialchars($_POST['rely_on_zakat']) : '';
    $other_income = htmlspecialchars($_POST['other_income']);
    $selected_options = isset($_POST['selected_options']) ? implode(',', array_map('htmlspecialchars', $_POST['selected_options'])) : '';
    $zakat_eligibility = htmlspecialchars($_POST['zakat_eligibility']);
    $zakat_amount = htmlspecialchars($_POST['zakat_amount']);
    $zakat_yuran = htmlspecialchars($_POST['zakat_yuran']);
    $appraisers_comments = htmlspecialchars($_POST['appraisers_comments']);
    $asnaf_course_needed = htmlspecialchars($_POST['asnaf_course_needed']);
    $course_recommendations = htmlspecialchars($_POST['course_recommendations']);

    $additional_document = '';
    // Handle file upload if additional document is provided
    if (!empty($_FILES['additional_document']['name'])) {
        $upload_dir = 'interviewer_uploads/'; 
        $allowed_types = array('pdf', 'docx', 'doc'); 
        $file_type = strtolower(pathinfo($_FILES['additional_document']['name'], PATHINFO_EXTENSION));
        if (in_array($file_type, $allowed_types)) {
            $file_name = uniqid() . '_' . $_FILES['additional_document']['name'];
            $upload_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['additional_document']['tmp_name'], $upload_file)) {
                $additional_document = $file_name;
            } else {
                $_SESSION['error'] = 'Error uploading file: ' . $_FILES['additional_document']['error'];
                header('Location: listappinterviewer.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Invalid file type';
            header('Location: listappinterviewer.php');
            exit;
        }
    }

    // Check if the application ID exists in interview_details
    $query = "SELECT id FROM interview_details WHERE application_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $application_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $interview_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$interview_id) {
        $_SESSION['error'] = "Error: Interview ID not found for the given application ID.";
        header('Location: listappinterviewer.php');
        exit;
    }

    // Check if feedback has already been submitted for this application
    $query_check = "SELECT feedback_submitted FROM interview_feedback WHERE application_id = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, 's', $application_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $feedback_submitted);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($feedback_submitted) {
        $_SESSION['error'] = "Feedback has already been submitted for this application.";
        header('Location: listappinterviewer.php');
        exit;
    }

    // Insert interview feedback into interview_feedback table
    $query_insert = "INSERT INTO interview_feedback (application_id, interview_id, rely_on_zakat, other_income, additional_documents, selected_options, zakat_eligibility, zakat_amount, zakat_yuran, appraisers_comments, asnaf_course_needed, course_recommendations, feedback_submitted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt_insert = mysqli_prepare($conn, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, 'ssssssssssss', $application_id, $interview_id, $rely_on_zakat, $other_income, $additional_document, $selected_options, $zakat_eligibility, $zakat_amount, $zakat_yuran, $appraisers_comments, $asnaf_course_needed, $course_recommendations);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success'] = "Interview feedback submitted successfully!";
    } else {
        $_SESSION['error'] = "Error inserting record: " . mysqli_error($conn);
    }

    // Update interview status in interview_details table to 'Completed'
    $query_status_update = "UPDATE interview_details SET interview_status = 'Completed' WHERE id = ?";
    $stmt_status_update = mysqli_prepare($conn, $query_status_update);
    mysqli_stmt_bind_param($stmt_status_update, 's', $interview_id);
    mysqli_stmt_execute($stmt_status_update);

    mysqli_close($conn);

    $feedback_submitted = true;

    header('Location: listappinterviewer.php');
    exit;
} else {
    // If accessed via GET method or without proper POST data
    // Retrieve previous feedback data if available
    if (!empty($_GET['application_id'])) {
        $application_id = htmlspecialchars($_GET['application_id']);
        
        // Query to retrieve previous feedback data
        $query_feedback = "SELECT rely_on_zakat, other_income, additional_documents, selected_options, zakat_eligibility, zakat_amount, zakat_yuran, appraisers_comments, asnaf_course_needed, course_recommendations FROM interview_feedback WHERE application_id = ?";
        $stmt_feedback = mysqli_prepare($conn, $query_feedback);
        mysqli_stmt_bind_param($stmt_feedback, 's', $application_id);
        mysqli_stmt_execute($stmt_feedback);
        mysqli_stmt_bind_result($stmt_feedback, $rely_on_zakat, $other_income, $additional_document, $selected_options, $zakat_eligibility, $zakat_amount, $zakat_yuran, $appraisers_comments, $asnaf_course_needed, $course_recommendations);
        
        // Fetch the results
        mysqli_stmt_fetch($stmt_feedback);
        mysqli_stmt_close($stmt_feedback);
    }
}

?>