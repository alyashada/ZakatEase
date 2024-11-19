<?php
session_start();
require_once 'dbconn.php';

$query = "
    SELECT 
        r.id, r.email, r.username, r.full_name, r.mobile_number, a.matric_no, r.status 
    FROM 
        registered_user r 
    INNER JOIN 
        applicant_information a ON r.id = a.user_id 
    INNER JOIN 
        applicant_statement s ON a.id = s.application_id 
    WHERE 
        r.usertype = 'student' AND s.statement = 1
";
$result = mysqli_query($conn, $query);
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $query = "SELECT ai.*, ru.email, ru.full_name, ru.mobile_number, ru.address_line_1, ru.address_line_2, ru.postcode, ru.state 
              FROM applicant_information ai 
              JOIN registered_user ru ON ai.user_id = ru.id 
              WHERE ai.user_id = $userId";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $applicationData = mysqli_fetch_assoc($result);
    }
}

if (mysqli_num_rows($result) > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Student List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

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
            border-radius: 30px;
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
            padding: 20px;
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
        .table-responsive {
            overflow-x: auto;
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

    echo '<br><br><br><br>';
    if (isset($_GET['success'])) {
        $successMessage = $_GET['success'];
        echo '<div class="alert alert-success alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">' . htmlspecialchars($successMessage) . '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }

    if (isset($_GET['error'])) {
        $errorMessage = $_GET['error'];
        echo '<div class="alert alert-danger alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">' . htmlspecialchars($errorMessage) . '<button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
    ?>

    <div class="container mt-5">
        <h2 style="text-align: center;">List of Applications</h2><br>
        <div class="filter-container text-right mb-3 pr-3">
            <select class="custom-select" id="statusFilter">
                <option value="all">Filter by Status</option>
                <option value="all">All</option>
                <option value="accept">Accept</option>
                <option value="reject">Reject</option>
                <option value="pending">Pending</option>
            </select>
        </div><br>
        <form method="post" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Matric No." value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        
    <div class="table-responsive">
        <table class="table table-bordered " id="applicationTable">
            <thead>
                <tr style="border-bottom: 1px solid silver; border-top: 1px solid silver;text-align:center">
                    <th>ID</th>
                    <th>Matric No.</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Mobile No</th>
                    <th>Status</th>
                    <th>View Application</th>
                    <th>Delete Application</th>
                    <th>Update Status</th>
                    <th>View Interviewer Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query = "
                    SELECT 
                        r.id, r.email, r.username, r.full_name, r.mobile_number, a.matric_no, r.status 
                    FROM 
                        registered_user r 
                    INNER JOIN 
                        applicant_information a ON r.id = a.user_id 
                    INNER JOIN 
                        applicant_statement s ON a.id = s.application_id 
                    WHERE 
                        r.usertype = 'student' AND s.statement = 1
                ";
    
                if (isset($_POST['search']) && !empty($_POST['search'])) {
                    $search = '%' . $_POST['search'] . '%';
                    $query .= " AND a.matric_no LIKE ?";
                }
    
                $stmt = mysqli_prepare($conn, $query);
                if (isset($search)) {
                    mysqli_stmt_bind_param($stmt, "s", $search);
                }
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

             
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['matric_no']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['mobile_number']; ?></td>
                        <td class="status"><?php echo isset($row['status']) ? htmlspecialchars($row['status']) : 'Pending'; ?></td>
                        <td class="text-center">
                            <a href="view_applicationadmin.php?user_id=<?php echo $row['id']; ?>" class="btn btn-primary d-inline-block">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </td>

                        <td class="text-center">
                        <button type="button" class="btn btn-danger deleteApplication" data-application-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        </td>

                        <td class="text-center">
                        <button type="button" class="btn btn-primary updateStatusBtn" data-toggle="modal" data-target="#statusModal<?php echo $row['id']; ?>" data-user-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-edit"></i> 
                        </button>
                          <!-- Status Modal -->
                            <div class="modal fade" id="statusModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="statusModalLabel<?php echo $row['id']; ?>">Update Status</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="update_status.php" method="POST">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <div class="form-group">
                                                    <label for="action-select-<?php echo $row['id']; ?>">Action:</label>
                                                    <select class="form-control action-select" id="action-select-<?php echo $row['id']; ?>" name="action" aria-label="Select action" required>
                                                        <option value="">Select Action</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="accept">Accept</option>
                                                        <option value="reject">Reject</option>
                                                    </select>
                                                </div>
                                                <div class="form-group interviewer-select" id="interviewerSelect<?php echo $row['id']; ?>" style="display:none;">
                                                    <label for="interviewer-id-<?php echo $row['id']; ?>">Select Interviewer:</label>
                                                    <select class="form-control" id="interviewer-id-<?php echo $row['id']; ?>" name="interviewer_id">
                                                        <option value="">Select Interviewer</option>
                                                        <?php
                                                        // Fetch available interviewers from the database
                                                        $interviewers_query = "SELECT id, full_name FROM registered_user WHERE usertype = 'interviewer'";
                                                        $interviewers_result = mysqli_query($conn, $interviewers_query);
                                                        while ($interviewer = mysqli_fetch_assoc($interviewers_result)) {
                                                            echo '<option value="' . $interviewer['id'] . '">' . $interviewer['full_name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="view_feedback.php?user_id=<?php echo $row['id']; ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> 
                            </a>
                        </td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div><br><br><br>


    <script>
        $(document).ready(function() {
            $('.action-select').change(function() {
                var rowId = $(this).closest('.modal').attr('id').replace('statusModal', '');
                var selectedAction = $(this).val();

                if (selectedAction === 'accept') {
                    $('#interviewerSelect' + rowId).show();
                } else {
                    $('#interviewerSelect' + rowId).hide();
                }
            });

            $('#qrCodeScannerBtn').click(function() {
                $('#scanner-container').show();
                let scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });

                scanner.addListener('scan', function(content) {
                    alert('Scanned content: ' + content);
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

            $('#closeScannerBtn').click(function() {
                $('#scanner-container').hide();
                if (typeof scanner !== 'undefined' && scanner !== null) {
                    scanner.stop();
                }
            });
        });

        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert").alert('close');
            }, 3000);
        });

        $(document).ready(function() {
            $('.deleteApplication').click(function() {
                var userId = $(this).data('application-id');
                var confirmDelete = confirm('Are you sure you want to delete this application?');
                if (confirmDelete) {
                    $.ajax({
                        type: 'POST',
                        url: 'delete_applicationadmin.php',
                        data: {
                            user_id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Application deleted successfully');
                                window.location.reload();
                            } else {
                                alert('Error deleting application');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting application');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
        //
        $(document).ready(function() {
        // Function to filter rows based on selected status
        function filterRows(status) {
            $('#applicationTable tbody tr').each(function() {
                if (status === 'all' || $(this).find('.status').text().trim().toLowerCase() === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Event listener for dropdown change
        $('#statusFilter').change(function() {
            var selectedStatus = $(this).val();
            filterRows(selectedStatus);
        });

        // Initial filter application (optional)
        filterRows('all');
    });
    //
    $(document).ready(function() {
        // Track whether the update status modal has been opened
        var updateStatusModalOpened = false;

        // Event listener for update status button click
        $('.updateStatusBtn').click(function() {
            var userId = $(this).data('user-id');
            
            // If modal is opened for the first time, show reminder alert
            if (!updateStatusModalOpened) {
                alert('Reminder: Please update the status only once.');
                updateStatusModalOpened = true; // Set flag to true after showing alert
            }
        });

        // Event listener for action select change
        $('.action-select').change(function() {
            var rowId = $(this).closest('.modal').attr('id').replace('statusModal', '');
            var selectedAction = $(this).val();

            // Show interviewer selection if action is 'accept'
            if (selectedAction === 'accept') {
                $('#interviewerSelect' + rowId).show();
            } else {
                $('#interviewerSelect' + rowId).hide();
            }
        });
    });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php
} else {
    echo "No students found.";
}
mysqli_close($conn);
?>
