<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'dbconn.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'student') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch notifications from the database
$query = "SELECT notifications.*, interview_details.qr_code_path
          FROM notifications
          INNER JOIN applicant_information ON notifications.user_id = applicant_information.user_id
          INNER JOIN interview_details ON applicant_information.id = interview_details.application_id
          WHERE notifications.user_id = ?
          ORDER BY notifications.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die('Error executing query: '. $stmt->error);
}

$result = $stmt->get_result();

// Check if notifications were fetched
if (!$result) {
    die('Error fetching notifications: ' . $conn->error);
}

echo "<!-- DEBUG: Notifications fetched, number of rows: " . $result->num_rows . " -->";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .notification-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .notification-item img {
            max-width: 100px;
            margin-right: 20px;
        }
        .notification-item small {
            color: #777;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h2>Notifications</h2>
        <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item notification-item">
                    <img src="<?php echo htmlspecialchars($row['qr_code_path']); ?>" alt="QR Code">
                    <div>
                        <?php echo htmlspecialchars($row['message']); ?>
                        <small><?php echo $row['created_at']; ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <p>No notifications found.</p>
        <?php endif; ?>
    </div><br><br>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
ob_end_flush();
?>
<?php
// Include the header based on user type
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    include 'headerdef.php';
} else {
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
 <?php include 'footer.php'; ?>