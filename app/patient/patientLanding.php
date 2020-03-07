

<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';
?>
<html>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body>
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

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="images/carousel-design-2.png" height="180" width="250">
          <div class="card-body">
            <h4 class="card-title"> Bookings </h4>
            <p class="card-text">Book or schedule an appomient with our doctors </p>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary"> Bookings </a>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="images/carousel-design-3.png" height="180" width="250">
          <div class="card-body">
            <h4 class="card-title"> Payments </h4>
            <p class="card-text"> Make and organise your payments  </p>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary"> Payments </a>
          </div>
        </div>
      </div>
    <!--
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="http://placehold.it/500x325" alt="">
          <div class="card-body">
            <h4 class="card-title">Card title</h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo magni sapiente, tempore debitis beatae culpa natus architecto.</p>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary">Find Out More!</a>
          </div>
        </div>
      </div>
      -->

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->

<!-- Import footer -->
<?php include 'imports/footer.php';?>
</body>

</html>