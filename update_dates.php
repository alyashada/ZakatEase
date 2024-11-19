<?php
session_start();
include 'dbconn.php';

if ($_SESSION['usertype'] != 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if a record already exists
    $query = "SELECT COUNT(*) as count FROM zakat_application_dates";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Update existing record
        $query = "UPDATE zakat_application_dates SET start_date = ?, end_date = ?";
    } else {
        // Insert new record
        $query = "INSERT INTO zakat_application_dates (start_date, end_date) VALUES (?, ?)";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Dates updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update dates.";
    }
    $stmt->close();
}

$query = "SELECT start_date, end_date FROM zakat_application_dates LIMIT 1";
$result = $conn->query($query);
$date_range = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Dates - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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

    </style>
</head>
<body>
    <?php
    // Header section
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
    <div class="container mt-5"><br><br><br>
        <h2 style="text-align: center;">Update Zakat Application Dates</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($date_range['start_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($date_range['end_date']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Dates</button>
        </form>
    </div>
</body>
</html>
