<?php
session_start();
include 'dbconn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM applicant_statement WHERE application_id = (SELECT id FROM applicant_information WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$alreadySubmitted = $result->num_rows > 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Had Kifayah Calculator</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
<link rel="icon" type="image/png" href="images/favicon.png">

<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
    }
    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .card-header {
        background-color: #b6c784 !important;
        color: black;
        border-radius: 10px 10px 0 0;
    }
    .btn-custom {
        background-color: #b6c784 !important;
        color: white;
    }
    .btn-custom:disabled {
        background-color: grey;
    }
    .btn-custom-no-color {
        background-color: transparent;
        color: black;
        border: 1px solid black;
    }
    .btn-custom-no-color:hover {
        background-color: #b6c784;
        color: white;
    }
    .slide {
        display: none;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var currentSlide = 1;

    function showSlide(slideIndex) {
        const slides = document.querySelectorAll('.slide');
        if (slideIndex < 1) {
            currentSlide = 1;
        } else if (slideIndex > slides.length) {
            currentSlide = slides.length;
        } else {
            currentSlide = slideIndex;
        }
        slides.forEach(slide => slide.style.display = 'none');
        document.getElementById('slide' + currentSlide).style.display = 'block';
    }

    var alreadySubmitted = <?php echo json_encode($alreadySubmitted); ?>;

    document.getElementById("continueButton").addEventListener("click", function(event) {
        if (alreadySubmitted) {
            event.preventDefault();
            alert("You have already submitted an application. Please wait for the response via email. Thank you!");
            this.disabled = true;
        } else {
            window.location.href = 'zakatappstuuu.php';
        }
    });

    document.getElementById("calcForm").addEventListener("submit", function(event) {
        event.preventDefault();

        var totalSponsorship = parseFloat(document.getElementById("totalSponsorship").value);
        var otherIncome = parseFloat(document.getElementById("otherIncome").value);

        var totalIncome = totalSponsorship + otherIncome;
        var hadKifayahValue = 3403.00;
        var balance = totalIncome - hadKifayahValue;

        var status = '';
        if (balance <= 1701) {
            status = 'Fakir';
        } else if (balance >= 1702 && balance < 3404) {
            status = 'Miskin';
        } else {
            status = 'Not Eligible';
        }

        document.getElementById("totalIncome").value = totalIncome.toFixed(2);
        document.getElementById("balance").value = balance.toFixed(2);
        document.getElementById("status").value = status;

        if (status === 'Fakir' || status === 'Miskin') {
            document.getElementById("continueButton").disabled = false;
        } else {
            document.getElementById("continueButton").disabled = true;
            alert("You are not eligible for zakat application");
        }
    });

    document.getElementById("prevButton").addEventListener('click', function() {
        showSlide(currentSlide - 1);
    });

    document.getElementById("nextButton").addEventListener('click', function() {
        showSlide(currentSlide + 1);
    });

    showSlide(1);
});

function resetForm() {
    document.getElementById("totalSponsorship").value = 0;
    document.getElementById("otherIncome").value = 0;
    document.getElementById("totalIncome").value = 0;
    document.getElementById("balance").value = 0;
    document.getElementById("status").value = '';
    document.getElementById("continueButton").disabled = true; 
}
</script>
</head>
<body>
<?php
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

