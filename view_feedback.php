<?php
require_once 'dbconn.php';
session_start();

// Check if user_id is provided in the URL
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Query to fetch application_id based on user_id
    $query = "
        SELECT
            id AS application_id
        FROM
            applicant_information
        WHERE
            user_id = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $application = mysqli_fetch_assoc($result);

    // Check if application_id was found
    if (!$application) {
        die("No application found for user ID: " . $userId);
    }

    // Retrieve application_id
    $applicationId = $application['application_id'];

    // Proceed to fetch interview feedback based on application_id
    $queryFeedback = "
        SELECT
            registered_user.id AS user_id,
            applicant_information.id AS application_id,
            interview_feedback.id AS interview_id,
            interview_feedback.application_id AS interview_application_id,
            interview_feedback.interview_id AS feedback_interview_id,
            interview_feedback.rely_on_zakat,
            interview_feedback.other_income,
            interview_feedback.additional_documents,
            interview_feedback.selected_options,
            interview_feedback.zakat_eligibility,
            interview_feedback.zakat_amount,
            interview_feedback.zakat_yuran,
            interview_feedback.appraisers_comments,
            interview_feedback.asnaf_course_needed,
            interview_feedback.course_recommendations,
            interview_feedback.created_at
        FROM
            interview_feedback
        JOIN
            applicant_information ON interview_feedback.application_id = applicant_information.id
        JOIN
            registered_user ON applicant_information.user_id = registered_user.id
        WHERE
            interview_feedback.application_id = ?
    ";

    $stmtFeedback = mysqli_prepare($conn, $queryFeedback);
    mysqli_stmt_bind_param($stmtFeedback, "i", $applicationId);
    mysqli_stmt_execute($stmtFeedback);
    $resultFeedback = mysqli_stmt_get_result($stmtFeedback);

    if (!$resultFeedback) {
        die("Query failed: " . mysqli_error($conn));
    }

    $feedbacks = mysqli_fetch_all($resultFeedback, MYSQLI_ASSOC);
} else {
    die("No user ID provided in the URL.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">


    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* Center align the alert messages */
        .alert-center {
            margin: 0 auto;
            text-align: center;
        }

        /* Add hover effect to feedback entries */
        .feedback-entry {
            background-color: #f5f5f5;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: slideInFromBottom 1s ease-in-out;
        }

        .feedback-entry:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        @keyframes slideInFromBottom {
            0% {
                transform: translateY(100%);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<?php
// Include header based on session
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    switch ($_SESSION['usertype']) {
        case 'admin':
            include 'headeradmin.php';
            break;
        case 'student':
            include 'headerlogin.php';
            break;
        case 'interviewer':
            include 'headerinterviewer.php';
            break;
        default:
            include 'headerdef.php';
    }
}
?>
<div class="container mt-5">
    <h2 style="text-align: center;">Interview Feedback</h2><br>
    <div class="feedback-entries">
        <?php if (!empty($feedbacks)) { ?>
            <?php foreach ($feedbacks as $feedback) { ?>
                <div class="feedback-entry">
                    <p><strong>User ID:</strong> <?php echo htmlspecialchars($feedback['user_id']); ?></p>
                    <p><strong>Application ID:</strong> <?php echo htmlspecialchars($feedback['application_id']); ?></p>
                    <p><strong>Interview ID:</strong> <?php echo htmlspecialchars($feedback['interview_id']); ?></p>
                    <p><strong>Rely on Zakat:</strong> <?php echo htmlspecialchars($feedback['rely_on_zakat']); ?></p>
                    <p><strong>Other Income:</strong> <?php echo htmlspecialchars($feedback['other_income']); ?></p>
                    <p><strong>Additional Documents:</strong>
                        <?php if (!empty($feedback['additional_documents'])) { ?>
                            <a href="<?php echo 'interviewer_uploads/' . htmlspecialchars($feedback['additional_documents']); ?>" target="_blank">View Document</a>
                        <?php } else { ?>
                            No document
                        <?php } ?>
                    </p>
                    <p><strong>Selected Options:</strong> <?php echo htmlspecialchars($feedback['selected_options']); ?></p>
                    <p><strong>Zakat Eligibility:</strong> <?php echo htmlspecialchars($feedback['zakat_eligibility']); ?></p>
                    <p><strong>Zakat Amount:</strong> <?php echo htmlspecialchars($feedback['zakat_amount']); ?></p>
                    <p><strong>Zakat Yuran:</strong> <?php echo htmlspecialchars($feedback['zakat_yuran']); ?></p>
                    <p><strong>Appraisers Comments:</strong> <?php echo htmlspecialchars($feedback['appraisers_comments']); ?></p>
                    <p><strong>Asnaf Course Needed:</strong> <?php echo htmlspecialchars($feedback['asnaf_course_needed']); ?></p>
                    <p><strong>Course Recommendations:</strong> <?php echo htmlspecialchars($feedback['course_recommendations']); ?></p>
                    <p><strong>Created At:</strong> <?php echo htmlspecialchars($feedback['created_at']); ?></p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info alert-center">
                No feedback found for application ID: <?php echo htmlspecialchars($applicationId); ?>
            </div>
        <?php } ?>
    </div><br>
    <a href="listapplication.php" class="btn btn-secondary">Back to Student List</a>
</div>
</body>
<?php
include 'footer.php';
?>
</html>

<?php
mysqli_close($conn);
?>
