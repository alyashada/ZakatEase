<?php
session_start();
require_once 'dbconn.php';
require_once 'phpqrcode/qrlib.php'; 

function generateAlerts() {
    if (isset($_SESSION['alertMessages']) && is_array($_SESSION['alertMessages'])) {
        foreach ($_SESSION['alertMessages'] as $message) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button>';
            echo '</div>';
        }
        unset($_SESSION['alertMessages']);
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

$interviewer_id = $_SESSION['user_id'];

error_log("Session user_id: " . $_SESSION['user_id']);
error_log("Session usertype: " . $_SESSION['usertype']);

$search = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search = trim($_POST['search']);
}
$query = "SELECT 
    id.application_id, 
    id.interview_date, 
    id.interview_time, 
    id.interview_location, 
    id.additional_notes, 
    id.interview_status, 
    id.qr_code_path, 
    ai.user_id, 
    ai.matric_no as applicant_matric_no, 
    ru_applicant.email, 
    ru_applicant.full_name as applicant_name, 
    ru_interviewer.full_name as interviewer_name,
    fb.rely_on_zakat,
    fb.other_income,
    fb.zakat_eligibility,
    fb.zakat_amount,
    fb.zakat_yuran,
    fb.appraisers_comments,
    fb.asnaf_course_needed,
    fb.course_recommendations
FROM interview_details id
JOIN applicant_information ai ON id.application_id = ai.id
JOIN registered_user ru_applicant ON ai.user_id = ru_applicant.id
JOIN registered_user ru_interviewer ON id.interviewer_id = ru_interviewer.id
LEFT JOIN interview_feedback fb ON id.application_id = fb.application_id
WHERE id.interviewer_id = ?
";
if (!empty($search)) {
    $query .= " AND ai.matric_no LIKE ?";
}

$stmt = $conn->prepare($query);

if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("is", $interviewer_id, $search_param);
} else {
    $stmt->bind_param("i", $interviewer_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("No applications found for interviewer_id: " . $interviewer_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['interview_status'];

    error_log("POST application_id: " . $application_id);
    error_log("POST interview_status: " . $new_status);

    $update_query = "UPDATE interview_details SET interview_status = ? WHERE application_id = ? AND interviewer_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sii", $new_status, $application_id, $interviewer_id);
    $update_stmt->execute();
    $update_stmt->close();
}

//
$feedback_status = [];
$feedback_query = "SELECT application_id FROM interview_feedback WHERE application_id IN (SELECT application_id FROM interview_details WHERE interviewer_id = ?)";
$feedback_stmt = $conn->prepare($feedback_query);
$feedback_stmt->bind_param("i", $interviewer_id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();

while ($row = $feedback_result->fetch_assoc()) {
    $feedback_status[$row['application_id']] = true;
}

$feedback_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interviewer Applications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <script>
    document.addEventListener("DOMContentLoaded", function() {
    const submitButton = document.getElementById('submit-feedback');
    const feedbackSubmitted = <?php echo json_encode($feedback_submitted); ?>;
    
    if (feedbackSubmitted) {
        submitButton.disabled = true;
    }

    submitButton.addEventListener('click', function(event) {
        if (feedbackSubmitted) {
            event.preventDefault();
            alert("You have already submitted the form.");
        } else {
            const confirmSubmission = confirm("Are you sure? You can only fill in the form once and this action cannot be repeated.");
            if (!confirmSubmission) {
                event.preventDefault();
            } else {
                disableSubmitButton();
            }
        }
    });

    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.add('fade');
            setTimeout(function() {
                alert.remove();
            }, 150);
        });
    }, 3000);
});

function disableSubmitButton() {
    document.getElementById("submit-feedback").disabled = true;
}

$('#successModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var applicationId = button.data('application-id');
    var modal = $(this);
    modal.find('#modal_application_id').val(applicationId);
});

function confirmSubmission() {
    const feedbackSubmitted = <?php echo json_encode($feedback_submitted); ?>;
    if (feedbackSubmitted) {
        alert("You have already submitted the form.");
        return false;
    } else {
        return confirm("Are you sure? You can only fill in the form once and this action cannot be repeated.");
    }
}


</script>

