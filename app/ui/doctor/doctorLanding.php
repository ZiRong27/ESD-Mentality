

<?php
require_once '../include/common.php';

$accountType = "doctor";
require_once '../include/protect.php';
?>
<html>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/doctorNavbar.php';?>

<div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
    <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../images/landing1" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5 style=color:#484848>To Care</h5>
        <p style=color:#484848>We are here for the needs of our patients.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/landing2" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5 style=color:#484848>To Provide Guidance</h5>
        <p style=color:#484848>We are here to help and guide our patients.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/landing3" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5 style=color:#484848>To Actively Listen</h5>
        <p style=color:#484848>We are here to patiently listen to whatever our patients have in mind.</p>
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
      <img class="card-img-top" src="../images/docwithpatient.jpg" height="180" width="250">
      <div class="card-body">
        <h4 class="card-title" style="text-align:center;"> Patients </h4>
        <p class="card-text" style="text-align:center;">View your patients.</p>
      </div>
      <div class="card-footer" style="background-color:white;">
        <div class="row justify-content-center">
        <a href="viewPatient.php" class="btn btn-primary"> View Patients </a>
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
      <p class="card-text" style="text-align:center;">View your appointments.</p>
    </div>
    <div class="card-footer" style="background-color:white;">
    <div class="row justify-content-center">
      <a href="doctorViewAppts.php" class="btn btn-primary"> View Appointments </a>
      </div>
    </div>
  </div>
</div>

<!-- Card 3 --->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card h-100">
      <img class="card-img-top" src="../images/consulthistory.jpg" height="180" width="250">
      <div class="card-body">
        <h4 class="card-title" style="text-align:center;"> Consultation</h4>
        <p class="card-text" style="text-align:center;"> View your patients' consultation history and review their progress.  </p>
      </div>
      <div class="card-footer" style="background-color:white;"> 
        <div class="row justify-content-center">
        <a href="doctorConsultation.php" class="btn btn-primary"> View Consultation History </a>
        </div>
      </div>
    </div>
  </div>

</div>

<script>    

</script>

</body>

</html>