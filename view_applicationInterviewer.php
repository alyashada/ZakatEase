<?php
session_start();
require_once 'dbconn.php';

// Check if application ID is provided in the URL
if (isset($_GET['application_id'])) {
    $applicationId = $_GET['application_id'];

    // Retrieve application information based on the application ID
    $query = "SELECT ai.*, ru.email, ru.full_name, ru.mobile_number, ru.address_line_1, ru.address_line_2, ru.postcode, ru.state 
              FROM applicant_information ai 
              JOIN registered_user ru ON ai.user_id = ru.id 
              WHERE ai.id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $applicationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $applicationData = mysqli_fetch_assoc($result);

        // Retrieve parent details for the application
        $parentQuery = "SELECT * FROM parent_details WHERE application_id = ?";
        $stmt = mysqli_prepare($conn, $parentQuery);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $parentResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($parentResult) > 0) {
            $parentDetails = mysqli_fetch_assoc($parentResult);
        } else {
            $parentDetails = [];
        }

        // Retrieve dependency of the head of the family details
        $dependencyQuery = "SELECT * FROM depend_head_fam WHERE applicant_id = ?";
        $stmt = mysqli_prepare($conn, $dependencyQuery);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $dependencyResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($dependencyResult) > 0) {
            $dependencyDetails = mysqli_fetch_assoc($dependencyResult);
        } else {
            $dependencyDetails = [];
        }

        // Retrieve sibling details for the application
        $siblingQuery = "SELECT * FROM sibling_details WHERE application_id = ?";
        $stmt = mysqli_prepare($conn, $siblingQuery);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $siblingResult = mysqli_stmt_get_result($stmt);

        $siblingDetails = [];
        if (mysqli_num_rows($siblingResult) > 0) {
            while ($row = mysqli_fetch_assoc($siblingResult)) {
                $siblingDetails[] = $row;
            }
        }

        // Retrieve accommodation details for the application
        $accommodationQuery = "SELECT * FROM accommodation_information WHERE application_id = ?";
        $stmt = mysqli_prepare($conn, $accommodationQuery);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $accommodationResult = mysqli_stmt_get_result($stmt);

        $accommodationDetails = [];
        if (mysqli_num_rows($accommodationResult) > 0) {
            while ($row = mysqli_fetch_assoc($accommodationResult)) {
                $accommodationDetails[] = $row;
            }
        }

        // Retrieve aid details for the application
        $aidQuery = "SELECT * FROM details_of_aid WHERE application_id = ?";
        $stmt = mysqli_prepare($conn, $aidQuery);
        mysqli_stmt_bind_param($stmt, 'i', $applicationId);
        mysqli_stmt_execute($stmt);
        $aidResult = mysqli_stmt_get_result($stmt);

        $aidDetails = [];
        if (mysqli_num_rows($aidResult) > 0) {
            while ($row = mysqli_fetch_assoc($aidResult)) {
                $aidDetails[] = $row;
            }
        }
    }}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <style>
    body {
        font-family: 'Roboto', sans-serif;
        }
        .table-hover-effect {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 10px; /* Curved border */
        overflow: hidden; /* Ensures the border-radius works with the table */
        border: 1px solid #ddd; /* Line around the table */
    
    }
    .table-hover-effect:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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
    <div class="container mt-5">
        <h1 style="text-align:center">Student Application</h1><br>
        <h3 style="text-align:center">A: Applicant Details</h3><br>
        <form>
            <!-- Applicant details form fields -->
            <table class="table table-bordered table-hover-effect">
                <tr><th>Matric No:</th><td><?php echo htmlspecialchars($applicationData['matric_no']); ?></td></tr>
                <tr><th>IC No:</th><td><?php echo htmlspecialchars($applicationData['ic_no']); ?></td></tr>
                <tr><th>Full Name:</th><td><?php echo htmlspecialchars($applicationData['full_name']); ?></td></tr>
                <tr><th>Gender:</th><td><?php echo htmlspecialchars($applicationData['gender']); ?></td></tr>
                <tr><th>Religion:</th><td><?php echo htmlspecialchars($applicationData['religion']); ?></td></tr>
                <tr><th>Email:</th><td><?php echo htmlspecialchars($applicationData['email']); ?></td></tr>
                <tr><th>Semester:</th><td><?php echo htmlspecialchars($applicationData['semester']); ?></td></tr>
                <tr><th>Program:</th><td><?php echo htmlspecialchars($applicationData['program']); ?></td></tr>
                <tr><th>Faculty:</th><td><?php echo htmlspecialchars($applicationData['faculty']); ?></td></tr>
                <tr><th>Campus:</th><td><?php echo htmlspecialchars($applicationData['campus']); ?></td></tr>
                <tr><th>Current CGPA:</th><td><?php echo htmlspecialchars($applicationData['current_cgpa']); ?></td></tr>
                <tr><th>Name of Academic Advisor:</th><td><?php echo htmlspecialchars($applicationData['name_of_academic_advisor']); ?></td></tr>
                <tr><th>Address Line 1:</th><td><?php echo htmlspecialchars($applicationData['address_line_1']); ?></td></tr>
                <tr><th>Address Line 2:</th><td><?php echo htmlspecialchars($applicationData['address_line_2']); ?></td></tr>
                <tr><th>Postcode:</th><td><?php echo htmlspecialchars($applicationData['postcode']); ?></td></tr>
                <tr><th>State:</th><td><?php echo htmlspecialchars($applicationData['state']); ?></td></tr>
                <tr><th>Mailing Address:</th><td><?php echo htmlspecialchars($applicationData['mailing_address']); ?></td></tr>
                <tr><th>Mobile Number:</th><td><?php echo htmlspecialchars($applicationData['mobile_number']); ?></td></tr>
                <tr><th>Marital Status:</th><td><?php echo htmlspecialchars($applicationData['marital_status']); ?></td></tr>
                <tr><th>Bank Account No:</th><td><?php echo htmlspecialchars($applicationData['bank_account_no']); ?></td></tr>
                <tr><th>Bank Name:</th><td><?php echo htmlspecialchars($applicationData['bank_name']); ?></td></tr>
                <tr><th>Special Privileges:</th><td><?php echo htmlspecialchars($applicationData['special_privileges']); ?></td></tr>
                <tr>
                    <th>OKU Card:</th>
                    <td>
                        <?php
                        if (!empty($applicationData['present_oku_card'])) {
                            $cardPath = '/fypzakat7/' . htmlspecialchars($applicationData['present_oku_card']);
                            echo '<a href="' . $cardPath . '" target="_blank">View OKU Card</a>';
                        } else {
                            echo 'No OKU Card uploaded';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Confirmation Letter:</th>
                    <td>
                        <?php
                        if (!empty($applicationData['confirmation_letter'])) {
                            $letterPath = '/fypzakat7/' . htmlspecialchars($applicationData['confirmation_letter']);
                            echo '<a href="' . $letterPath . '" target="_blank">View Confirmation Letter</a>';
                        } else {
                            echo 'No Confirmation Letter uploaded';
                        }
                        ?>
                    </td>
                </tr>

            </table>
        </form>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">B. Family Background (Parents/Guardian)</h3><br>
        <table class="table table-bordered table-hover-effect">
        <tr><th>Father's Name/Guardian:</th><td><?php echo isset($parentDetails['father_name']) ? htmlspecialchars($parentDetails['father_name']) : ''; ?></td></tr>
            <tr><th>Father's Relationship:</th><td><?php echo isset($parentDetails['father_relationship']) ? htmlspecialchars($parentDetails['father_relationship']) : ''; ?></td></tr>
            <tr><th>Father's Status:</th><td><?php echo isset($parentDetails['father_status']) ? htmlspecialchars($parentDetails['father_status']) : ''; ?></td></tr>
            <tr><th>Father's Passed Away Date:</th><td><?php echo isset($parentDetails['father_passed_away_date']) ? htmlspecialchars($parentDetails['father_passed_away_date']) : ''; ?></td></tr>
            <tr><th>Father's Age:</th><td><?php echo isset($parentDetails['father_age']) ? htmlspecialchars($parentDetails['father_age']) : ''; ?></td></tr>
            <tr><th>Father's Occupation:</th><td><?php echo isset($parentDetails['father_occupation']) ? htmlspecialchars($parentDetails['father_occupation']) : ''; ?></td></tr>
            <tr><th>Father's Employer:</th><td><?php echo isset($parentDetails['father_employer']) ? htmlspecialchars($parentDetails['father_employer']) : ''; ?></td></tr>
            <tr><th>Father's Phone No:</th><td><?php echo isset($parentDetails['father_phone_no']) ? htmlspecialchars($parentDetails['father_phone_no']) : ''; ?></td></tr>
            <tr><th>Father's Health Info:</th><td><?php echo isset($parentDetails['father_health_info']) ? htmlspecialchars($parentDetails['father_health_info']) : ''; ?></td></tr>
            <tr><th>Mother's Name:</th><td><?php echo isset($parentDetails['mother_name']) ? htmlspecialchars($parentDetails['mother_name']) : ''; ?></td></tr>
            <tr><th>Mother's Relationship:</th><td><?php echo isset($parentDetails['mother_relationship']) ? htmlspecialchars($parentDetails['mother_relationship']) : ''; ?></td></tr>
            <tr><th>Mother's Status:</th><td><?php echo isset($parentDetails['mother_status']) ? htmlspecialchars($parentDetails['mother_status']) : ''; ?></td></tr>
            <tr><th>Mother's Passed Away Date:</th><td><?php echo isset($parentDetails['mother_passed_away_date']) ? htmlspecialchars($parentDetails['mother_passed_away_date']) : ''; ?></td></tr>
            <tr><th>Mother's Age:</th><td><?php echo isset($parentDetails['mother_age']) ? htmlspecialchars($parentDetails['mother_age']) : ''; ?></td></tr>
            <tr><th>Mother's Occupation:</th><td><?php echo isset($parentDetails['mother_occupation']) ? htmlspecialchars($parentDetails['mother_occupation']) : ''; ?></td></tr>
            <tr><th>Mother's Employer:</th><td><?php echo isset($parentDetails['mother_employer']) ? htmlspecialchars($parentDetails['mother_employer']) : ''; ?></td></tr>
            <tr><th>Mother's Phone No:</th><td><?php echo isset($parentDetails['mother_phone_no']) ? htmlspecialchars($parentDetails['mother_phone_no']) : ''; ?></td></tr>
            <tr><th>Mother's Health Info:</th><td><?php echo isset($parentDetails['mother_health_info']) ? htmlspecialchars($parentDetails['mother_health_info']) : ''; ?></td></tr>
            <tr><th>Other Info:</th><td><?php echo isset($parentDetails['other_info']) ? htmlspecialchars($parentDetails['other_info']) : ''; ?></td></tr>
            <tr><th>Father's Income:</th><td><?php echo isset($parentDetails['father_income']) ? htmlspecialchars($parentDetails['father_income']) : ''; ?></td></tr>
            <tr><th>Mother's Income:</th><td><?php echo isset($parentDetails['mother_income']) ? htmlspecialchars($parentDetails['mother_income']) : ''; ?></td></tr>
            <tr><th>Other Income:</th><td><?php echo isset($parentDetails['other_income']) ? htmlspecialchars($parentDetails['other_income']) : ''; ?></td></tr>
            <tr><th>Total Income:</th><td><?php echo isset($parentDetails['total_income']) ? htmlspecialchars($parentDetails['total_income']) : ''; ?></td></tr>
        </table>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">C. Dependency of Head of the Family Details</h3><br>
        <table class="table table-bordered table-hover-effect">
        <tr><th>City of Residence:</th><td><?php echo isset($dependencyDetails['city_of_residence']) ? htmlspecialchars($dependencyDetails['city_of_residence']) : ''; ?></td></tr>
            <tr><th>Head of Family:</th><td><?php echo isset($dependencyDetails['head_of_family']) ? htmlspecialchars($dependencyDetails['head_of_family']) : ''; ?></td></tr>
            <tr><th>Working Status:</th><td><?php echo isset($dependencyDetails['working_status']) ? htmlspecialchars($dependencyDetails['working_status']) : ''; ?></td></tr>
            <tr><th>Working Adults:</th><td><?php echo isset($dependencyDetails['working_adults']) ? htmlspecialchars($dependencyDetails['working_adults']) : ''; ?></td></tr>
            <tr><th>Unemployed Adults:</th><td><?php echo isset($dependencyDetails['unemployed_adults']) ? htmlspecialchars($dependencyDetails['unemployed_adults']) : ''; ?></td></tr>
            <tr><th>IPT Students:</th><td><?php echo isset($dependencyDetails['ipt_students']) ? htmlspecialchars($dependencyDetails['ipt_students']) : ''; ?></td></tr>
            <tr><th>School Children:</th><td><?php echo isset($dependencyDetails['school_children']) ? htmlspecialchars($dependencyDetails['school_children']) : ''; ?></td></tr>
            <tr><th>Children 5 Years and Under:</th><td><?php echo isset($dependencyDetails['children_5_years_and_under']) ? htmlspecialchars($dependencyDetails['children_5_years_and_under']) : ''; ?></td></tr>
        </table>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">D. SIBLING DETAILS</h3><br>
        <form>
            <?php if (!empty($siblingDetails)): ?>
                <table class="table table-bordered table-hover-effect">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Relationship</th>
                            <th>Age</th>
                            <th>Marital Status</th>
                            <th>Working Status</th>
                            <th>Position</th>
                            <th>Employer</th>
                            <th>Additional Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siblingDetails as $sibling): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sibling['name']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['relationship']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['age']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['marital_status']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['working_status']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['position']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['employer']); ?></td>
                                <td><?php echo htmlspecialchars($sibling['add_info']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No sibling details available.</p>
            <?php endif; ?>
        </form>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">E. FINANCIAL INFORMATION</h3><br>
        <table class="table table-bordered table-hover-effect">
            <?php
            // Retrieve financial information for the application
            $financialQuery = "SELECT * FROM financial_information WHERE application_id = ?";
            $stmt = mysqli_prepare($conn, $financialQuery);
            mysqli_stmt_bind_param($stmt, 'i', $applicationData['id']);
            mysqli_stmt_execute($stmt);
            $financialResult = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($financialResult) > 0) {
                $financialData = mysqli_fetch_assoc($financialResult);
            ?>
                <tr><th>Sponsorship/Loan:</th><td><?php echo htmlspecialchars($financialData['sponsorship_loan']); ?></td></tr>
                <tr><th>Reason:</th><td><?php echo htmlspecialchars($financialData['reason']); ?></td></tr>
                <tr><th>Source:</th><td><?php echo htmlspecialchars($financialData['source']); ?></td></tr>
                <tr><th>Sponsorship Amount:</th><td><?php echo htmlspecialchars($financialData['sponsorship_amount']); ?></td></tr>
                <tr><th>Sponsorship Balance:</th><td><?php echo htmlspecialchars($financialData['sponsorship_balance']); ?></td></tr>
                <tr><th>Tuition Fee:</th><td><?php echo htmlspecialchars($financialData['tuition_fee']); ?></td></tr>
                <tr><th>Fee Status:</th><td><?php echo htmlspecialchars($financialData['fee_status']); ?></td></tr>
            <?php
            } else {
                echo "<tr><td colspan='2'>No financial information available.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">F. ACCOMMODATION INFORMATION (COLLEGE / RENT HOUSE / FAMILY HOUSE)</h3><br>
        <?php if (!empty($accommodationDetails)): ?>
            <?php foreach ($accommodationDetails as $accommodation): ?>
                <table class="table table-bordered table-hover-effect">
                    <tr><th>ID:</th><td><?php echo htmlspecialchars($accommodation['id']); ?></td></tr>
                    <tr><th>Accommodation Type:</th><td><?php echo htmlspecialchars($accommodation['accommodation_type']); ?></td></tr>
                    <tr><th>Accommodation Fee:</th><td><?php echo htmlspecialchars($accommodation['accommodation_fee']); ?></td></tr>
                    <tr><th>Payment Rate:</th><td><?php echo htmlspecialchars($accommodation['payment_rate']); ?></td></tr>
                    <tr><th>Vehicle:</th><td><?php echo htmlspecialchars($accommodation['vehicle']); ?></td></tr>
                    <tr><th>Vehicle Type:</th><td><?php echo htmlspecialchars($accommodation['vehicle_type']); ?></td></tr>
                    <tr><th>Vehicle Model:</th><td><?php echo htmlspecialchars($accommodation['vehicle_model']); ?></td></tr>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No accommodation details available.</p>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h3 style="text-align:center">G. DETAILS OF AID</h3><br>
        <?php if (!empty($aidDetails)): ?>
            <?php foreach ($aidDetails as $aid): ?>
                <table class="table table-bordered table-hover-effect">
                    <tr><th>Detail ID:</th><td><?php echo htmlspecialchars($aid['detail_id']); ?></td></tr>
                    <tr><th>Zakat Received:</th><td><?php echo htmlspecialchars($aid['zakat_received']); ?></td></tr>
                    <tr><th>Zakat Year:</th><td><?php echo htmlspecialchars($aid['zakat_year']); ?></td></tr>
                    <tr><th>Zakat Semester:</th><td><?php echo htmlspecialchars($aid['zakat_semester']); ?></td></tr>
                    <tr><th>Zakat Amount:</th><td><?php echo htmlspecialchars($aid['zakat_amount']); ?></td></tr>
                    <tr><th>Zakat Purpose:</th><td><?php echo htmlspecialchars($aid['zakat_purpose']); ?></td></tr>
                    <tr>
                        <th>Zakat Documents:</th>
                        <td>
                            <?php
                            if (!empty($aid['zakat_documents'])) {
                                // Adjust the path to match the actual folder structure
                                $documentPath = '/fypzakat7/' . htmlspecialchars($aid['zakat_documents']);
                                echo '<a href="' . $documentPath . '" target="_blank">View Document</a>';
                            } else {
                                echo 'No document available.';
                            }
                            ?>
                        </td>
                    </tr>           
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No aid details available.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
