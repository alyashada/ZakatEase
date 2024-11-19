<?php
session_start();
require_once 'dbconn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header('Location: login.php');
    exit;
}

// Update the user data in the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image = $_FILES['image'];
    $full_name = trim(filter_var($_POST['full_name'], FILTER_SANITIZE_STRING));
    $mobile_number = trim(filter_var($_POST['mobile_number'], FILTER_SANITIZE_STRING));
    $address_line_1 = trim(filter_var($_POST['address_line_1'], FILTER_SANITIZE_STRING));
    $address_line_2 = trim(filter_var($_POST['address_line_2'], FILTER_SANITIZE_STRING));
    $postcode = trim(filter_var($_POST['postcode'], FILTER_SANITIZE_STRING));
    $state = trim(filter_var($_POST['state'], FILTER_SANITIZE_STRING));

    // Initialize variables for the SQL query
    $query = "UPDATE registered_user SET full_name = ?, mobile_number = ?, address_line_1 = ?, address_line_2 = ?, postcode = ?, state = ?";
    $params = [$full_name, $mobile_number, $address_line_1, $address_line_2, $postcode, $state];
    $param_types = "ssssss";

    // Check if a new image is uploaded
    if ($image['name']) {
        $image_path = 'storage/profile_images/' . $image['name'];
        move_uploaded_file($image['tmp_name'], $image_path);

        $query .= ", image = ?";
        array_push($params, $image['name']);
        $param_types .= "s";
    }

    $query .= " WHERE id = ?";
    array_push($params, $_SESSION['user_id']);
    $param_types .= "i";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();

    $_SESSION['update_message'] = 'Profile updated successfully!';
    header('Location: profile.php');
    exit;
}

// Retrieve the user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM registered_user WHERE id =?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Setting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <style>
         body {
        font-family: 'Roboto', sans-serif;
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

        .form-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .custom-login-btn {
            background-color: #b6c784 !important;
            border: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .custom-login-btn:hover {
            background-color: #a3b574 !important; /* Slightly darker shade for hover effect */
        }
    </style>
</head>
<?php
// Header section
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
<body>

<div class="container">
    <div class="text-center mt-5">
        <h2>Profile Settings</h2>
        <?php if (isset($_SESSION['update_message'])):?>
            <div class="alert alert-success" id="update-message">
                <?= $_SESSION['update_message']?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" id="dismiss-message">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['update_message']); endif;?>

        <div class="form-container">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image" class="profile-label">Profile Picture</label>
                    <div class="profile-image-container">
                        <?php if ($user_data['image']): ?>
                            <!-- If the user has uploaded an image, display it -->
                            <img src="storage/profile_images/<?php echo $user_data['image']; ?>" alt="Profile Image" class="profile-image">

                        <?php else: ?>
                            <!-- If no image is uploaded, use a default placeholder image -->
                            <img src="https://via.placeholder.com/150" alt="Profile Image" class="profile-image">
                        <?php endif; ?>
                    </div>

                    <div class="mt-3">
                        <input type="file" class="form-control-file" id="image" name="image">
                    </div>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user_data['full_name'];?>" required>
                </div>
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $user_data['mobile_number'];?>" required>
                </div>
                <div class="form-group">
                    <label for="address_line_1">Address Line 1</label>
                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="<?php echo $user_data['address_line_1'];?>">
                </div>
                <div class="form-group">
                    <label for="address_line_2">Address Line 2</label>
                    <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="<?php echo $user_data['address_line_2'];?>">
                </div>
                <div class="form-group">
                    <label for="postcode">Postcode</label>
                    <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo $user_data['postcode'];?>">
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" class="form-control" id="state" name="state" value="<?php echo $user_data['state'];?>">
                </div>
                <button type="submit" class="btn custom-login-btn btn-block mb-4">Update Profile</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Check if the update message exists and is not empty
        var updateMessage = '<?= isset($_SESSION['update_message']) && !empty($_SESSION['update_message']) ? $_SESSION['update_message'] : '' ?>';
        if (updateMessage) {
            // Show the update message
            $('#update-message').text(updateMessage).show();

            // Set a timeout to hide the message after 3 seconds
            setTimeout(function() {
                $('#update-message').fadeOut();
            }, 3000);
        }
    });
</script>
</body>
</html>
