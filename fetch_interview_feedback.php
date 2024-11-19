<?php
require_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $applicationId = $_POST['application_id'];

    $query = "SELECT rely_on_zakat, other_income, selected_options, zakat_eligibility, zakat_amount, zakat_yuran, appraisers_comments, asnaf_course_needed, course_recommendations FROM interview_feedback WHERE application_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $applicationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $response = [
                'rely_on_zakat' => $row['rely_on_zakat'],
                'other_income' => $row['other_income'],
                'selected_options' => explode(',', $row['selected_options']), 
                'zakat_eligibility' => $row['zakat_eligibility'],
                'zakat_amount' => $row['zakat_amount'],
                'zakat_yuran' => $row['zakat_yuran'],
                'appraisers_comments' => $row['appraisers_comments'],
                'asnaf_course_needed' => $row['asnaf_course_needed'],
                'course_recommendations' => $row['course_recommendations']
            ];

            echo json_encode($response);
        } else {
            
            echo json_encode([]);
        }
    } else {
        
        echo json_encode(['error' => mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>