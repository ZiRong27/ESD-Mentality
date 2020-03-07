
<html>
<header>
    <?php include 'imports/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "imports/stylesheet.css" />
    <style>
      body {
        padding-top: 56px;
        }
      </style>

</header>


<body>
<!-- Import navigation bar -->
<?php include 'imports/patientNavbar.php';?>

  <!-- Page Content -->
  <div class="container">

    <!-- Jumbotron Header -->
    <header class="jumbotron my-4">
      <h1 class="display-3"> "Name Here" </h1>
      <p class="lead"> Welcome to CAS, we hope that you will have a enjoyable experience using this service </p>
      <a href="#" class="btn btn-primary btn-lg">Call to action!</a>
    </header>

    <!-- Page Features -->
    <div class="row text-center">

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
          <img class="card-img-top" src="images/carousel-design-1.png" height="180" width="250">
          <div class="card-body">
            <h4 class="card-title"> My Profile </h4>
            <p class="card-text"> Manage your medical profile, This will empower our doctors to better make decision and fast.</p>
          </div>
          <div class="card-footer">
            <a href="#" class="btn btn-primary"> Profile </a>
          </div>
        </div>
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