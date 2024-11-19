<?php
error_reporting(E_ALL & ~E_NOTICE);
include 'depend_headfam.php';
include 'applicant_information.php';
include 'parent_details.php';
include 'sibling_info.php';
include 'financial_info.php';
include 'accommodation_info.php';
include 'detailaid_info.php';
include 'applicant_statement.php';

// Fetch existing user data if available
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $userId = $_SESSION['user_id'];
    $userData = getUserData($userId);
}

// Redirect to another page if the application has been submitted
if (isset($_SESSION['application_submitted']) && $_SESSION['application_submitted'] == true) {
    echo "<script>
            alert('You have already submitted your application for this semester. Please wait for our response via email.');
            window.location.href = 'dashboard.php'; // Redirect to another page or show a different message
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Multi-page Form</title>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="images/favicon.png">

<style>
    body {
        font-family: 'Roboto', sans-serif;
        }
    /* Style untuk form */
    .form-page {
        display: none;
    }
    .form-page.active {
        display: block;
    }
   
    .table-bordered {
        border: 1px solid #ddd;
        border-collapse: collapse;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .table-bordered th {
        background-color: #f2f2f2;
    }

    .custom-next-btn {
        background-color: #b6c784 !important;
        border: none;
        color: white;
        transition: background-color 0.3s ease;
    }

    .custom-next-btn:hover {
        background-color: #a3b574 !important; 
    }

    .button-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .alert-center {
        margin: 0 auto;
        text-align: center;
    }
</style>
</head>
<body>
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

    echo '<br>';
    if (isset($_GET['success'])) {
        $successMessage = $_GET['success'];
        echo '<div class="alert alert-success alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">
                ' . htmlspecialchars($successMessage) . '
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
    }
    
    if (isset($_GET['error'])) {
        $errorMessage = $_GET['error'];
        echo '<div class="alert alert-danger alert-dismissible fade show alert-center" role="alert" style="width: fit-content;">
                ' . htmlspecialchars($errorMessage) . '
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
    }
    
    ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- info 1 -->
            <div class="form-page active" id="form-page-1">
                <br><br>
                <h2 style="text-align:center">Application for Zakat Assistance via UiTM Perlis Zakat Sites</h2>
                <p style="text-align:center">Application Opens: March 12, 2024 - April 15, 2024<br><br>
                This application is for self-support, fee assistance and laptop assistance (for all students) at UiTM Perlis Branch.</p>
                <div class="button-container mt-4">
                <button onclick="nextPage()" class="btn custom-next-btn">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                </div>
            </div>

            <!-- info 2 -->
            <div class="form-page " id="form-page-2"><br><br>
                <h2 style="text-align:center">Applicant Instructions</h2>
                    <ol>
                        <li>Fill out the application form in CAPITAL LETTERS.</li>
                        <li>Ensure all information in each section is completed FULLY.</li>
                        <li>Check UiTM Perlis Kifayah Limit.</li>
                        <li>All supporting documents MUST BE CERTIFIED by an Officer (Academic Advisor/Course Coordinator/Head of Study Center).</li>
                        <li>Applicants must inform the certifying officer to fill out the Confirmation Form.</li>
                        <li>Ensure supporting documents are arranged in the CORRECT ORDER and merged into 1 PDF file.</li>
                        <li>Incomplete applications and those not following the GIVEN INSTRUCTIONS will NOT BE PROCESSED.</li>
                        <li>Applicants can refer to Frequently Asked Questions (FAQs) for any queries.</li>
                    </ol>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- info 3 -->
            <div class="form-page " id="form-page-3"><br><br>
                <h2 style="text-align:center">REQUIREMENTS FOR APPLICANTS OF STUDENT ZAKAT ASSISTANCE ( ONLINE )</h2>
                    <ol>
                        <li>Islam.</li>
                        <li>Full-time UiTM students (Diploma / Degree).</li>
                        <li>Not receiving financial sponsorship (scholarship/bursary) from any institution such as JPA, MARA and so on.</li>
                        <li>Not subject to any disciplinary action by the University.</li>
                        <li>A large number of siblings or dependent parents/guardians.</li>
                        <li>Application is only allowed once per semester.</li>
                        <li>The total family income does not exceed RM4000.</li>
                    </ol>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- info 4 -->
            <div class="form-page " id="form-page-4"><br><br>
                <h2  style="text-align:center">LIST OF DOCUMENTS TO BE CERTIFIED & UPLOADED WITH THE APPLICATION FORM</h2>
                    <ol>
                        <li>Student ID card @ UiTM offer letter</li>
                        <li>Final exam results*</li>
                        <li>Parent/guardian's declaration letter (Link: <a href="https://tinyurl.com/tanggungan">Tanggungan</a>)</li>
                        <li>Income verification letter (Link: <a href="https://tinyurl.com/gajisah">Gaji Sah</a>)</li>
                        <li>Lecturer support letter** (Link: <a href="https://tinyurl.com/suratSokonganKomputerRiba">Support Letter for Laptop</a>)</li>
                        <li>Unpaid fee statement from student portal***</li>
                        <li>Other supporting documents (e.g., death certificate, divorce certificate, medical declaration letter, etc.)</li>
                        <li>Note: Required documents must be provided in ONE (1) PDF file.</li>
                        <li>*exempted for semester 1 diploma and foundation (bachelor's degree)</li>
                        <li>**additional document for Laptop Aid Zakat.</li>
                        <li>***additional document for Fee Assistance Zakat</li>
                    </ol>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
            <!-- Borang 1 -->
            <div class="form-page" id="form-page-5"><br><br>
            
                <h2 style="text-align:center">A. APPLICANT'S PERSONAL DETAILS</h2>
                <form method="POST" action="applicant_information.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="matricNo">Matric No.</label>
                            <input type="number" class="form-control" id="matricNo" name="matricNo" placeholder="Enter your Matric No (e.g., 2021485546)" value="<?php echo isset($applicantData['matric_no']) ? $applicantData['matric_no'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="icNo">IC No.</label>
                            <input type="number" class="form-control" id="icNo" name="icNo" placeholder="Enter your IC (e.g., 011212140616 with no '-')" value="<?php echo isset($applicantData['ic_no']) ? $applicantData['ic_no'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your Full Name" value="<?php echo isset($userData['full_name']) ? $userData['full_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" <?php echo (isset($applicantData['gender']) && $applicantData['gender'] === 'male') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?php echo (isset($applicantData['gender']) && $applicantData['gender'] === 'female') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="religion">Religion</label>
                            <input type="text" class="form-control" id="religion" name="religion" placeholder="Enter your Religion (e.g., ISLAM)" value="<?php echo isset($applicantData['religion']) ? $applicantData['religion'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email (e.g., alyashada@gmail.com)" value="<?php echo isset($userData['email']) ? $userData['email'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="number" class="form-control" id="semester" name="semester" placeholder="Enter your Semester (e.g., 6)" value="<?php echo isset($applicantData['semester']) ? $applicantData['semester'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="program">Program</label>
                            <input type="text" class="form-control" id="program" name="program" placeholder="Enter your Program (e.g., BACHELOR OF INFORMATION TECHNOLOGY)" value="<?php echo isset($applicantData['program']) ? $applicantData['program'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" class="form-control" id="faculty" name="faculty" placeholder="Enter your Faculty (e.g., FACULTY OF COMPUTER SCIENCE AND MATHEMATICS)" value="<?php echo isset($applicantData['faculty']) ? $applicantData['faculty'] : ''; ?>"  required>
                        </div>
                        <div class="form-group">
                            <label for="campus">Campus</label>
                            <input type="text" class="form-control" id="campus" name="campus" placeholder="Enter your Campus (e.g., ARAU)" value="<?php echo isset($applicantData['campus']) ? $applicantData['campus'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="currentCGPA">Current CGPA</label>
                            <input type="number" step="0.01" class="form-control" id="currentCGPA" name="currentCGPA" placeholder="Enter your Current CGPA (e.g., 3.54)" value="<?php echo isset($applicantData['current_cgpa']) ? $applicantData['current_cgpa'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nameOfAcademicAdvisor">Name of Academic Advisor</label>
                            <input type="text" class="form-control" id="nameOfAcademicAdvisor" name="nameOfAcademicAdvisor" placeholder="Enter your Advisor Name" value="<?php echo isset($applicantData['name_of_academic_advisor']) ? $applicantData['name_of_academic_advisor'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="addressLine1">Address Line 1</label>
                            <input type="text" class="form-control" id="addressLine1" name="addressLine1" placeholder="Enter your Address Line 1" value="<?php echo isset($userData['address_line_1']) ? $userData['address_line_1'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="addressLine2">Address Line 2</label>
                            <input type="text" class="form-control" id="addressLine2" name="addressLine2" placeholder="Enter your Address Line 2" value="<?php echo isset($userData['address_line_2']) ? $userData['address_line_2'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="number" class="form-control" id="postcode" name="postcode" placeholder="Enter your Postcode (e.g., 57000)" value="<?php echo isset($userData['postcode']) ? $userData['postcode'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" class="form-control" id="state" name="state" placeholder="Enter your State" value="<?php echo isset($userData['state']) ? $userData['state'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="mailingAddress" title="Optional">Mailing Address <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="mailingAddress" name="mailingAddress" placeholder="Enter your Mailing Address" value="<?php echo isset($applicantData['mailing_address']) ? $applicantData['mailing_address'] : ''; ?>">
                          </div>
                        <div class="form-group">
                            <label for="maritalStatus">Marital Status</label>
                            <input type="text" class="form-control" id="maritalStatus" name="maritalStatus" placeholder="Enter your Marital Status (e.g., SINGLE)" value="<?php echo isset($applicantData['marital_status']) ? $applicantData['marital_status'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="mobileNumber">Applicant Phone.No</label>
                            <input type="number" class="form-control" id="mobileNumber" name="mobileNumber" placeholder="Enter your Phone Number (e.g., 01125706313)" value="<?php echo isset($userData['mobile_number']) ? $userData['mobile_number'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="bankAccountNo">Bank Account.No</label>
                            <input type="number" class="form-control" id="bankAccountNo" name="bankAccountNo" placeholder="Enter your Bank Account Number (e.g., 14229020255307)" value="<?php echo isset($applicantData['bank_account_no']) ? $applicantData['bank_account_no'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="bankName">Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bankName" placeholder="Enter your Bank Name (e.g., BANK ISLAM)" value="<?php echo isset($applicantData['bank_name']) ? $applicantData['bank_name'] : ''; ?>" required>
                        </div>
                        <?php
                        $selectedPrivilege = isset($applicantData['special_privileges']) ? $applicantData['special_privileges'] : ''; // Retrieve the selected privilege from the applicant data
                        ?>
                        <div class="form-group">
                            <label for="specialPrivileges">Special Privileges</label>
                            <select class="form-control" id="specialPrivileges" name="specialPrivileges" required>
                                <option value="" selected disabled>Please select one</option>
                                <?php
                                $specialPrivileges = array(
                                    "NOT APPLICABLE",
                                    "EAR",
                                    "HAND/LEG",
                                    "NASAL VOICE",
                                    "SPEECH (STUTTERING)",
                                    "SPEECH (MUTE)",
                                    "SPEECH",
                                    "VISION (BLURRED, USING SPECIAL LENSES)",
                                    "VISION (BLIND)",
                                    "VISION (COLOR BLIND)",
                                    "VISION (BLIND IN ONE EYE)",
                                    "HEARING (DEAF)",
                                    "HEARING (DEAF, USING AID)",
                                    "EYE",
                                    "LEARNING DISABILITY",
                                    "PARALYSIS",
                                    "OTHER DISABILITY",
                                    "HAND DISABILITY",
                                    "LEG DISABILITY",
                                    "DWARFISM",
                                    "OTHER"

                                );

                                foreach ($specialPrivileges as $privilege) {
                                    $selected = ($selectedPrivilege === $privilege) ? 'selected' : ''; // Check if the privilege is selected
                                    echo "<option value=\"$privilege\" $selected>$privilege</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">

                            <label for="presentTheOKUCard" title="Optional">Present the OKU Card <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <?php if (!empty($applicantData['present_the_oku_card'])): ?>
                                <div>
                                    <a href="<?php echo $applicantData['present_the_oku_card']; ?>" target="_blank">View OKU Card</a>
                                </div>
                                <div class="mt-2">
                                    <?php if (!empty($presentTheOkuCardPath)): ?>
                                        <a href="<?php echo $presentTheOkuCardPath; ?>" target="_blank">View Uploaded File</a>
                                    <?php endif; ?>
                                    <label for="updatePresentTheOKUCard" class="btn btn-primary">Update OKU Card</label>
                                    <input type="file" id="updatePresentTheOKUCard" name="updatePresentTheOKUCard" style="display: none;" onchange="updateFileName(this)">
                                    <span id="updatePresentTheOKUCardName"></span>
                                </div>
                            <?php else: ?>
                                <input type="file" id="presentTheOKUCard" name="presentTheOKUCard" onchange="updateFileName(this)">
                                <span id="presentTheOKUCardName"></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="confirmationLetter" title="Optional">Confirmation Letter <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <?php if (!empty($applicantData['confirmation_letter'])): ?>
                                <div>
                                    <a href="<?php echo $applicantData['confirmation_letter']; ?>" target="_blank">View Confirmation Letter</a>
                                </div>
                                <div class="mt-2">
                                    <?php if (!empty($confirmationLetterPath)): ?>
                                        <a href="<?php echo $confirmationLetterPath; ?>" target="_blank">View Uploaded File</a>
                                    <?php endif; ?>
                                    <label for="updateConfirmationLetter" class="btn btn-primary">Update Confirmation Letter</label>
                                    <input type="file" id="updateConfirmationLetter" name="updateConfirmationLetter" style="display: none;" onchange="updateFileName(this)">
                                    <span id="updateConfirmationLetterName"></span>
                                </div>
                            <?php else: ?>
                                <input type="file" id="confirmationLetter" name="confirmationLetter" onchange="updateFileName(this)">
                                <span id="confirmationLetterName"></span>
                                <?php endif; ?>
                        </div>

                        <input type="hidden" name="id" value="<?php echo isset($_POST['id']) ? $_POST['id'] : ''; ?>">
                        <button type="submit" id="saveButton" class="btn btn-primary">Save</button><br>
                    </form>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- B. FAMILY BACKGROUND (PARENTS/GUARDIAN) -->
            <div class="form-page" id="form-page-6"><br><br>
            
                <h2 style="text-align:center">B. FAMILY BACKGROUND (PARENTS/GUARDIAN)</h2>
                <form method="POST" action="parent_details.php" enctype="multipart/form-data">
                    <!-- Isi borang di sini -->
                    <div class="form-group">
                        <label for="fatherName">Father's Name/Guardian</label>
                        <input type="text" class="form-control" id="fatherName" name="fatherName" placeholder="Enter your Father's or Guardian Name" value="<?php echo isset($parentDetails['father_name']) ? $parentDetails['father_name'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fatherRelationship">Relationship</label>
                        <select class="form-control" id="fatherRelationship" name="fatherRelationship" required>
                            <option value="" selected disabled>Please select one</option>
                            <?php
                            $fatherRelationships = array(
                                "Mother",
                                "Father",
                                "Guardian",
                                "Husband/Wife",
                                "Grandfather/Grandmother",
                                "Older Sister",
                                "Older Brother",
                                "Younger Sister/Younger Brother",
                                "Relatives",
                                "Others",
                                "Tiada"
                            );

                            foreach ($fatherRelationships as $fRelationship) {
                                $selected = (isset($parentDetails['father_relationship']) && $parentDetails['father_relationship'] === $fRelationship) ? 'selected' : '';
                                echo "<option value=\"$fRelationship\" $selected>$fRelationship</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fatherStatus">Status</label><br>
                    
                        <input type="radio" id="fatherStatusAlive" name="fatherStatus" value="STILL ALIVE" <?php echo (isset($parentDetails) && isset($parentDetails['father_status']) && $parentDetails['father_status'] === 'STILL ALIVE') ? 'checked' : ''; ?> required>
                        <label for="fatherStatusAlive">Still Alive</label><br>
                        <input type="radio" id="fatherStatusDivorced" name="fatherStatus" value="DIVORCED" <?php echo (isset($parentDetails['father_status']) && $parentDetails['father_status'] === 'DIVORCED') ? 'checked' : ''; ?> required>
                        <label for="fatherStatusDivorced">Divorced</label><br>
                        <input type="radio" id="fatherStatusPassedAway" name="fatherStatus" value="PASSED AWAY" <?php echo (isset($parentDetails['father_status']) && $parentDetails['father_status'] === 'PASSED AWAY') ? 'checked' : ''; ?> required>
                        <label for="fatherStatusPassedAway">Passed Away</label><br>
                        <input type="radio" id="fatherStatusNo" name="fatherStatus" value="NO STATUS" <?php echo (isset($parentDetails) && isset($parentDetails['father_status']) && $parentDetails['father_status'] === 'NO STATUS') ? 'checked' : ''; ?> required>
                        <label for="fatherStatusNo">No Status</label><br>
                    </div>

                    <div class="form-group">
                        <label for="fatherPassedAwayDate" title="Optional">Date Father Passed Away  <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="date" class="form-control" id="fatherPassedAwayDate" name="fatherPassedAwayDate" value="<?php echo isset($parentDetails['father_passed_away_date']) ? $parentDetails['father_passed_away_date'] : ''; ?>">
                    </div>
                        <div class="form-group">
                            <label for="fatherAge" title="Optional">Age <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if not applicable"></i></label>
                            <input type="number" class="form-control" id="fatherAge" name="fatherAge" placeholder="Enter your Father's or Guardian Age (e.g., 56)" value="<?php echo isset($parentDetails['father_age']) ? $parentDetails['father_age'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="fatherOccupation" title="Optional">Occupation <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="fatherOccupation" name="fatherOccupation" placeholder="Enter your Father's or Guardian Ocuupation (e.g., POLICE OFFICER)" value="<?php echo isset($parentDetails['father_occupation']) ? htmlspecialchars($parentDetails['father_occupation']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="fatherEmployer" title="Optional">Employer <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="fatherEmployer" name="fatherEmployer" placeholder="Enter your Father's or Guardian Employer (e.g., COCHLEAR SDN.BHD)" value="<?php echo isset($parentDetails['father_employer']) ? htmlspecialchars($parentDetails['father_employer']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="fatherPhoneNo">Phone No. Father/Guardian</label>
                            <input type="number" class="form-control" id="fatherPhoneNo" name="fatherPhoneNo" placeholder="Enter your Father's or Guardian Phone Number (e.g., 0186789856)" value="<?php echo isset($parentDetails['father_phone_no']) ? $parentDetails['father_phone_no'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fatherHealthInfo">Father/Guardian Health Information</label>
                            <input type="text" class="form-control" id="fatherHealthInfo" name="fatherHealthInfo" placeholder="Enter your Father's or Guardian Health Information (e.g., DIABETES)" value="<?php echo isset($parentDetails['father_health_info']) ? htmlspecialchars($parentDetails['father_health_info']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="motherName" title="Optional">Mother's Name/Guardian <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="motherName" name="motherName"placeholder="Enter your Mother's Name" value="<?php echo isset($parentDetails['mother_name']) ? htmlspecialchars($parentDetails['mother_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motherRelationship" title="Optional">Relationship <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <select class="form-control" id="motherRelationship" name="motherRelationship" >
                                <option value="" selected disabled>Please select one</option>
                                <?php
                                $motherRelationships = array(
                                    "Mother",
                                    "Father",
                                    "Guardian",
                                    "Husband/Wife",
                                    "Grandfather/Grandmother",
                                    "Older Sister",
                                    "Older Brother",
                                    "Younger Sister/Younger Brother",
                                    "Relatives",
                                    "Others",
                                    "Tiada"
                                );

                                foreach ($motherRelationships as $mRelationship) {
                                    $selected = isset($parentDetails['mother_relationship']) && $parentDetails['mother_relationship'] === $mRelationship ? 'selected' : '';
                                    echo "<option value=\"$mRelationship\" $selected>$mRelationship</option>";
                                }
                                ?>
                            </select>
                        </div><div class="form-group">
                            <label for="motherStatus">Status</label><br>
                            <input type="radio" id="motherStatusAlive" name="motherStatus" value="STILL ALIVE" <?php echo isset($parentDetails['mother_status']) && $parentDetails['mother_status'] === 'STILL ALIVE' ? 'checked' : ''; ?> >
                            <label for="motherStatusAlive">Still Alive</label><br>
                            <input type="radio" id="motherStatusDivorced" name="motherStatus" value="DIVORCED" <?php echo isset($parentDetails['mother_status']) && $parentDetails['mother_status'] === 'DIVORCED' ? 'checked' : ''; ?> >
                            <label for="motherStatusDivorced">Divorced</label><br>
                            <input type="radio" id="motherStatusPassedAway" name="motherStatus" value="PASSED AWAY" <?php echo isset($parentDetails['mother_status']) && $parentDetails['mother_status'] === 'PASSED AWAY' ? 'checked' : ''; ?> >
                            <label for="motherStatusPassedAway">Passed Away</label><br>
                            <input type="radio" id="motherStatusNo" name="motherStatus" value="NO STATUS" <?php echo isset($parentDetails['mother_status']) && $parentDetails['mother_status'] === 'NO STATUS' ? 'checked' : ''; ?> >
                            <label for="motherStatusNo">No Status</label><br>
                        </div>

                        <div class="form-group">
                            <label for="motherPassedAwayDate" title="Optional">Date Mother Passed Away  <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="date" class="form-control" id="motherPassedAwayDate" name="motherPassedAwayDate" value="<?php echo isset($parentDetails['mother_passed_away_date']) ? $parentDetails['mother_passed_away_date'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motherAge" title="Optional">Age <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="number" class="form-control" id="motherAge" name="motherAge" placeholder="Enter your Mother's Age (e.g., 58)" value="<?php echo isset($parentDetails['mother_age']) ? $parentDetails['mother_age'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motherOccupation" title="Optional">Occupation <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="motherOccupation" name="motherOccupation" placeholder="Enter your Mother's Occupation (e.g., TEACHER)" value="<?php echo isset($parentDetails['mother_occupation']) ? htmlspecialchars($parentDetails['mother_occupation']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="motherEmployer" title="Optional">Employer <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="motherEmployer" name="motherEmployer" placeholder="Enter your Mother's Employer (e.g., KPM)" value="<?php echo isset($parentDetails['mother_employer']) ? htmlspecialchars($parentDetails['mother_employer']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motherPhoneNo"title="Optional">Phone No. Mother/Guardian <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="number" class="form-control" id="motherPhoneNo" name="motherPhoneNo" placeholder="Enter your Mother's Phone NUmber (e.g., 0186784567)" value="<?php echo isset($parentDetails['mother_phone_no']) ? $parentDetails['mother_phone_no'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motherHealthInfo" title="Optional">Mother/Guardian Health Information <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="motherHealthInfo" name="motherHealthInfo" placeholder="Enter your Mother's Health Information (e.g., GOOD HEALTH)" value="<?php echo isset($parentDetails['mother_health_info']) ? htmlspecialchars($parentDetails['mother_health_info']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="otherInfo" title="Optional">Other Information Regarding Parents/Guardians (if any) <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="text" class="form-control" id="otherInfo" name="otherInfo" placeholder="Enter your Parent's Other Information" value="<?php echo isset($parentDetails['other_info']) ? htmlspecialchars($parentDetails['other_info']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="fatherIncome" title="Optional">Father's Income <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="number" step="0.01" class="form-control" id="fatherIncome" name="fatherIncome" placeholder="Enter your Father's Income (e.g., 1500)" value="<?php echo isset($parentDetails['father_income']) ? $parentDetails['father_income'] : ''; ?>" onchange="calculateTotalIncome()">
                        </div>
                        <div class="form-group">
                            <label for="motherIncome" title="Optional">Mother's Income <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <input type="number" step="0.01" class="form-control" id="motherIncome" name="motherIncome"  placeholder="Enter your Mother's Income (e.g., 1500)" value="<?php echo isset($parentDetails['mother_income']) ? $parentDetails['mother_income'] : ''; ?>" onchange="calculateTotalIncome()">
                        </div>
                        <div class="form-group">
                            <label for="otherIncome">Other's Income</label>
                            <input type="number" step="0.01" class="form-control" id="otherIncome" name="otherIncome"  placeholder="Enter your Other's Income (e.g., 1500) If you are working" value="<?php echo isset($parentDetails['other_income']) ? $parentDetails['other_income'] : ''; ?>" onchange="calculateTotalIncome()">
                        </div>
                        <div class="form-group">
                            <label for="totalIncome">Total Income</label>
                            <input type="number" step="0.01" class="form-control" id="totalIncome" name="totalIncome" value="<?php echo isset($parentDetails['total_income']) ? $parentDetails['total_income'] : ''; ?>" disabled>
                        </div>


                        <button type="submit" id="saveButton2" class="btn btn-primary">Save</button><br>
                    </form>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="form-page" id="form-page-7"><br><br>
          
                <h2 style="text-align:center">C. DEPENDENCY OF THE HEAD OF THE FAMILY (PARENT / GUARDIAN)</h2>
                <form method="POST" action="depend_headfam.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="cityOfResidence" >City of Residence:</label>
                            <select id="cityOfResidence" name="cityOfResidence" class="form-control" required>
                                <option value="" selected disabled>Please Choose City of Residence</option>
                                <?php
                                $cities = array(
                                    "OTHERS",
                                    "JOHOR BAHRU",
                                    "ALOR SETAR",
                                    "KOTA BHARU",
                                    "SEREMBAN",
                                    "KUANTAN",
                                    "IPOH",
                                    "KANGAR",
                                    "GEORGETOWN",
                                    "BUTTERWORTH",
                                    "KOTA KINABALU",
                                    "KUCHING",
                                    "KUALA TERENGGANU",
                                    "BANDAR DI MELAKA",
                                    "WP KUALA LUMPUR",
                                    "WP PUTRAJAYA",
                                    "BANDAR DI SELANGOR",
                                    "PASIR GUDANG",
                                    "BUKIT MERTAJAM"
                                );
                                foreach ($cities as $city) {
                                    $selected = ($dependHeadFamData && $dependHeadFamData[0]['city_of_residence'] == $city) ? "selected" : "";
                                    echo "<option value=\"$city\" $selected>$city</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="headOfFamily" title="Optional">Head of the Family: <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if you have no head of family/relatives"></i></label>
                            <input type="number" id="headOfFamily" name="headOfFamily"  placeholder="Enter number of your Head of FAmily (e.g., 1)" class="form-control" value="<?php echo isset($dependHeadFamData[0]['head_of_family']) ? $dependHeadFamData[0]['head_of_family'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label title="Optional">Is the head of the family working? <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                            <div>
                                <label for="workingStatusYes">
                                <input type="radio" id="workingStatusYes" name="workingStatus" value="YES"
                                    <?php echo (!empty($dependHeadFamData) && isset($dependHeadFamData[0]['working_status']) && $dependHeadFamData[0]['working_status'] == 'YES') ? 'checked' : ''; ?>>Yes
                                </label>
                                <label for="workingStatusNo">
                                    <input type="radio" id="workingStatusNo" name="workingStatus" value="NO" <?php echo (!empty($dependHeadFamData) && isset($dependHeadFamData[0]['working_status']) && $dependHeadFamData[0]['working_status'] == 'NO') ? 'checked' : ''; ?>>No
                                </label>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="workingAdults" title="Optional">Working adults (18 years and over): <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if no working adult"></i></label>
                            <select id="workingAdults" name="workingAdults" class="form-control" required>
                                <option value="" selected disabled>Please Choose Number of Working adults (18 years and over)</option>
                                <?php
                                for ($i = 0; $i <= 10; $i++) {
                                    $selected = (isset($dependHeadFamData[0]['working_adults']) && $dependHeadFamData[0]['working_adults'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="unemployedAdults" title="Optional">Unemployed adults (18 years and over):<i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if no unemployed adults (18 years and over)"></i></label>
                            <select id="unemployedAdults" name="unemployedAdults" class="form-control" required>
                                <option value="" selected disabled>Please Choose Number of Unemployed adults (18 years and over)</option>
                                <?php
                                for ($i = 0; $i <= 10; $i++) {
                                    $selected = (isset($dependHeadFamData[0]['unemployed_adults']) && $dependHeadFamData[0]['unemployed_adults'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="iptStudents">IPT Students (including applicants):</label>
                            <select id="iptStudents" name="iptStudents" class="form-control" required>
                                <option value="" selected disabled>Please Choose Number of IPT Students (including applicants)</option>
                                <?php
                                for ($i = 0; $i <= 10; $i++) {
                                    $selected = (isset($dependHeadFamData[0]['ipt_students']) && $dependHeadFamData[0]['ipt_students'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="schoolChildren" title="Optional">School Children (6 - 17 years old):<i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if no School Children (6 - 17 years old)"></i></label>
                            <select id="schoolChildren" name="schoolChildren" class="form-control" required>
                                <option value="" selected disabled>Please Choose Number of School Children (6 - 17 years old)</option>
                                <?php
                                for ($i = 0; $i <= 10; $i++) {
                                    $selected = (isset($dependHeadFamData[0]['school_children']) && $dependHeadFamData[0]['school_children'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="children5YearsAndUnder" title="Optional">Children 5 years and under:<i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if no Children 5 years and under"></i></label>
                            <select id="children5YearsAndUnder" name="children5YearsAndUnder" class="form-control" required>
                                <option value="" selected disabled>Please Choose Number of Children 5 years and under</option>
                                <?php
                                for ($i = 0; $i <= 10; $i++) {
                                    $selected = (isset($dependHeadFamData[0]['children_5_years_and_under']) && $dependHeadFamData[0]['children_5_years_and_under'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" id="saveButton3" class="btn btn-primary">Save</button><br>
                </form>
                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="form-page" id="form-page-8"><br><br>
           
                <h2 style="text-align:center">D. SIBLINGS INFORMATION (Include You)</h2>
                <form method="POST" action="sibling_info.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="siblingName">Name</label>
                        <input type="text" class="form-control" id="siblingName" name="siblingName" placeholder="Enter your Sibling's Name" required>
                    </div>
                    <div class="form-group">
                        <label for="siblingRelationship">Relationship</label>
                        <select class="form-control" id="siblingRelationship" name="siblingRelationship" required>
                            <option value="" selected disabled>Select an option</option>
                            <option value="Older Brother">Older Brother</option>
                            <option value="Older Sister">Older Sister</option>
                            <option value="Younger Brother/Sister">Younger Brother/Sister</option>
                            <option value="Others">Others</option>
                            <option value="Unaccompanied">Unaccompanied</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="siblingAge">Age</label>
                        <input type="number" class="form-control" id="siblingAge" name="siblingAge"  placeholder="Enter your Sibling's Age (e.g., 27)" required>
                    </div>
                    <div class="form-group">
                        <label for="siblingWorkingStatus">Working Status</label>
                        <select class="form-control" id="siblingWorkingStatus" name="siblingWorkingStatus" required>
                            <option value="" selected disabled>Select an option</option>
                            <option value="Study">Study</option>
                            <option value="Working">Working</option>
                            <option value="Not Working">Not Working</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="siblingMaritalStatus">Marital Status</label><br>
                        <input type="radio" name="siblingMaritalStatus" id="siblingMaritalStatusYes" value="Yes" required>
                        <label for="siblingMaritalStatusYes">Yes</label>
                        <input type="radio" name="siblingMaritalStatus" id="siblingMaritalStatusNo" value="No" required>
                        <label for="siblingMaritalStatusNo">No</label>
                    </div>
                    <div class="form-group">
                        <label for="siblingPosition" title="Optional">Position (If Applicable) <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="text" class="form-control" id="siblingPosition" name="siblingPosition" placeholder="Enter your Sibling's Working Position (e.g., TECHNICIAN)">
                    </div>
                    <div class="form-group">
                        <label for="siblingEmployer" title="Optional">Employer/Department/Institution <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="text" class="form-control" id="siblingEmployer" name="siblingEmployer" placeholder="Enter your Sibling's EMployer (e.g., COCHLEAR SDN BHD)">
                    </div>
                    <div class="form-group">
                        <label for="siblingAdditionalInfo" title="Optional">Please fill in other information about siblings (if any): <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <textarea id="siblingAdditionalInfo" name="siblingAdditionalInfo" placeholder="Enter your Sibling's Additional Information" rows="4" cols="50"></textarea>
                    </div>
                    <input type="hidden" id="application_id" name="application_id" value="<?php echo $application_id; ?>">
                    <button type="submit" id="saveButton4" class="btn btn-primary">Add and Save</button><br>
                    </form><br>

                <h3 style="text-align:center">Your Siblings' Information</h3>
                <table class="table">
                    <thead>
                    <tr style="border-bottom: 1px solid silver; border-top: 1px solid silver;text-align:center">
                            <th>Name</th>
                            <th>Relationship</th>
                            <th>Age</th>
                            <th>Working Status</th>
                            <th>Marital Status</th>
                            <th>Position</th>
                            <th>Employer</th>
                            <th>Additional Info</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($sibling_details_result) > 0) {
                            while ($row = mysqli_fetch_assoc($sibling_details_result)) {
                                echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>{$row['relationship']}</td>
                                        <td>{$row['age']}</td>
                                        <td>{$row['working_status']}</td>
                                        <td>{$row['marital_status']}</td>
                                        <td>{$row['position']}</td>
                                        <td>{$row['employer']}</td>
                                        <td>{$row['add_info']}</td>
                                        <td>
                                            <button class='btn btn-primary edit-btn' data-id='{$row['id']}'>Edit</button><br><br>
                                            <form method='POST' action='sibling_info.php' style='display:inline;'>
                                                <input type='hidden' name='sibling_id' value='{$row['id']}'>
                                                <button type='submit' class='btn btn-danger' name='delete'>Delete</button>
                                            </form>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No siblings found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>

                <!-- Modal for editing sibling information -->
                <div class="modal fade" id="editSiblingModal" tabindex="-1" role="dialog" aria-labelledby="editSiblingModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSiblingModalLabel">Edit Sibling Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="sibling_info.php">
                                    <input type="hidden" id="editSiblingId" name="sibling_id">
                                    <div class="form-group">
                                        <label for="editSiblingName">Name</label>
                                        <input type="text" class="form-control" id="editSiblingName" name="siblingName" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingRelationship">Relationship</label>
                                        <select class="form-control" id="editSiblingRelationship" name="siblingRelationship" required>
                                            <option value="" selected disabled>Select an option</option>
                                            <option value="Older Brother">Older Brother</option>
                                            <option value="Older Sister">Older Sister</option>
                                            <option value="Younger Brother/Sister">Younger Brother/Sister</option>
                                            <option value="Others">Others</option>
                                            <option value="Unaccompanied">Unaccompanied</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingAge">Age</label>
                                        <input type="number" class="form-control" id="editSiblingAge" name="siblingAge" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingWorkingStatus">Working Status</label>
                                        <select class="form-control" id="editSiblingWorkingStatus" name="siblingWorkingStatus" required>
                                            <option value="" selected disabled>Select an option</option>
                                            <option value="Study">Study</option>
                                            <option value="Working">Working</option>
                                            <option value="Not Working">Not Working</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingMaritalStatus">Marital Status</label><br>
                                        <input type="radio" name="siblingMaritalStatus" id="editSiblingMaritalStatusYes" value="Yes" required>
                                        <label for="editSiblingMaritalStatusYes">Yes</label>
                                        <input type="radio" name="siblingMaritalStatus" id="editSiblingMaritalStatusNo" value="No" required>
                                        <label for="editSiblingMaritalStatusNo">No</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingPosition">Position (If Applicable)</label>
                                        <input type="text" class="form-control" id="editSiblingPosition" name="siblingPosition">
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingEmployer">Employer/Department/Institution</label>
                                        <input type="text" class="form-control" id="editSiblingEmployer" name="siblingEmployer">
                                    </div>
                                    <div class="form-group">
                                        <label for="editSiblingAdditionalInfo">Please fill in other information about siblings (if any):</label>
                                        <textarea id="editSiblingAdditionalInfo" name="siblingAdditionalInfo" rows="4" cols="50"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                  
            <div class="form-page" id="form-page-9">
                <h2 style="text-align:center">E. FINANCIAL INFORMATION</h2>
                <form method="POST" action="financial_info.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="sponsorshipLoan">Do you get sponsorship/loan?</label>
                        <select id="sponsorshipLoan" class="form-control" name="sponsorshipLoan" onchange="handleSponsorshipLoan(this.value)" required>
                            <option value="" selected disabled>Select an option</option>
                            <option value="Yes" <?= $sponsorshipLoan == 'Yes' ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $sponsorshipLoan == 'No' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason" title="Optional">If No, please state the reason: <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <textarea id="reason" class="form-control" name="reason" placeholder="Enter your Reason (e.g., Non-Compliance with Requirements)" <?= $sponsorshipLoan == 'Yes' ? 'disabled' : '' ?>><?= htmlspecialchars($reason ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="source" title="Optional">If any, state from where: <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="text" id="source" class="form-control" name="source" placeholder="Enter your Source of Sponsorship/Loan (e.g., MARA)" value="<?= htmlspecialchars($source ?? '') ?>" <?= $sponsorshipLoan == 'Yes' ? 'disabled' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label for="sponsorshipAmount" title="Optional">Total sponsorship/loan per/sem (RM): <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="number" step="0.01" id="sponsorshipAmount" class="form-control" name="sponsorshipAmount" placeholder="Enter your Total Sponsorship/loan per/sem (e.g., 1500)" value="<?= htmlspecialchars($sponsorshipAmount ?? '') ?>" <?= $sponsorshipLoan == 'Yes' ? 'disabled' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label for="sponsorshipBalance" title="Optional">Sponsorship/Loan Latest Balance (which has not yet been used): <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                        <input type="number" step="0.01" id="sponsorshipBalance" class="form-control" name="sponsorshipBalance" placeholder="Enter your Sponsorship/Loan Latest Balance (e.g., 4500)" value="<?= htmlspecialchars($sponsorshipBalance ?? '') ?>" <?= $sponsorshipLoan == 'Yes' ? 'disabled' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label for="tuitionFee">Tuition fee per/sem:</label>
                        <input type="number" step="0.01" id="tuitionFee" class="form-control" name="tuitionFee" placeholder="Enter your Tuition Fee per/sem (e.g., 590)" value="<?= htmlspecialchars($tuitionFee ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="feeStatus">Fee Payment Status:</label>
                        <select id="feeStatus" class="form-control" name="feeStatus" required>
                            <option value="" selected disabled>Select an option</option>
                            <option value="Paid" <?= $feeStatus == 'Paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="Not Paid" <?= $feeStatus == 'Not Paid' ? 'selected' : '' ?>>Not Paid</option>
                        </select>
                    </div>
                    <button type="submit" id="saveButton5" class="btn btn-primary">Save</button><br>
                </form>
                <div class="button-container mt-4">
                    <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                    <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>


            <div class="form-page" id="form-page-10"><br><br>
           
            <h2 style="text-align:center">F. ACCOMMODATION INFORMATION (COLLEGE / RENT HOUSE / FAMILY HOUSE)</h2>
            <form method="POST" action="accommodation_info.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="accommodationType">Accommodation:</label>
                    <select class="form-control" id="accommodationType" name="accommodationType" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="college" <?php if ($accommodationType == 'college') echo 'selected'; ?>>College</option>
                        <option value="rented house" <?php if ($accommodationType == 'rented house') echo 'selected'; ?>>Rented House</option>
                        <option value="family house" <?php if ($accommodationType == 'family house') echo 'selected'; ?>>Family House</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="accommodationFee">Accommodation Fee:</label>
                    <select class="form-control" id="accommodationFee" name="accommodationFee" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="free" <?php if ($accommodationFee == 'free') echo 'selected'; ?>>Free</option>
                        <option value="paid" <?php if ($accommodationFee == 'paid') echo 'selected'; ?>>Paid</option>
                        <option value="unpaid" <?php if ($accommodationFee == 'unpaid') echo 'selected'; ?>>Unpaid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="paymentRate" title="Optional">Payment rate: <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="Fill in 0 if no rate"></i></label>
                    <input type="number" class="form-control" step="0.01" id="paymentRate" name="paymentRate" placeholder="Enter your Payment Rate of Accomodation (e.g., 300)" value="<?php echo isset($paymentRate) ? $paymentRate : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="vehicle">Do you have (carry) a vehicle?</label>
                    <select class="form-control" id="vehicle" name="vehicle" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="yes" <?php if ($vehicle == 'yes') echo 'selected'; ?>>Yes</option>
                        <option value="no" <?php if ($vehicle == 'no') echo 'selected'; ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vehicleType" title="Optional">Type of vehicle: <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                    <select class="form-control" id="vehicleType" name="vehicleType" <?php if ($vehicle != 'yes') echo 'disabled'; ?>>
                        <option value="" selected disabled>Select an option</option>
                        <option value="bicycle" <?php if ($vehicleType == 'bicycle') echo 'selected'; ?>>Bicycle</option>
                        <option value="car" <?php if ($vehicleType == 'car') echo 'selected'; ?>>Car</option>
                        <option value="lorry" <?php if ($vehicleType == 'lorry') echo 'selected'; ?>>Lorry</option>
                        <option value="motorcycles" <?php if ($vehicleType == 'motorcycles') echo 'selected'; ?>>Motorcycles</option>
                        <option value="motorcycles exceed 125cc" <?php if ($vehicleType == 'motorcycles exceed 125cc') echo 'selected'; ?>>Motorcycles Exceed 125cc</option>
                        <option value="van" <?php if ($vehicleType == 'van') echo 'selected'; ?>>Van</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vehicleModel" title="Optional">Vehicle Model <i class="fas fa-question-circle"data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                    <input type="text" class="form-control" id="vehicleModel" name="vehicleModel" placeholder="Enter your Vehicle Model (e.g., Perodua MyVi)" <?php if ($vehicle != 'yes') echo 'disabled'; ?> value="<?php echo isset($vehicleModel) ? $vehicleModel : ''; ?>">
                </div>
                <button type="submit" id="saveButton6" class="btn btn-primary">Save</button><br>
                </form>
            <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

            
        <div class="form-page" id="form-page-11">
            <h2 style="text-align:center">G. DETAILS OF AID AND PURPOSE</h2>
            <form method="POST" action="detailaid_info.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="zakatReceived">Have you ever received Zakat assistance from UiTM?</label>
                    <select class="form-control" id="zakatReceived" name="zakatReceived" onchange="toggleFields()" required>
                        <option value="" <?php if ($zakatReceived == "") echo "selected"; ?> selected disabled>Select an option</option>
                        <option value="yes" <?php if ($zakatReceived == "yes") echo "selected"; ?>>Yes</option>
                        <option value="no" <?php if ($zakatReceived == "no") echo "selected"; ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="zakatYear" title="Optional">(If yes) Year receiving zakat: <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                    <input type="number" class="form-control" id="zakatYear" name="zakatYear" placeholder="Enter your Yaer Receiving Zakat (e.g., 2023)" value="<?php echo htmlspecialchars($zakatYear); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="zakatSemester" title="Optional">(If yes) Zakat semester (Part): <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                    <input type="number" class="form-control" id="zakatSemester" name="zakatSemester" placeholder="Enter your Semester of Receiving Zakat (e.g., 4)" value="<?php echo htmlspecialchars($zakatSemester); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="zakatAmount" title="Optional">(If yes) Amount of zakat per semester: <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="This is optional"></i></label>
                    <input type="number" step="0.01" class="form-control" id="zakatAmount" name="zakatAmount" placeholder="Enter your Zakat Amount Per Semester (e.g., 500)" value="<?php echo htmlspecialchars($zakatAmount); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="zakatPurpose">Please state the purpose of applying for zakat assistance:</label>
                    <textarea class="form-control" id="zakatPurpose" name="zakatPurpose" placeholder="Enter your purpose of applying for zakat assistance" required><?php echo htmlspecialchars($zakatPurpose); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="zakatDocuments" title="Optional">Please attach all the required documents as follows in one (1) PDF or ZIP format Only: <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="Please upload a single PDF or ZIP file, up to 30 MB in size."></i></label>
                    <input type="file" class="form-control-file" id="zakatDocuments" name="zakatDocuments" accept="application/pdf, application/zip" required>
                    <?php if (!empty($zakatDocuments)) echo "<p>Uploaded file: <a href='$zakatDocuments'>$zakatDocuments</a></p>"; ?>
                </div>
                <button type="submit" id="saveButton7" class="btn btn-primary">Save</button><br>
            </form>
            <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
                <button onclick="nextPage()" class="btn custom-next-btn"> Next <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>


        <div class="form-page" id="form-page-12">
            <br><br>
            <h2 style="text-align:center">H. Applicant's Statement</h2>
            <form method="POST" action="applicant_statement.php" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="applicantStatement" id="applicantStatement1" value="1" <?php echo ($applicantStatement == 1) ? 'checked' : ''; ?> required>
                        <label class="form-check-label" for="applicantStatement1">
                            I, AS NAMED IN THIS APPLICATION, ACKNOWLEDGE BY NAME ALLAH (FOR ISLAMIC STUDENTS), THAT ALL INFORMATION WHAT IS PROVIDED IN THIS APPLICATION IS TRUE. I REALIZE IT WOULD BE A FAULT AND A SIN IF I GAVE INACCURATE AND FALSE INFORMATION.
                        </label>
                    </div>
                </div>
                <button type="submit" id="saveButton8" class="btn btn-primary">Submit the Application</button><br>
            </form>
            <div class="button-container mt-4">
                <button onclick="prevPage()" class="btn custom-next-btn"> <i class="fas fa-arrow-left"></i> Previous</button>&nbsp;
            </div>
        </div>

    </div>
</div><br>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var currentPage = getCurrentPage();
    document.querySelectorAll('.form-page').forEach(function (page) {
        page.style.display = 'none';
    });
    var currentFormPage = document.getElementById('form-page-' + currentPage);
    if (currentFormPage) {
        currentFormPage.style.display = 'block';
    }

});

function getCurrentPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('page');
    if (pageParam) {
        const pageParts = pageParam.split('-');
        return parseInt(pageParts[2]);
    }
    return 1; // Default to the first page if no parameter is present
}

function showPage(pageNumber) {
    document.querySelectorAll('.form-page').forEach(page => {
        page.style.display = 'none';
    });
    document.getElementById('form-page-' + pageNumber).style.display = 'block';
}

function nextPage() {
    const totalPages = 12; // Adjust this value based on the total number of pages
    if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
        updateURL(currentPage);
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
        updateURL(currentPage);
    }
}

function updateURL(pageNumber) {
    const url = new URL(window.location);
    url.searchParams.set('page', 'form-page-' + pageNumber);
    window.history.pushState({}, '', url);
}

let currentPage = getCurrentPage(); // Initialize currentPage
///
    
    // function saveFormData(formNumber) {
    //     // Kod untuk menyimpan data ke pangkalan data boleh dimasukkan di sini
    //     alert('Borang ' + formNumber + ' telah disimpan!');
    // }

    function updateFileName(input) {
        var fileNameSpanId = input.id + "Name";
        var fileNameSpan = document.getElementById(fileNameSpanId);
        if (input.files.length > 0) {
            fileNameSpan.innerText = input.files[0].name;
        } else {
            fileNameSpan.innerText = "";
        }
    }

    function cancelUpload(inputId) {
        const fileInput = document.getElementById(inputId);
        const fileNameSpan = document.getElementById(inputId + 'Name');
        fileInput.value = '';
        fileNameSpan.textContent = '';
    }

    function calculateTotalIncome() {
        var fatherIncome = parseFloat(document.getElementById('fatherIncome').value) || 0;
        var motherIncome = parseFloat(document.getElementById('motherIncome').value) || 0;
        var otherIncome = parseFloat(document.getElementById('otherIncome').value) || 0;

        var totalIncome = fatherIncome + motherIncome + otherIncome;

        document.getElementById('totalIncome').value = totalIncome.toFixed(2);
    }

    /////
    $(document).ready(function() {
    // Edit button click handler
    $('.edit-btn').on('click', function() {
        var siblingId = $(this).data('id');
        
        // Fetch sibling data via AJAX
        $.ajax({
            url: 'sibling_info.php',
            type: 'POST',
            data: { edit: true, sibling_id: siblingId },
            success: function(response) {
                try {
                    var sibling = JSON.parse(response);
                    
                    if (sibling.error) {
                        alert(sibling.error);
                    } else {
                        // Populate the modal fields with the fetched data
                        $('#editSiblingName').val(sibling.name);
                        $('#editSiblingRelationship').val(sibling.relationship);
                        $('#editSiblingAge').val(sibling.age);
                        $('#editSiblingWorkingStatus').val(sibling.working_status);
                        $('input[name="siblingMaritalStatus"][value="' + sibling.marital_status + '"]').prop('checked', true);
                        $('#editSiblingPosition').val(sibling.position);
                        $('#editSiblingEmployer').val(sibling.employer);
                        $('#editSiblingAdditionalInfo').val(sibling.add_info);
                        $('#editSiblingId').val(sibling.id);
                        
                        // Show the modal
                        $('#editSiblingModal').modal('show');
                    }
                } catch (e) {
                    alert('Failed to parse response: ' + response);
                }
            }
        });
    });
});
////
// Enable vehicleType and vehicleModel fields based on vehicle selection
document.addEventListener("DOMContentLoaded", function() {
    const vehicleSelect = document.getElementById("vehicle");
    const vehicleTypeSelect = document.getElementById("vehicleType");
    const vehicleModelInput = document.getElementById("vehicleModel");

    vehicleSelect.addEventListener("change", function() {
        if (vehicleSelect.value === "yes") {
            vehicleTypeSelect.disabled = false;
            vehicleModelInput.disabled = false;
        } else {
            vehicleTypeSelect.disabled = true;
            vehicleModelInput.disabled = true;
            vehicleTypeSelect.value = "";
            vehicleModelInput.value = "";
        }
    });

    // Trigger change event to set initial state
    vehicleSelect.dispatchEvent(new Event("change"));
});

////
function toggleFields() {
        var zakatReceived = document.getElementById("zakatReceived").value;
        var yearField = document.getElementById("zakatYear");
        var semesterField = document.getElementById("zakatSemester");
        var amountField = document.getElementById("zakatAmount");
        var purposeField = document.getElementById("zakatPurpose");

        if (zakatReceived === "no") {
            yearField.disabled = true;
            semesterField.disabled = true;
            amountField.disabled = true;
        } else {
            yearField.disabled = false;
            semesterField.disabled = false;
            amountField.disabled = false;
        }
    }


    ///
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});

$(document).ready(function() {
            window.setTimeout(function() {
                $(".alert").alert('close');
            }, 3000);
        });

///
document.getElementById('saveButton8').addEventListener('click', function(event) {
    if (confirm("Are you sure? You will send the application once you submit.")) {
        alert("You have submitted the application. You can check the application status through this website or we will email you. Thank you.");
        document.querySelector('form').submit(); // Submit the form
        window.location.href = 'dashboard.php'; // Redirect to dashboard
    }
});
//
function handleSponsorshipLoan(value) {
    var reasonField = document.getElementById('reason');
    var sourceField = document.getElementById('source');
    var sponsorshipAmountField = document.getElementById('sponsorshipAmount');
    var sponsorshipBalanceField = document.getElementById('sponsorshipBalance');

    if (value === 'Yes') {
        reasonField.setAttribute('disabled', 'disabled');
        sourceField.removeAttribute('disabled');
        sponsorshipAmountField.removeAttribute('disabled');
        sponsorshipBalanceField.removeAttribute('disabled');
    } else if (value === 'No') {
        reasonField.removeAttribute('disabled');
        sourceField.setAttribute('disabled', 'disabled');
        sponsorshipAmountField.setAttribute('disabled', 'disabled');
        sponsorshipBalanceField.setAttribute('disabled', 'disabled');
    } 
}
//
function toggleFields() {
    var zakatReceived = document.getElementById('zakatReceived').value;
    var yearField = document.getElementById('zakatYear');
    var semesterField = document.getElementById('zakatSemester');
    var amountField = document.getElementById('zakatAmount');

    if (zakatReceived === 'yes') {
        yearField.removeAttribute('disabled');
        semesterField.removeAttribute('disabled');
        amountField.removeAttribute('disabled');
    } else if (zakatReceived === 'no') {
        yearField.setAttribute('disabled', 'disabled');
        semesterField.setAttribute('disabled', 'disabled');
        amountField.setAttribute('disabled', 'disabled');
    }
}


function validateForm() {
        var matricNo = document.getElementById('matricNo').value.trim();
        var icNo = document.getElementById('icNo').value.trim();
        var email = document.getElementById('email').value.trim();
        var semester = document.getElementById('semester').value.trim();
        var currentCGPA = document.getElementById('currentCGPA').value.trim();
        var mobileNumber = document.getElementById('mobileNumber').value.trim();
        var bankAccountNo = document.getElementById('bankAccountNo').value.trim();

        // Regular expressions for validation
        var matricNoPattern = /^\d{10}$/;
        var icNoPattern = /^\d{12}$/;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var semesterPattern = /^\d+$/;
        var currentCGPAPattern = /^\d+(\.\d{1,2})?$/; // Allows 1 or 2 decimal places
        var phonePattern = /^\d+$/;
        var bankAccountPattern = /^\d+$/;

        // Validation checks
        if (!matricNoPattern.test(matricNo)) {
            alert('Matric No. must be 10 numeric characters.');
            return false;
        }

        if (!icNoPattern.test(icNo)) {
            alert('IC No. must be 12 numeric characters.');
            return false;
        }

        if (!emailPattern.test(email)) {
            alert('Invalid email format.');
            return false;
        }

        if (!semesterPattern.test(semester)) {
            alert('Semester must be a number.');
            return false;
        }

        if (!currentCGPAPattern.test(currentCGPA)) {
            alert('Current CGPA format should be like "4.00".');
            return false;
        }

        if (!phonePattern.test(mobileNumber)) {
            alert('Applicant Phone.No must be numeric.');
            return false;
        }

        if (!bankAccountPattern.test(bankAccountNo)) {
            alert('Bank Account.No must be numeric.');
            return false;
        }

        // All validations passed
        return true;
    }

    // Optional: Function to reset form validation messages
    function resetValidationMessages() {
        document.getElementById('matricNo').setCustomValidity('');
        document.getElementById('icNo').setCustomValidity('');
        document.getElementById('email').setCustomValidity('');
        document.getElementById('semester').setCustomValidity('');
        document.getElementById('currentCGPA').setCustomValidity('');
        document.getElementById('mobileNumber').setCustomValidity('');
        document.getElementById('bankAccountNo').setCustomValidity('');
    }
//
<?php if (isset($_GET['redirect']) && $_GET['redirect'] == 1): ?>
    alert("<?php echo isset($_GET['success']) ? $_GET['success'] : (isset($_GET['error']) ? $_GET['error'] : ''); ?>");
    window.location.href = "dashboard.php";
<?php endif; ?>
</script>




</body>
</html>