<div class="container mt-5">
    <div class="alert alert-info" role="alert">
        Please read all the information about the Had Kifayah Calculator carefully. You need to calculate your eligibility to apply for zakat before proceeding. Navigate through the slides using the Next and Previous buttons at the top of the slide.
    </div>
    <div class="container mt-4">
    <div id="buttons" class="d-flex justify-content-between">
        <button class="btn btn-light" id="prevButton"><i class="fas fa-chevron-left"></i></button>
        <button class="btn btn-light" id="nextButton"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div id="slide1" class="slide card" style="display: block;">
        <div class="card-header">
            <h2 class="text-center mb-0">Understanding Had Kifayah</h2>
        </div>
        <div class="card-body">
            <p style="font-size: 18px; text-align: justify; line-height: 1.5;">
                Students of UiTM Perlis Branch NEED to check the Had Kifayah before filling out the zakat (personal welfare) application form. Had Kifayah, from a linguistic perspective, refers to the level of necessity (sufficiency). From a Shariah perspective, it signifies the minimum level of basic needs in an individual's life. In essence, Had Kifayah refers to a minimum basic necessity level set based on current cost of living. This threshold is also used to determine the amount needed to meet basic expenditure sufficiency.
            </p>
            <h2 style="color: black; text-align: center;">Two Required Pieces of Information:</h2>
            <ol style="font-size: 18px; text-align: justify; line-height: 1.5; padding-left: 20px;">
                <li><strong>Total sponsorship for the current semester:</strong><br>
                    Example: "For this semester, I received RM1500 from PTPTN. The value 1500 should be entered in the 'Total Sponsorship/Semester' field."</li>
                <li><strong>Other income:</strong><br>
                    Example: "I also receive financial assistance from family members amounting to RM500 per month. The value 500 should be entered in the 'Other Income' field."</li>
            </ol>
            <p style="font-size: 18px; text-align: justify; line-height: 1.5;">
                If the status of the Had Kifayah Calculator is MISKIN or FAKIR, students/staff are ELIGIBLE to apply for zakat (personal welfare) assistance. If NOT ELIGIBLE, students/staff can apply for assistance from the UiTM Perlis Branch SEDEKAH UNIT.
            </p>
        </div>
    </div>

    <div id="slide2" class="slide card">
        <div class="card-header">
            <h2 class="text-center mb-0">Cost of Living at UiTM Perlis</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <caption>Cost of Living at UiTM Perlis</caption>
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Description (English)</th>
                        <th scope="col">Cost (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Item A: Cost of Living</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Food and Beverage Expenses</td>
                        <td>1,200.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Accommodation</td>
                        <td>310.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Clothing</td>
                        <td>60.00</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Medical</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Communication</td>
                        <td>120.00</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Transportation</td>
                        <td>200.00</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Personal Care Items</td>
                        <td>100.00</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Cost of Returning Hometown</td>
                        <td>200.00</td>
                    </tr>
                    <tr>
                        <th scope="row">Total Cost of Living</th>
                        <td></td>
                        <td>RM2,190.00</td>
                    </tr>
                    <tr>
                        <th scope="row">Item B: Academic Cost</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Tuition Fees</td>
                        <td>530.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Association Fees</td>
                        <td>4.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Reference Books</td>
                        <td>120.00</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Printing</td>
                        <td>80.00</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Utility Tools</td>
                        <td>27.00</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Coursework Equipment</td>
                        <td>35.00</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Cost of Coursework (On Campus)</td>
                        <td>180.00</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Cost of Coursework (Off Campus)</td>
                        <td>210.00</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Formal Clothing/Uniform</td>
                        <td>27.00</td>
                    </tr>
                    <tr>
                        <th scope="row">Total Academic Cost</th>
                        <td></td>
                        <td>RM1,213.00</td>
                    </tr>
                    <tr>
                        <th scope="row">Total Overall Expenses (Item A + Item B)</th>
                        <td></td>
                        <td>RM3,403.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="slide3" class="slide card">
        <div class="card-header">
            <h2 class="text-center mb-0">Had Kifayah Calculator</h2>
        </div>
        <div class="card-body">
            <form method="post" id="calcForm">
                <div class="form-group">
                    <label for="totalSponsorship">Total Sponsorship/Semester (RM): </label>
                    <input type="number" class="form-control" name="totalSponsorship" step="0.01" id="totalSponsorship" value="0">
                </div>

                <div class="form-group">
                    <label for="otherIncome">Other Income (RM): </label>
                    <input type="number" class="form-control" name="otherIncome" step="0.01" id="otherIncome" value="0">
                </div>

                <div class="form-group">
                    <label for="totalIncome">Total Income (RM):</label>
                    <input type="text" class="form-control" id="totalIncome" value="0" readonly>
                </div>

                <div class="form-group">
                    <label for="hkifayah">Had Kifayah (RM):</label>
                    <input type="text" class="form-control" id="hkifayah" value="3403.00" readonly>
                </div>

                <div class="form-group">
                    <label for="balance">Balance (RM): </label>
                    <input type="text" class="form-control" id="balance" value="0" readonly>
                </div>

                <div class="form-group">
                    <label for="status">Status: </label>
                    <input type="text" class="form-control" id="status" value="" readonly>
                </div>

                <button type="submit" class="btn btn-custom">Calculate</button>
                <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
                <button type="button" class="btn btn-custom-no-color" id="continueButton" disabled>Continue to the application</button>
            </form>
        </div>
    </div>
</div>


</div>
</body>
</html>