<style> 
        body {
            font-family: 'Roboto', sans-serif;
        }

        
        .alert-center {
            margin: 0 auto;
            text-align: center;
        }

        table tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
            
        }

        .table-bordered {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 30px; /* Curved border radius */
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

        table {
            animation: slideInFromBottom 1s ease-in-out;
        }

        .enlarged-qr-code {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            z-index: 9999;
            cursor: pointer;
        }
        .floating-icon-container {
            position: fixed;
            bottom: 5%;
            left: 5%;
            z-index: 1000;
        }
        .floating-icon-container button {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
        }
        #scanner-container {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            background: rgba(0, 0, 0, 0.8);
            padding: 28px;
            border-radius: 8px;
        }
        #scanner {
            width: 100%;
            max-width: 400px;
            height: auto;
        }
        #closeScannerBtn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        .filter-container {
            position: absolute;
            top: 120px; 
            right: 55px;
            width: 200px; 
        }

        .text-center {
            text-align: center;
        }

        .text-center .btn-primary {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; 
        }

        @media (max-width: 768px) {
            .filter-container {
                position: relative; 
                width: 100%; 
                margin-top: 10px; 
                text-align: right; 
            }
        }
</style>
        
</head>
<body>
    <?php
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
    
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_GET['success']);
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button>';
        echo '</div>';
    }
    
    ?>
    
    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <?php generateAlerts(); ?>

    <div class="container mt-5">       
        <h2 style="text-align:center">Applications Assigned to You</h2><br>
        <div class="filter-container text-right mb-3 pr-3">
            <div class="dropdown">
                <select class="custom-select" id="filterDropdown">
                    <option value="all">Filter by Interview Status</option>
                    <option value="all">All</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>

        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Matric No." value="<?php echo htmlspecialchars($search); ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form><br>
        
        <div class="table-responsive">
            <?php
            if ($result->num_rows > 0) {
                
                echo '<table class="table table-bordered"; "table table-bordered table-sm">';
                echo '<thead>';
                echo '<tr style="border-bottom: 1px solid silver; border-top: 1px solid silver;text-align:center">';
                echo '<th>Application ID</th>';
                echo '<th>Applicant Name</th>';
                echo '<th>Matric No</th>';
                echo '<th>Interviewer Name</th>';
                echo '<th>View Application</th>';
                echo '<th>Assign QR Code</th>';
                echo '<th>View Interview Details</th>'; 
                echo '<th>QR Code</th>';
                echo '<th>Send Email & Notification</th>';
                echo '<th>Submit Interview Feedback</th>';
                echo '<th>Interview Status</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                
                while ($row = $result->fetch_assoc()) {
                    $interviewer_name = htmlspecialchars($row['interviewer_name']);
                    $applicant_name = htmlspecialchars($row['applicant_name']);
                    $applicant_matric_no = htmlspecialchars($row['applicant_matric_no']);
                    $application_id = htmlspecialchars($row['application_id']);
                    $user_id = htmlspecialchars($row['user_id']);
                    $email = htmlspecialchars($row['email']);
                    $qr_code_path = htmlspecialchars($row['qr_code_path']);
                    $interview_date = htmlspecialchars($row['interview_date']);
                    $interview_time = htmlspecialchars($row['interview_time']);
                    $interview_location = htmlspecialchars($row['interview_location']);
                    $additional_notes = htmlspecialchars($row['additional_notes']);
                    $interview_status = htmlspecialchars($row['interview_status']);

                    echo '<tr data-application-id="'. $application_id. '">';
                    echo '<td>'. $application_id. '</td>';
                    echo '<td>'. $applicant_name. '</td>';
                    echo '<td>'. $applicant_matric_no. '</td>';
                    echo '<td>'. $interviewer_name. '</td>';
                
            
                    echo '<td class="text-center">';
                    echo '<a href="view_applicationInterviewer.php?application_id=' . $application_id . '" class="btn btn-secondary">';
                    echo '<i class="fas fa-file-alt"></i>'; 
                    echo '</a>';
                    echo '</td>';

                    echo '<td class="text-center">';
                    echo '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#qrModal' . $application_id . '">';
                    echo '<i class="fas fa-qrcode"></i>'; 
                    echo '</button>';
                    echo '<!-- QR Modal -->';
                    echo '<div class="modal fade" id="qrModal' . $application_id . '" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel' . $application_id . '" aria-hidden="true">';
                    echo '<div class="modal-dialog" role="document">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="qrModalLabel' . $application_id . '">Generate QR Code</h5>';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                    echo '<div class="modal-body">';
                    echo '<form action="update_interview.php" method="post">';
                    
                    echo '<div class="form-group">';
                    echo '<label for="application_id">Application ID</label>';
                    echo '<input type="text" class="form-control" id="application_id" name="application_id" value="' . $application_id . '" readonly>';
                    echo '</div>';
                    
                    echo '<div class="form-group">';
                    echo '<label for="interview_date">Interview Date</label>';
                    echo '<input type="date" class="form-control" id="interview_date" name="interview_date" value="' . htmlspecialchars($row['interview_date']) . '" required>';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="interview_time">Interview Time</label>';
                    echo '<input type="time" class="form-control" id="interview_time" name="interview_time" value="' . htmlspecialchars($row['interview_time']) . '" required>';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="interview_location">Interview Location</label>';
                    echo '<input type="text" class="form-control" id="interview_location" name="interview_location" value="' . htmlspecialchars($row['interview_location']) . '" required>';
                    echo '</div>';
                    echo '<div class="form-group">';
                    echo '<label for="interviewer_id">Interviewer ID</label>';
                    echo '<input type="text" class="form-control" id="interviewer_id" name="interviewer_id" value="' . htmlspecialchars($interviewer_id) . '" readonly>';
                    echo '</div>';
                    echo '<div class="row">';
                    echo '<div class="col-md-12">';
                    echo '<div class="form-group">';
                    echo '<label for="additional_notes">Additional Notes</label>';
                    echo '<textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" readonly>Congratulation, you are pass to go to the interview</textarea>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
           
                    echo '<button type="submit" name="generate_qr" class="btn btn-primary">Generate QR Code</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';

                    echo '<td class="text-center">';
                    echo '<button type="button" class="btn btn-info" data-toggle="modal" data-target="#interviewDetailsModal' . $application_id . '">';
                    echo '<i class="fas fa-info-circle"></i>';
                    echo '</button>';

                    echo '<!-- Interview Details Modal -->';
                    echo '<div class="modal fade" id="interviewDetailsModal' . $application_id . '" tabindex="-1" role="dialog" aria-labelledby="interviewDetailsModalLabel' . $application_id . '" aria-hidden="true">';
                    echo '<div class="modal-dialog" role="document">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="interviewDetailsModalLabel' . $application_id . '">Interview Details</h5>';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                    echo '<div class="modal-body">';
                    echo '<p><strong>Interview Date:</strong> ' . $interview_date . '</p>';
                    echo '<p><strong>Interview Time:</strong> ' . $interview_time . '</p>';
                    echo '<p><strong>Interview Location:</strong> ' . $interview_location . '</p>';
                    echo '<p><strong>Additional Notes:</strong> ' . $additional_notes . '</p>';
                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';
                    
                    
                    echo '<td>';
                    if ($qr_code_path && file_exists($qr_code_path)) {
                        echo '<a href="#" onclick="enlargeQRCode(\'' . htmlspecialchars($qr_code_path) . '\')">';
                        echo '<img src="' . htmlspecialchars($qr_code_path) . '" class="img-fluid qr-code" alt="QR Code">';
                        echo '</a>';
                    } else {
                        echo '<p>QR Code Image does not generated yet.</p>';
                    }
                    echo '</td>';
                 
                   // Fetch the current notification and email status from the database
                    $stmt = $conn->prepare("SELECT notification_sent, email_sent, qr_code_path FROM interview_details WHERE application_id = ?");
                    $stmt->bind_param("i", $application_id);
                    $stmt->execute();
                    $stmt->bind_result($notification_sent, $email_sent, $qr_code_path);
                    $stmt->fetch();
                    $stmt->close();

                    echo '<td class="text-center">';
                    echo '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#actionModal' . $application_id . '">';
                    echo '<i class="fas fa-envelope"></i>'; 
                    echo '</button>';
                    echo '<!-- Action Modal -->';
                    echo '<div class="modal fade" id="actionModal' . $application_id . '" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel' . $application_id . '" aria-hidden="true">';
                    echo '<div class="modal-dialog" role="document">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<h5 class="modal-title" id="actionModalLabel' . $application_id . '">Send Notification / Email</h5>';
                    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    echo '<span aria-hidden="true">&times;</span>';
                    echo '</button>';
                    echo '</div>';
                    echo '<div class="modal-body">';

                    // Notification Form
                    if ($notification_sent) {
                        echo '<div class="alert alert-info">Notification has already been sent.</div>';
                    } else {
                        echo '<!-- Notification Form -->';
                        echo '<form action="send_notification.php" method="post" id="notification-form" onsubmit="return confirmSend()">';
                        echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
                        echo '<input type="hidden" name="application_id" value="' . $application_id . '">';
                        echo '<div class="form-group">';
                        echo '<label for="notification-message">Message:</label>';
                        echo '<textarea class="form-control" id="notification-message" name="message" required readonly rows="4" cols="50">Hi! "Greetings from ZakatEase! We extend a warm invitation to you to scan the QR code enclosed herein for comprehensive details regarding your zakat application. Your cooperation in this matter is greatly valued."</textarea>';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label>QR Code:</label><br>';
                        echo '<img src="' . $qr_code_path . '" alt="QR Code" style="max-width: 200px;">';
                        echo '</div>';
                        echo '<input type="hidden" name="qr_code_path" value="' . $qr_code_path . '">';
                        echo '<button type="submit" class="btn btn-primary">Send Notification</button>';
                        echo '</form>';
                    }

                        echo'<hr>';

                    // Email Form
                    if ($email_sent) {
                        echo '<div class="alert alert-info">Email has already been sent.</div>';
                    } else {
                        echo '<!-- Email Form -->';
                        echo '<form action="send_email.php" method="post" onsubmit="return confirmSendEmail()">';
                        echo '<div class="form-group">';
                        echo '<label for="email">Recipient Email Address:</label>';
                        echo '<input type="email" class="form-control" id="email" name="email" value="' . htmlspecialchars($email) . '" readonly>';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label for="subject">Email Subject:</label>';
                        echo '<input type="text" class="form-control" id="subject" name="subject" value="ZakatEase - Your zakat Status">';
                        echo '</div>';
                        echo '<div class="form-group">';
                        echo '<label for="message">Message Template:</label>';
                        echo '<textarea class="form-control" id="message-template' . $application_id . '" name="message" required readonly rows="4" cols="50">Hi! "Greetings from ZakatEase! We extend a warm invitation to you to scan the QR code enclosed herein for comprehensive details regarding your zakat application. Your cooperation in this matter is greatly valued.".</textarea>';
                        echo '</div>';
                        if ($qr_code_path && file_exists($qr_code_path)) {
                            echo '<img src="' . htmlspecialchars($qr_code_path) . '" alt="QR Code">';
                        } else {
                            echo '<p>QR Code Image does not exist at the provided path.</p>';
                        }
                        echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($user_id) . '">';
                        echo '<button type="submit" class="btn btn-primary" name="send_email">Send Email</button>';
                        echo '</form>';
                    }

                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';

                    echo '<td class="text-center">';
                    echo '<button type="button" class="btn btn-secondary feedback-btn" data-toggle="modal" data-target="#successModal" data-application-id="' . $application_id . '">';
                    echo '<i class="fas fa-comment-alt"></i>'; 
                    echo '</button>';
                    echo '</td>';                                       
                    echo '<td class="status-' . strtolower($row['interview_status']) . '">';
                    echo '<div id="updateFormContainer">';
                    echo '<form method="POST" target="updateFrame" class="form-inline update-form" onsubmit="updateInterviewStatus(event)" style="display:none;">';
                    echo '<input type="hidden" name="application_id" value="' . $application_id . '">';
                    echo '<input type="hidden" name="update_status" value="1">'; // This hidden field is important
                    echo '<input type="hidden" name="interview_status" value="' . $row['interview_status'] . '">'; // Set initial value based on existing interview status
                    echo '<iframe name="updateFrame" style="display: none;"></iframe>';
                    echo '</form>';
                    echo '<div>';
                    echo '<span>' . ucfirst(strtolower($row['interview_status'])) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';

                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                // Floating icon and scanner container
                echo '<div class="floating-icon-container">';
                echo '<button type="button" class="btn btn-primary" id="qrCodeScannerBtn"><i class="fas fa-qrcode"></i></button>';
                echo '</div>';
                echo '<div id="scanner-container">';
                echo '<button id="closeScannerBtn">&times;</button>';
                echo '<video id="scanner" playsinline></video>';
                echo '</div>';
                
            } else {
                echo '<p>No applications assigned to you.</p>';
            }
            ?>
        </div>
    </div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?> 


<!-- Modal for interview feedback -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Interview Session Assessment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="interview_feedback.php" enctype="multipart/form-data" onsubmit="return confirmSubmission()">                <div class="modal-body">
            <input type="hidden" id="modal_application_id" name="application_id" value="<?php echo htmlspecialchars($application_id); ?>">
                    <div class="form-group">
                        <label for="rely_on_zakat">1. Do students rely solely on zakat?</label><br>
                        <label><input type="radio" name="rely_on_zakat" value="yes"> Yes</label>
                        <label><input type="radio" name="rely_on_zakat" value="no"> No</label>
                    </div>
                    <div class="form-group">
                        <label for="other_income">2. Other income per semester:</label>
                        <input type="text" id="other_income" name="other_income" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="additional_document">3. If there are additional documents:</label>
                        <input type="file" id="additional_document" name="additional_document" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>4. Is the PYD in the following categories:</label><br>
                        <label><input type="checkbox" name="selected_options[]" value="orphans"> Orphans</label><br>
                        <label><input type="checkbox" name="selected_options[]" value="oku"> OKU</label><br>
                        <label><input type="checkbox" name="selected_options[]" value="guardian"> Guardian of a sick family member</label><br>
                        <label><input type="checkbox" name="selected_options[]" value="not_applicable"> NOT APPLICABLE</label>
                    </div>
                    <div class="form-group">
                        <label for="zakat_eligibility">5. Based on the student profile information and the interview session conducted, is PYD eligible to be given zakat assistance UiTM Perlis Branch?</label><br>
                        <input type="text" id="zakat_eligibility" name="zakat_eligibility" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="zakat_amount">6. Based on the assessment, what amount should be given to PYD? (ZAKAT SARA DIRI)</label><br>
                        <label><input type="radio" name="zakat_amount" value="full_financing"> Full financing</label>
                        <label><input type="radio" name="zakat_amount" value="partial_financing"> Partial financing</label>
                        <label><input type="radio" name="zakat_amount" value="not_eligible"> Not Eligible</label>
                    </div>
                    <div class="form-group">
                        <label for="zakat_yuran">7. Based on the assessment, what amount should be given to PYD? (ZAKAT YURAN)</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM200"> RM200</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM250"> RM250</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM300"> RM300</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM350"> RM350</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM400"> RM400</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM450"> RM450</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM500"> RM500</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM600"> RM600</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM700"> RM700</label><br>
                        <label><input type="radio" name="zakat_yuran" value="RM800"> RM800</label>
                        <label><input type="radio" name="zakat_yuran" value="RM1000"> RM1000 (students have no source of income other than zakat UiTM)</label>
                        <label><input type="radio" name="zakat_yuran" value="Others"> Others (Please state the appropriate rate given in Item 10: Appraiser's Comment)</label>
                        <label><input type="radio" name="zakat_yuran" value="noteligible"> Not Eligible</label>
                    </div>
                    <div class="form-group">
                        <label for="appraisers_comments">8. Appraiser's Comments</label>
                        <textarea id="appraisers_comments" name="appraisers_comments" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="asnaf_course_needed">9. In the opinion of the Evaluator, does PYD need an asnaf strengthening course?</label><br>
                        <label><input type="radio" name="asnaf_course_needed" value="yes"> Yes</label>
                        <label><input type="radio" name="asnaf_course_needed" value="no"> No</label>
                        <label><input type="radio" name="asnaf_course_needed" value="maybe"> Maybe</label>
                    </div>
                    <div class="form-group">
                        <label for="course_recommendations">10. Recommendations for necessary asnaf stabilization courses for PYD (if any)</label>
                        <textarea id="course_recommendations" name="course_recommendations" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit_feedback" id="submit-feedback" class="btn btn-primary" <?php echo isset($feedback_status[$application_id]) ? 'disabled' : ''; ?>>Submit</button>                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
$(document).ready(function() {
    // Function to generate QR code
    $('.generate-qr-btn').on('click', function() {
        var applicationId = $(this).data('application-id');
        var qrContainer = $('#qr-code-container-' + applicationId);

        $.ajax({
            url: 'update_interview.php',
            method: 'POST',
            data: {
                application_id: applicationId,
                interview_time: $('#interview_time').val(),
                interview_date: $('#interview_date').val(),
                interview_location: $('#interview_location').val(),
                additional_notes: $('#additional_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    qrContainer.html('<img src="' + response.qr_code_path + '" class="img-fluid qr-code" data-application-id="' + applicationId + '">');
                } else {
                    alert('Failed to generate QR code: ' + response.error);
                }
            }
        });
    });

    // Function to enlarge QR code
    function enlargeQRCode(qrCodePath) {
        var enlargedQRCode = document.createElement('img');
        enlargedQRCode.src = qrCodePath;
        enlargedQRCode.classList.add('enlarged-qr-code');

        document.body.appendChild(enlargedQRCode);

        enlargedQRCode.addEventListener('click', function() {
            document.body.removeChild(enlargedQRCode);
        });
    }

    // Event listener for QR code clicks
    $(document).on('click', '.qr-code', function() {
        var qrCodePath = $(this).attr('src');
        enlargeQRCode(qrCodePath);
    });

    // Initialize and handle QR code scanner
    let scanner = null;
    $('#qrCodeScannerBtn').click(function() {
        $('#scanner-container').show();
        scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });

        scanner.addListener('scan', function(content) {
            try {
                var scannedData = JSON.parse(content);

                // Handle the scanned data
                var applicationId = scannedData['Application ID'];
                var interviewDate = scannedData['Interview Date'];
                var interviewTime = scannedData['Interview Time'];
                var interviewLocation = scannedData['Interview Location'];
                var interviewerId = scannedData['Interviewer ID'];
                var additionalNotes = scannedData['Additional Notes'];

                // Format the scanned data for display
                var formattedMessage = '';
                for (var key in scannedData) {
                    formattedMessage += key + ': ' + scannedData[key] + '\n';
                }

                // Display the formatted message
                alert('Scanned content:\n\n' + formattedMessage);
            } catch (error) {
                alert('Scanned content: ' + content);
            }
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(error) {
            console.error(error);
        });
    });

    // Close QR code scanner
    $('#closeScannerBtn').click(function() {
        $('#scanner-container').hide();
        if (scanner !== null) {
            scanner.stop();
            scanner = null;
        }
    });

    document.getElementById('notification-form').addEventListener('submit', function(e) {
        var qrCodePath = document.getElementById('qr-code-path').value;
        var message = document.getElementById('notification-message').value;
        var htmlMessage = message + '<br><img src="' + qrCodePath + '" alt="QR Code">';
        document.getElementById('notification-message').value = htmlMessage;
    });

    // Event listener for send email button
    $('.send-email-btn').on('click', function() {
        var applicationId = $(this).closest('tr').data('application-id');
        alert('Email and notification sent to applicant!');
    });

    // Event listener for assign QR code button
    $('.assign-qr-code-btn').on('click', function() {
        var applicationId = $(this).closest('tr').data('application-id');
        alert('QR code assigned to applicant!');
    });

    // Event listener for feedback buttons
    $('.feedback-btn').click(function() {
        var applicationId = $(this).data('application-id');
        document.getElementById('modal_application_id').value = applicationId;

        // AJAX request to fetch data based on applicationId
        $.ajax({
            url: 'fetch_interview_feedback.php',
            type: 'POST',
            data: { application_id: applicationId },
            dataType: 'json',
            success: function(response) {
                // Log the response for debugging
                console.log('Response received:', response);

                // Check if response is not empty
                if ($.isEmptyObject(response)) {
                    console.log('No data found for application ID: ' + applicationId);
                    return;
                }

                // Populate modal fields with fetched data
                $('input[name="rely_on_zakat"][value="' + response.rely_on_zakat + '"]').prop('checked', true);
                $('#other_income').val(response.other_income);
                // Populate other fields similarly
                $('input[name="selected_options[]"]').each(function() {
                    $(this).prop('checked', response.selected_options.includes($(this).val()));
                });

                $('#zakat_eligibility').val(response.zakat_eligibility);
                $('input[name="zakat_amount"][value="' + response.zakat_amount + '"]').prop('checked', true);
                $('input[name="zakat_yuran"][value="' + response.zakat_yuran + '"]').prop('checked', true);
                $('#appraisers_comments').val(response.appraisers_comments);
                $('input[name="asnaf_course_needed"][value="' + response.asnaf_course_needed + '"]').prop('checked', true);
                $('#course_recommendations').val(response.course_recommendations);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
            }
        });
    });

    // Automatically close the alert after 3 seconds
    window.setTimeout(function() {
        $(".alert").alert('close');
    }, 3000);

    // Event listener for filter dropdown
    $('#filterDropdown').change(function() {
        var filter = $(this).val();

        if (filter === 'all') {
            $('.table tbody tr').show();
        } else {
            $('.table tbody tr').hide();
            $('.table tbody tr .status-' + filter).closest('tr').show();
        }
    });
});

// Confirm functions
function confirmSend() {
    return confirm("Are you sure you want to send the notification? This action cannot be undone.");
}

function confirmSendEmail() {
    return confirm("Are you sure you want to send the email? This action cannot be undone.");
}

function confirmSubmission() {
    return confirm("Are you sure you want to submit the interview feedback? This action cannot be undone.");
}
</script>

</body>
</html>
