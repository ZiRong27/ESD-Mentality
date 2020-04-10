<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';
?>
<html>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css"/>
</header>

<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>

<div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../images/landing1.jpg" class="d-block w-100 h-50" style="opacity:0.65;">
      <div class="carousel-caption d-none d-md-block">
        <h5 style="color:#484848; font-size:25px;">Here For You</h5>
        <p style="color:#484848; font-size:20px; font-weight:480;">We are here for you and your needs.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/landing2.jpg" class="d-block w-100 h-50" style="opacity:0.65;">
      <div class="carousel-caption d-none d-md-block">
        <h5 style="color:#484848; font-size:25px;">Providing You Guidance</h5>
        <p style="color:#484848; font-size:20px; font-weight:480;">We are here to help and guide you.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/landing3.jpg" class="d-block w-100 h-50" style="opacity:0.65;">
      <div class="carousel-caption d-none d-md-block">
        <h5 style="color:#484848; font-size:25px;">Caring For You</h5>
        <p style="color:#484848; font-size:20px; font-weight:480;">We are here to patiently listen to whatever you have in mind.</p>
      </div>
    </div>
  </div>
  <!-- Left arrow key -->
  <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <!-- Right arrow key -->
  <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<br>

<div class="row justify-content-center">
  <!-- Card 1--->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100">
      <img class="card-img-top" src="../images/book.jpg" height="180" width="250">
      <div class="card-body">
        <h4 class="card-title" style="text-align:center;"> Book </h4>
        <p class="card-text" style="text-align:center;">Make a booking with our range of qualified counsellors. </p>
      </div>
      <div class="card-footer" style="background-color:white;">
        <div class="row justify-content-center">
        <a href="patientViewAllDoctors.php" class="btn btn-primary"> Make Booking </a>
        </div>
      </div>
    </div>
  </div>

<!-- Card 2 --->
<div class="col-lg-3 col-md-6 mb-4">
  <div class="card h-100">
    <img class="card-img-top" src="../images/viewappointment.jpg" height="180" width="250">
    <div class="card-body">
      <h4 class="card-title" style="text-align:center;"> Appointments </h4>
      <p class="card-text" style="text-align:center;">View all your appointments made.</p>
    </div>
    <div class="card-footer" style="background-color:white;">
    <div class="row justify-content-center">
      <a href="patientUpdateAppts.php" class="btn btn-primary"> View Appointments </a>
      </div>
    </div>
  </div>
</div>

<!-- Card 3 --->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100">
      <img class="card-img-top" src="../images/consulthistory.jpg" height="180" width="250">
      <div class="card-body">
        <h4 class="card-title" style="text-align:center;"> Consultation History </h4>
        <p class="card-text" style="text-align:center;"> View your consultation history with us to review your progress.  </p>
      </div>
      <div class="card-footer" style="background-color:white;">
        <div class="row justify-content-center">
        <a href="patientConsultation.php" class="btn btn-primary"> View Consultation History </a>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

<script>
  $(() => {
    $("#home").addClass("active");
  });
</script>

</body>
</html>

