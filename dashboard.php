<?php 
session_start();

$total_users = 0;
$total_applications = 0;

include 'dbconn.php';

// Fetch total users
$query = "SELECT COUNT(*) as total_users FROM registered_user";
$result = mysqli_query($conn, $query);
$total_users = mysqli_fetch_assoc($result)['total_users'] ?? 0;

// Fetch total applications
$query = "SELECT COUNT(*) as total_applications FROM applicant_information";
$result = mysqli_query($conn, $query);
$total_applications = mysqli_fetch_assoc($result)['total_applications'] ?? 0;

mysqli_close($conn);

// Fetch user data
include 'dbconn.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header('Location: login.php');
    exit;
}

$query = "SELECT * FROM registered_user WHERE id = ".$_SESSION['user_id'];
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

$image = $user_data['image'];
$full_name = $user_data['full_name'];
$created_at = $user_data['created_at'];

// Check for incomplete profile fields
$incomplete_profile = false;
$profile_fields = ['full_name', 'mobile_number', 'address_line_1', 'address_line_2', 'postcode', 'state'];
foreach ($profile_fields as $field) {
    if (empty($user_data[$field])) {
        $incomplete_profile = true;
        break;
    }
}

// Set the session variable if the profile is incomplete
if ($incomplete_profile) {
    $_SESSION['incomplete_profile_message'] = 'Please complete your profile information.';
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 80px;
        }
        .navbar {
            background-color: #b6c784;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            opacity: 0;
            transform: translateY(100px); /* Start cards from 100px below */
            animation: slideUp 0.8s ease forwards;
        }

        .card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Box shadow on hover */
        }
        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(100px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            display: flex;
            align-items: center;
            background: linear-gradient(90deg, #b6c784 100%, #8fab5b 50%);
            color: black;
        }
        .card-header i {
            margin-right: 10px;
            color: black;
        }
        .card-header .header-text {
            color: black;
        }
        .card-body h1 {
            font-size: 36px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .profile-image-container {
            text-align: center;
        }
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .btn-edit-profile {
            background-color: #b6c784 !important;
            color: black !important;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-edit-profile:hover {
            background-color: #a3b574 !important;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: none;
            border-radius: 0.25rem;
        }
        .alert .btn-close {
            color: #155724;
        }
        .card-height-adjust {
            height: 500px; /* Adjust this value as needed */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        #userInformationCard {
            height: 580px; /* Auto height based on content */
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        switch ($_SESSION['usertype']) {
            case 'admin':
                echo "<br><br><br><br>";
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

    <?php if (isset($_SESSION['login_success'])): ?>
        <div id="loginSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> You have successfully logged in.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    <!-- Display the incomplete profile message if set -->
    <?php if (isset($_SESSION['incomplete_profile_message'])): ?>
        <div class="alert alert-warning">
            <?= $_SESSION['incomplete_profile_message'] ?>
            <?php unset($_SESSION['incomplete_profile_message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-3">
                <div class="card mb-3" id="userInformationCard">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i>
                        <span class="header-text">User Information</span>
                    </div>
                    <div class="card-body">
                        <div class="text-center profile-image-container">
                            <?php if ($user_data['image']): ?>
                                <img src="storage/profile_images/<?php echo $user_data['image']; ?>" alt="Profile Image" class="profile-image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150" alt="Profile Image" class="profile-image">
                            <?php endif; ?>
                        </div>
                        <br>
                        <h5 class="text-center">Name: <?php echo $full_name;?></h5>
                        <p class="text-center">Email: <?php echo $user_data['email'];?></p>
                        <p class="text-center">Role: <?php echo ucfirst($user_data['usertype']);?></p>
                        <p class="text-center">Member since: <?php echo date("F d, Y h:i:s A", strtotime($created_at));?></p>
                        <div class="text-center mt-3">
                            <a href="profile.php" class="btn btn-edit-profile">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card mb-3" id="totalUsersCard">
                    <div class="card-header">
                        <i class="fas fa-users-cog"></i>
                        <span class="header-text">Total Users</span>
                    </div>
                    <div class="card-body">
                        <h1 class="text-center"><span id="totalUsersCount">0</span></h1>
                    </div>
                </div>
                <div class="card mb-3" id="totalApplicationsCard">
                    <div class="card-header">
                        <i class="fas fa-file-alt"></i>
                        <span class="header-text">Total Applications</span>
                    </div>
                    <div class="card-body">
                        <h1 class="text-center"><span id="totalApplicationsCount">0</span></h1>
                    </div>
                </div>
                <div class="card mb-3" id="userChartCard">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        <span class="header-text">Chart</span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to animate count-up effect
        function animateCountUp(targetElement, start, end, duration) {
            var range = end - start;
            var current = start;
            var increment = end > start ? 1 : -1;
            var stepTime = Math.abs(Math.floor(duration / range));
            var timer = setInterval(function() {
                current += increment;
                targetElement.innerText = current;
                if (current == end) {
                    clearInterval(timer);
                }
            }, stepTime);
        }

        $(document).ready(function(){
            // PHP variables to JavaScript
            var totalUsers = <?php echo $total_users; ?>;
            var totalApplications = <?php echo $total_applications; ?>;
            
            // Animate count-up for total users
            var totalUsersElement = document.getElementById('totalUsersCount');
            animateCountUp(totalUsersElement, 0, totalUsers, 1500); // Adjust duration as needed
            
            // Animate count-up for total applications
            var totalApplicationsElement = document.getElementById('totalApplicationsCount');
            animateCountUp(totalApplicationsElement, 0, totalApplications, 1500); // Adjust duration as needed

            // Animate cards one by one using delay
            $('.card').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.2) + 's' // Adjust delay between cards
                });
            });

            // Delay the chart rendering until the cards are done animating
            setTimeout(function() {
                var ctx = document.getElementById('userChart').getContext('2d');
                var userChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Total Users', 'Total Applications'],
                        datasets: [{
                            label: 'Count',
                            data: [totalUsers, totalApplications],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)', 
                                'rgba(54, 162, 235, 0.2)', 
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutBounce'
                        }
                    }
                });
            }, 200 * $('.card').length); // Adjust the delay to synchronize with card animations
        });
    </script>
</body>
</html>
