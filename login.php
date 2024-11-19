<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['session_expired']) && $_GET['session_expired'] === 'true') {
    echo '<script type="text/javascript">alert("Your session has expired. Please log in again.");</script>';
    unset($_GET['session_expired']); 
}
require_once 'dbconn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $usertype = $_POST['usertype'];

    if (empty($username) || empty($password) || empty($usertype)) {
        $_SESSION['error'] = "Please enter all required fields";
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT * FROM registered_user WHERE username = ? AND usertype = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $usertype);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['loggedin'] = true;
                $_SESSION['usertype'] = $usertype;
                $_SESSION['login_success'] = 'Login successful!';
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Incorrect username or password";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Incorrect username or password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "An error occurred during login. Please try again later.";
        header("Location: login.php");
        exit();
    }
}

$success_message = '';
$logout_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['logout_success'])) {
    $logout_message = "You have successfully logged out.";
    unset($_SESSION['logout_success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zakat Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

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

        .login-image {
            animation: slideInRight 1s ease forwards;
        }
        .card {
            animation: fadeInUp 1s ease forwards;
        }

        .custom-login-btn {
            background-color: #b6c784 !important;
            border: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .custom-login-btn:hover {
            background-color: #a3b574 !important;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            padding-right: 40px;
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
</head>
<body>
    <?php include 'headerdef.php'; ?><br>
    <div class="container">
        <?php if ($success_message) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php } ?>

        <?php if ($logout_message) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $logout_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show floating-alert" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <section class="container">
            <div class="row gx-lg-5 align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 login-image">
                    <img src="images/loginimg.png" alt="Login image" class="img-fluid">
                </div>
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card">
                        <div class="card-body py-5 px-md-5">
                            <?php if (isset($_SESSION['error'])) {?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <?php echo $_SESSION['error'];?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php }?>
                            
                            <h1 class="mb-5 display-4 fw-bold ls-tight text-center" style="font-size: 2.5rem;">
                                ZakatEase <br>
                                <span class="text-primary" style="font-size: 2.0rem; color: #b6c784 !important;">Log In</span>
                            </h1>

                            <form method="post" action="login.php">
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
                                <div class="form-outline mb-4">
                                    <input type="text" id="username" name="username" class="form-control" required />
                                    <label class="form-label" for="username">Username</label>
                                </div>
                                <div class="form-outline mb-4">
                                    <div class="password-container">
                                        <i class="far fa-eye-slash" id="togglePassword"></i>
                                        <input type="password" id="password" name="password" class="form-control" required />
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn custom-login-btn btn-block mb-4">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section><br><br><br>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeAlert(alertElement) {
            setTimeout(function() {
                alertElement.remove();
            }, 5000);
        }

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

        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
