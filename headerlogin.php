<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'dbconn.php';

$query = "SELECT start_date, end_date FROM zakat_application_dates WHERE id = 1";
$result = $conn->query($query);
$date_range = $result->fetch_assoc();
$start_date = $date_range['start_date'];
$end_date = $date_range['end_date'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding-top: 80px;
        }

        header {
            background-color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            height: 50px;
            max-width: 100%;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-right: 20px;
        }

        nav ul li:last-child {
            margin-right: 0;
        }

        nav ul li a {
            text-decoration: none !important;
            color: black;
            font-weight: bold;
        }

        .auth-buttons {
            position: relative;
        }

        .auth-buttons a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            padding: 8px 16px;
            border: 2px solid black;
            border-radius: 5px;
            margin-right: 10px;
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        nav ul li a:hover {
            color: #b6c784 !important;
            transition: color 0.3s ease;
        }

        .auth-buttons a:hover {
            background-color: black;
            color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
        }


        @media (max-width: 991.98px) {
            body {
                padding-top: 60px;
            }
        }

    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/zakateaselogo.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Zakat Application
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown" onclick="checkDate(event)">
                        <a class="dropdown-item" href="calc.php">Student</a>
                    </div>
                </li>
                <li><a href="index.php#faq">FAQs</a></li>
            </ul>
        </nav>
        <div class="auth-buttons dropdown">
            <a href="notification.php">
                <i class="fas fa-bell"></i>
            </a>
            <a href="profile.php"><i class="fas fa-user"></i></a>
            <a href="logout.php">LogOut</a>
        </div>
    </header>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const startDate = new Date('<?php echo $start_date; ?>');
        const endDate = new Date('<?php echo $end_date; ?>');

        function checkDate(event) {
            const currentDate = new Date();
            if (currentDate < startDate || currentDate > endDate) {
                event.preventDefault();
                alert('Zakat Application is not available during this period.');
            }
        }
    </script>
</body>
</html>
<?php
ob_end_flush();
?>
