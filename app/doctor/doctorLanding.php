

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


<body>
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
      <img src="../images/carousel-design-1" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5>First slide label</h5>
        <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/carousel-design-2" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5>Second slide label</h5>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../images/carousel-design-3" class="d-block w-100 h-50">
      <div class="carousel-caption d-none d-md-block">
        <h5>Third slide label</h5>
        <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
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

</br>

<div class="container col-12">

    <div class="row d-flex justify-content-center">

      <!-- Card 1 -->
        <div class="col-4 justify-content-center">
          <div class="card">
            <div class="card-body">
                <div class="patientLanding-card">
                  <h5 class="card-title"> Consultation </h5>
                  <p class="card-text"> View and schedule an appointment with our doctors. </p>
                  <a href="../patient/patientConsultation.php" class="btn btn-primary btn-lg"> Consultation </a>
                </div>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-4 justify-content-center">
          <div class="card">
            <div class="card-body">
              <div class="patientLanding-card">
                <h5 class="card-title"> Bookings </h5>
                <p class="card-text"> View all your booking made with your doctor.</p>
                <a href="#" class="btn btn-primary btn-lg"> Bookings </a>
              </div>
            </div>
          </div>
        </div>

      <!-- Card 3 -->
      <div class="col-4 justify-content-center">
          <div class="card">
            <div class="card-body">
              <div class="patientLanding-card">
                <h5 class="card-title"> Payments </h5>
                <p class="card-text"> Made your payment through our mentality. </p>
                <a href="#" class="btn btn-primary btn-lg"> Payments </a>
              </div>
            </div>
          </div>
        </div>

    </div>

</div>

<script>    

</script>

</body>

</html>