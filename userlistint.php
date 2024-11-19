<?php
session_start();
// Include the database connection file
require_once 'dbconn.php';

// Initialize the search variable
$search = "";
// Check if the search form is submitted
if(isset($_POST['search'])) {
    $search = $_POST['search'];
}
// Fetch data from the registered_user table with usertype "interviewer"
$query = "SELECT id, email, username, full_name, mobile_number, address_line_1, address_line_2, postcode, state FROM registered_user WHERE usertype = 'interviewer'";

// If search term is provided, add WHERE clause to filter by full_name
if(!empty($search)) {
    // Use prepared statement to prevent SQL injection
    $query .= " AND full_name LIKE ?";
    $stmt = mysqli_prepare($conn, $query);
    $searchParam = "%{$search}%";
    mysqli_stmt_bind_param($stmt, "s", $searchParam);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // No search term provided, execute the query directly
    $result = mysqli_query($conn, $query);
}

// Check if any rows were returned
if (mysqli_num_rows($result) > 0) {
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Student list</title>
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

        /* Add hover effect to table rows */
        table tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        /* Add box-shadow to the table */
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

        /* Apply animation to the table */
        table {
            animation: slideInFromBottom 1s ease-in-out;
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
    echo '<br><br><br><br>';
    // Check for success message in the URL
    if (isset($_GET['success'])) {
        $successMessage = $_GET['success'];
        echo '<div class="alert alert-success alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">' . htmlspecialchars($successMessage) . '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }

    // Check for error message in the URL
    if (isset($_GET['error'])) {
        $errorMessage = $_GET['error'];
        echo '<div class="alert alert-danger alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">' . htmlspecialchars($errorMessage) . '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
    ?>

    <div class="container mt-5">
        <h2 style="text-align: center;">Registered Interviewer</h2>
        <br>
         <!-- Search Form -->
         <form action="" method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by Full Name" name="search" value="<?php echo $search; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr style="border-bottom: 1px solid silver; border-top: 1px solid silver;text-align:center" >
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Mobile No</th>
                    <th>Address Line 1</th>
                    <th>Address Line 2</th>
                    <th>Postcode</th>
                    <th>State</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                
                
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['mobile_number']; ?></td>
                        <td><?php echo $row['address_line_1']; ?></td>
                        <td><?php echo $row['address_line_2']; ?></td>
                        <td><?php echo $row['postcode']; ?></td>
                        <td><?php echo $row['state']; ?></td>
                        <td>
                        <div class="d-flex">
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $row['id'];?>" data-email="<?php echo $row['email'];?>"  data-username="<?php echo $row['username'];?>" data-full-name="<?php echo $row['full_name'];?>" data-mobile-number="<?php echo $row['mobile_number'];?>"  data-address-line-1="<?php echo $row['address_line_1'];?>" data-address-line-2="<?php echo $row['address_line_2'];?>" data-postcode="<?php echo $row['postcode'];?>" data-state="<?php echo $row['state'];?>" >
                                <i class="fas fa-edit"></i> <!-- Font Awesome edit icon -->
                            </button>&nbsp;                        
                            <a href="userlistintdelete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> <!-- Font Awesome delete icon -->
                            </a>
                        </div>
                        </td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Student Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Edit form will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Edit button click event listener
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const email = this.getAttribute('data-email');
            const username = this.getAttribute('data-username');
            const fullName = this.getAttribute('data-full-name');
            const mobileNumber = this.getAttribute('data-mobile-number');
            const addressLine1 = this.getAttribute('data-address-line-1');
            const addressLine2 = this.getAttribute('data-address-line-2');
            const postcode = this.getAttribute('data-postcode');
            const state = this.getAttribute('data-state');

            // Create the edit form HTML
            const formHTML = `
                <form id="editForm" action="userlistintedit.php" method="post">
                    <input type="hidden" name="id" value="${id}">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="${email}" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="${username}" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="${fullName}" required>
                    </div>
                    <div class="mb-3">
                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" value="${mobileNumber}" required>
                    </div>
                    <div class="mb-3">
                        <label for="addressLine1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="addressLine1" name="addressLine1" value="${addressLine1}" required>
                    </div>
                    <div class="mb-3">
                        <label for="addressLine2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="addressLine2" name="addressLine2" value="${addressLine2}">
                    </div>
                    <div class="mb-3">
                        <label for="postcode" class="form-label">Postcode</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" value="${postcode}" required>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="${state}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            `;

            // Insert the edit form HTML into the modal body
            document.querySelector('.modal-body').innerHTML = formHTML;

            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });

    $(document).ready(function() {
        // Automatically close alert messages after a few seconds
        window.setTimeout(function() {
            $(".alert").alert('close');
        }, 3000);
    });
    </script>

<?php include 'footer.php'; ?>
</body>

</html>
<?php
} else {
    echo "No Interviewer found.";
}

// Close the database connection
mysqli_close($conn);
?>
