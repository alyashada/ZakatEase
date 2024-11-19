<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'vendor/autoload.php';  
function sendEmail($to, $subject, $message, $qrCodePath) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '2021485546@student.uitm.edu.my';  
        $mail->Password = 'rblpfcxtsckrogbz';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port = 465; 

        // Recipients
        $mail->setFrom('2021485546@student.uitm.edu.my', 'ZakatEase');  
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = '<p>' . $message . '</p>'; 

        // Embed QR code image
        if (file_exists($qrCodePath)) {
            $mail->addEmbeddedImage($qrCodePath, 'qrcodeimg');
            $mail->Body .= '<br><img src="cid:qrcodeimg" alt="QR Code">';
        } else {
            $mail->Body .= '<p>QR Code Image does not exist at the provided path.</p>';
        }

        // Additional text if needed
        $mail->Body .= '<p>Please scan QR code to acknowledge your zakat application status</p>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $mail->ErrorInfo); // Log the error for debugging
        return 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

session_start();
require_once 'dbconn.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'interviewer') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        header('Location: listappinterviewer.php?error=Invalid+or+missing+user_id');
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if (!$email || !$subject || !$message) {
        header('Location: listappinterviewer.php?error=Invalid+input');
        exit;
    }

    // Check if user_id exists in the applicant_information table
    $stmt = $conn->prepare("SELECT id FROM applicant_information WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $stmt->close();
        header('Location: listappinterviewer.php?error=User+ID+does+not+exist');
        exit;
    }

    $stmt->bind_result($application_id);
    $stmt->fetch();
    $stmt->close();

    // Fetch the QR code path from interview_details table
    $stmt = $conn->prepare("SELECT qr_code_path FROM interview_details WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        header('Location: listappinterviewer.php?error=QR+Code+path+does+not+exist+for+this+user');
        exit;
    }

    $stmt->bind_result($qrCodePath);
    $stmt->fetch();
    $stmt->close();

    // Send the email
    $result = sendEmail($email, $subject, $message, $qrCodePath);
    if ($result !== true) {
        header('Location: listappinterviewer.php?error=' . urlencode($result));
        exit;
    }

    // Insert into emails table
    $stmt = $conn->prepare("INSERT INTO emails (user_id, email, subject, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $user_id, $email, $subject, $message);
    if (!$stmt->execute()) {
        error_log('Insert into emails failed: ' . $stmt->error); // Log the error for debugging
        header('Location: listappinterviewer.php?error=Insert+into+emails+failed');
        exit;
    }
    $stmt->close();

    // Update interview_details to mark email_sent as 1
    $stmt = $conn->prepare("UPDATE interview_details SET email_sent = 1 WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    if (!$stmt->execute()) {
        error_log('Update interview_details failed: ' . $stmt->error); // Log the error for debugging
        header('Location: listappinterviewer.php?error=Update+interview_details+failed');
        exit;
    }
    $stmt->close();

    // Insert into notifications table
    $notificationMessage = "Hi! Greetings from ZakatEase! We extend a warm invitation to you to scan the QR code enclosed herein for comprehensive details regarding your zakat application. Your cooperation in this matter is greatly valued";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->bind_param("is", $user_id, $notificationMessage);
    if (!$stmt->execute()) {
        error_log('Insert into notifications failed: ' . $stmt->error); // Log the error for debugging
        header('Location: listappinterviewer.php?error=Insert+into+notifications+failed');
        exit;
    }
    $stmt->close();

    header('Location: listappinterviewer.php?success=Email+sent+successfully');
} else {
    header('Location: listappinterviewer.php');
}

$conn->close();
?>
