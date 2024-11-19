<?php
// Include the database connection file
require_once 'dbconn.php';
session_start();

// Define error messages
$email_error = $username_error = $password_error = $fullname_error = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $usertype = $_POST['usertype'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the form data
    if (empty($fullname)) {
        $fullname_error = 'Full name is required';
    }

    if (empty($email)) {
        $email_error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = 'Invalid email format';
    }

    if (empty($username)) {
        $username_error = 'Username is required';
    }

    if (empty($password)) {
        $password_error = 'Password is required';
    }

    // If there are no errors, insert the data into the database
    if ($fullname && $email && $username && $password) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert the data into the database
        $query = "INSERT INTO registered_user (usertype, email, username, password, full_name) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $usertype, $email, $username, $password_hash, $fullname);

        try {
            // Attempt to execute the query
            $result = $stmt->execute();

            if ($result) {
                // Success! Set a session variable with the success message
                session_start();
                $_SESSION['success_message'] = 'Registration successful! You can now log in.';
                header('Location: login.php');
                exit;
            } else {
                // Error! Display an error message
                $error = 'Error inserting data';
            }
        } catch (mysqli_sql_exception $e) {
            // Check if the error is due to a duplicate entry for email
            if ($e->getCode() == 1062) {
                $error = 'Email already exists. Please choose a different email.';
            } else {
                // Other database error
                $error = 'Error inserting data: '. $e->getMessage();
            }
        }

        // Close the statement
        $stmt->close();
    }

    // Close the connection
    $conn->close();
}
// Check for success message session variable
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the session variable
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zakat Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <style>
        body {
        font-family: 'Roboto', sans-serif;
        }
        /* CSS animation for card */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* CSS animation for image */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Apply animations to elements */
        .card {
            animation: fadeInUp 1s ease forwards;
        }

        .signup-image {
            animation: slideInRight 1s ease forwards;
        }

        /* CSS for floating alert */
        .floating-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: auto;
            max-width: 90%;
        }

        /* Custom styles for signup button */
        .custom-signup-btn {
            background-color: #b6c784 !important;
            border: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .custom-signup-btn:hover {
            background-color: #a3b574 !important; /* Slightly darker shade for hover effect */
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            padding-right: 40px; /*  space for the icon */
        }
        .password-container .fa-eye, .password-container .fa-eye-slash {
            position: absolute;
            right: 10px;
            top: 30%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 2;
        }
    </style>

    <script>
        // Add a function to remove the alert after a specified time
        function removeAlert(alertElement) {
            setTimeout(function() {
                alertElement.remove(); // Remove the alert from the DOM
            }, 5000); // Adjust the time (in milliseconds) as needed
        }

        // Call the removeAlert function for each alert
        document.addEventListener('DOMContentLoaded', function() {
            var errorAlert = document.querySelector('.alert-danger');
            var successAlert = document.querySelector('.alert-success');

            if (errorAlert) {
                removeAlert(errorAlert);
            }

            if (successAlert) {
                removeAlert(successAlert);
            }
        });
    </script>
</head>
<body>
    <?php include 'headerdef.php'; ?>
   
    <!-- Signup form section -->
    <br><section class="container">
        <div class="row gx-lg-5 align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 signup-image">
                <img src="images\signupimgh.png" alt="ZakatEase Logo" class="signup-image">
            </div>
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="card">
                    <div class="card-body py-5 px-md-5">
                        <h1 class="mb-5 display-4 fw-bold ls-tight text-center" style="font-size: 2.5rem;">
                            ZakatEase <br>
                            <span class="text-primary" style="font-size: 2.0rem; color: #b6c784 !important;">Sign Up</span>
                        </h1>
                        <form method="post" action="signup.php">
                            <!-- Radio buttons for staff or student -->
                            <div class="mb-4 d-flex flex-row">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="usertype" id="studentRadio" value="student">
                                    <label class="form-check-label" for="studentRadio">Student</label>
                                </div>
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="usertype" id="adminRadio" value="admin">
                                    <label class="form-check-label" for="adminRadio">Admin</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usertype" id="interviewerRadio" value="interviewer">
                                    <label class="form-check-label" for="interviewerRadio">Interviewer</label>
                                </div>
                            </div>

                            <!-- Full name -->
                            <div class="form-outline mb-4">
                                <input type="text" id="fullname" class="form-control" name="fullname" value="" required>
                                <label class="form-label" for="fullname">Full Name</label>
                            </div>

                            <!-- Email input -->
                            <div class="form-outline mb-4">
                                <input type="email" id="email" class="form-control" name="email" value="" required>
                                <label class="form-label" for="email">Email address</label>
                            </div>

                            <!-- Username -->
                            <div class="form-outline mb-4">
                                <input type="text" id="username" class="form-control" name="username" value="" required>
                                <label class="form-label" for="username">Username</label>
                            </div>

                            <!-- Password input -->
                            
                            <div class="form-outline mb-4">
                                <div class="password-container">
                                    <i class="far fa-eye-slash" id="togglePassword"></i>
                                    <input type="password" id="password" name="password" class="form-control" required />
                                    <label class="form-label" for="password">Password</label>
                                </div>
                            </div>
                            

                             <!-- Submit button -->
                             <button type="submit" class="btn custom-signup-btn btn-block mb-4">
                                Sign up
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section><br><br><br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Toggle the eye / eye-slash icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
