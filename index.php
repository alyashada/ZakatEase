<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'dbconn.php';

$query = "SELECT start_date, end_date FROM zakat_application_dates LIMIT 1";
$result = $conn->query($query);
$date_range = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <style>

        body {
        font-family: 'Roboto', sans-serif;
        }
        .image-box {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            overflow: hidden;
            position: relative;
        }

        .slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            transition: left 0.5s ease; 
        }

        .active {
            left: 0; 
        }

        .slide-image:not(.active) {
            left: -100%; 
        }

        .overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 1;
            transition: opacity 0.5s; 
        }

        .overlay h2 {
            font-size: 2.5rem;
            margin: 0;
            color: black; 
            transition: opacity 0.5s; 
        }

        .overlay button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.5s; 
        }

        .overlay button:hover {
            background-color: #0056b3;
        }

        .w3-left, .w3-right {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            z-index: 3; /* Ensure arrows are above overlay */
        }

        .w3-left {
            left: 0;
            border-radius: 3px 0 0 3px;
        }

        .w3-right {
            right: 0;
            border-radius: 0 3px 3px 0;
        }

        .w3-left:hover, .w3-right:hover {
            background-color: rgba(0,0,0,0.8);
        }

        .w3-badge {
            height: 13px;
            width: 13px;
            padding: 0;
            cursor: pointer;
        }

        .ways {
            background-color: #f8f9fa;
            padding: 40px 0;
        }

        #ways .row {
            display: flex;
            justify-content: center;
        }

        #ways .col-lg-4 {
            flex: 1;
        }

        .box {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            position: relative;
            transition: box-shadow 0.3s ease;
            margin: 0 10px;
        }

        .box img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        .box:hover {
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.5);
        }

        .section-title {
            text-align: center;
        }
        .faq {
            padding: 40px 0;
        }

        .faq h2 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 30px;
        }

        .custom-accordion-button {
            background-color: #FFFFFF !important;
            color: #000 !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 20px !important;
            margin-bottom: 10px !important;
            width: 100% !important;
            text-align: left !important;
            transition: background-color 0.3s ease, box-shadow 0.3s ease !important;
            cursor: pointer !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            outline: none !important;
            position: relative !important;
        }

        .custom-accordion-button:hover {
            background-color: #b6c784 !important;
        }

        .custom-accordion-button::after {
            content: '+';
            font-size: 1.5rem;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }

        .custom-accordion-button.active::after {
            content: '-';
            transform: translateY(-50%);
        }

        .custom-accordion-item {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 0 20px 10px 20px;
            margin-bottom: 10px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .custom-accordion-item.active {
            max-height: 500px; /* Set a max height large enough to accommodate content */
        }

        .accordion-content {
            margin-top: 10px;
        }

        .custom-accordion-button:focus {
            outline: none;
            box-shadow: none;
        }

        .about-section {
            background-color: #f8f9fa;
            padding: 40px 0;
        }

        .about-section .container {
            text-align: center;
        }

        .about-section h2 {
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .about-section p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .about-image {
            max-width: 60%; 
            height: auto; 
            display: block; 
            margin: 0 auto; 
        }
        .about-content-box {
            background-color: white; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            padding: 20px; 
            margin-top: 20px; 
        }
        .get-in-touch {
            padding: 50px 0;
            background-color: #f9f9f9;
        }
        .contact-info {
            text-align: center;
        }
        .contact-info h2 {
            margin-bottom: 20px;
        }
        .contact-info p {
            margin: 0;
        }
        .map-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%; 
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .marquee {
            width: 100%;
            overflow: hidden;
            background-color: #000;
            color: #fff;
            padding: 10px 0;
        }

        .marquee div {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 20s linear infinite;
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
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
    } else {
        include 'headerdef.php';
    }
?>

<div class="marquee">
    <div>
        Application open: <?php echo htmlspecialchars($date_range['start_date']); ?> | 
        Application close: <?php echo htmlspecialchars($date_range['end_date']); ?> | 
        Important: Zakat Application Deadline is <?php echo htmlspecialchars($date_range['end_date']); ?>. Submit Your Application Today!
    </div>
</div>

<div class="container-fluid p-0 image-box" data-aos="fade-in">
    <img class="mySlides slide-image" src="images/banner1212.png" alt="Banner 3">
    <img class="mySlides slide-image" src="images/banner1 (2).png" alt="Banner 1">
    <img class="mySlides slide-image" src="images/banner22.png" alt="Banner 2">
    <div class="w3-left w3-hover-text-khaki" onclick="plusDivs(-1)">&#10094;</div>
    <div class="w3-right w3-hover-text-khaki" onclick="plusDivs(1)">&#10095;</div>
    <div class="w3-center w3-container w3-section w3-large w3-text-white w3-display-bottommiddle" style="width:100%">
        <span class="w3-badge demo w3-border w3-transparent w3-hover-white" onclick="currentDiv(1)"></span>
        <span class="w3-badge demo w3-border w3-transparent w3-hover-white" onclick="currentDiv(2)"></span>
        <span class="w3-badge demo w3-border w3-transparent w3-hover-white" onclick="currentDiv(3)"></span>
    </div>
</div>

<div class="about-section" data-aos="fade-up">
    <div class="container">
        <div class="about-content-box" data-aos="zoom-in">
            <div class="section-title">
                <h2><strong>About Us</strong></h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <img src="images/aboutus.gif" alt="About Us Image" class="img-fluid about-image" data-aos="flip-left">
                </div>
                <div class="col-md-6" data-aos="fade-right">
                    <p>Welcome to our ZakatEase! The UiTM Perlis Branch Zakat Unit is dedicated to facilitating zakat application processes with ease and efficiency. At Zakat Unit UiTM Perlis Branch, we are committed to supporting our students' educational journey by providing essential financial assistance through zakat contributions.</p>
                    <p>Our mission is to ensure that eligible students receive the necessary support to achieve their academic goals and contribute positively to society. Through a seamless application process and transparent procedures, we strive to make zakat accessible and impactful for our community.</p>
                    <p>Join us in our mission to empower students and foster a community where educational opportunities are accessible to all.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<section id="ways" class="ways">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><strong>Apply Zakat Easily</strong></h2><br>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="box">
                    <h5>01</h5>
                    <p>Register for account</p><br>
                    <img src="images/afz1111.gif" alt="Image 1">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="box">
                    <h5>02</h5>
                    <p>Review eligibility criteria for zakat</p>
                    <img src="images/afz11.gif" alt="Image 2">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="box">
                    <h5>03</h5>
                    <p>Complete the application form</p>
                    <img src="images/afz3333.gif" alt="Image 3">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="box">
                    <h5>04</h5>
                    <p>Track application status</p>
                    <img src="images/afz44.gif" alt="Image 4">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="box">
                    <h5>05</h5>
                    <p>You will be notified by e-mail</p>
                    <img src="images/afz555.gif" alt="Image 5">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="faq">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><strong>Frequently Asked Questions</strong></h2><br>
        </div>
        <div class="accordion" data-aos="fade-up">
            <button class="custom-accordion-button">FAQ 1: What form of assistance is offered by the Zakat Unit UiTM Perlis Branch?</button>
            <div class="custom-accordion-item">
                <div class="accordion-content">Until now, the UiTM Perlis Branch Zakat Unit has offered assistance for self-sufficiency zakat, laptops, and tuition fees.</div>
            </div>
            <button class="custom-accordion-button">FAQ 2: Who can apply for Zakat Assistance (Self-Support/Laptop/Study Fees) UiTM Perlis Branch?</button>
            <div class="custom-accordion-item">
                <div class="accordion-content">All UiTM Perlis Branch students with active status.</div>
            </div>
            <button class="custom-accordion-button">FAQ 3: What are the (mandatory) copies of documents that need to be uploaded?</button>
            <div class="custom-accordion-item">
                <div class="accordion-content">
                    <ol>
                        <li>UiTM matric card (Front and back) or study offer letter.</li>
                        <li>Final exam results last semester.</li>
                        <li>Parent/Guardian Dependency Certificate.</li>
                        <li>Salary Slip / Income Verification Letter.</li>
                    </ol>
                </div>
            </div>
            <button class="custom-accordion-button">FAQ 4: How do I apply for Zakat?</button>
            <div class="custom-accordion-item">
                <div class="accordion-content">To apply for Zakat, simply create an account on ZakatEase, fill out the application form, and submit it for review by our team.</div>
            </div>
            <button class="custom-accordion-button">FAQ 5: What happens after I apply?</button>
            <div class="custom-accordion-item">
                <div class="accordion-content">Once you submit your application, the person in charge will assess it. If successful, you'll receive a QR code for verification to go to interview.</div>
            </div>
        </div>
    </div>
</section>

<div class="container get-in-touch mx-auto" data-aos="fade-up">
    <div class="row justify-content-center">
        <div class="col-md-5 text-center">
            <div class="map-container bg-white shadow-sm rounded">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.5871701429796!2d100.27467067368953!3d6.447015724050128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304ca293fb930483%3A0xd5b1bb9a5ee6576e!2sAcademy%20of%20Contemporary%20Islamic%20Studies%20(ACIS)!5e0!3m2!1sen!2smy!4v1718767056013!5m2!1sen!2smy" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        <div class="col-md-5 contact-info bg-white shadow-sm rounded p-3">
            <h2 class="mb-3 text-center">Get In Touch</h2>
            <div class="contact-item mb-2">
                <i class="fas fa-phone-alt"></i>
                <strong>&nbsp;&nbsp;Phone:</strong> +604-988 2000
            </div>
            <div class="contact-item mb-2">
                <i class="fab fa-whatsapp"></i>
                <strong>&nbsp;&nbsp;WhatsApp:</strong> +604-9882019
            </div>
            <div class="contact-item mb-2">
                <i class="fas fa-envelope"></i>
                <strong>&nbsp;&nbsp;Email:</strong> korporatperlis@uitm.edu.my
            </div>
            <div class="contact-item mb-2">
                <i class="fas fa-map-marker-alt"></i>
                <strong>&nbsp;&nbsp;Address:</strong> UiTM Arau, 02600 Arau, Perlis
            </div>
            <div class="contact-item mb-2">
                <i class="fas fa-clock"></i>
                <strong>&nbsp;&nbsp;Working Hours:</strong> Mon-Fri: 9am-5pm
            </div>
        </div>
    </div>
</div>

<br>
<br>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1200,
        once: true
    });

    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.custom-accordion-button');

        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const item = this.nextElementSibling;
                const isActive = item.classList.toggle('active');
                this.classList.toggle('active');

                document.querySelectorAll('.custom-accordion-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        otherItem.style.maxHeight = '0';
                        otherItem.previousElementSibling.style.backgroundColor = '#FFFFFF';
                        otherItem.previousElementSibling.classList.remove('active');
                    }
                });

                if (isActive) {
                    item.style.maxHeight = item.scrollHeight + 'px';
                    this.style.backgroundColor = '#b6c784';
                } else {
                    item.style.maxHeight = '0';
                    this.style.backgroundColor = '#FFFFFF';
                }
            });
        });
    });

    function isPageScrollable() {
        return document.body.scrollHeight > window.innerHeight;
    }

    function toggleStickyFooter() {
        var footer = document.getElementById('footer');
        if (isPageScrollable()) {
            footer.classList.remove('fixed-bottom');
        } else {
            footer.classList.add('fixed-bottom');
        }
    }

    window.onload = toggleStickyFooter;
    window.onresize = toggleStickyFooter;

    var slideIndex = 0;
    showDivs();

    function plusDivs(n) {
        slideIndex += n;
        showDivs();
    }

    function currentDiv(n) {
        slideIndex = n - 1;
        showDivs();
    }

    function showDivs() {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("demo");
        if (slideIndex >= slides.length) {slideIndex = 0}
        if (slideIndex < 0) {slideIndex = slides.length - 1}
        for (i = 0; i < slides.length; i++) {
            slides[i].classList.remove("active");
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" w3-white", "");
        }
        slides[slideIndex].classList.add("active");
        dots[slideIndex].className += " w3-white";

        for (i = 0; i < slides.length; i++) {
            slides[i].style.left = (i - slideIndex) * 100 + '%';
        }
    }

    setInterval(function() {
        slideIndex++;
        showDivs();
    }, 5000); 
</script>

<?php include 'footer.php'; ?>
</body>
</html>