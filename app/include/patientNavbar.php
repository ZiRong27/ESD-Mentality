<html>
<?php include 'codeLinks.php';?>
<link rel = "stylesheet" type = "text/css" href = "stylesheet.css" />
<head>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">
        <img class="d-inline-block align-middle mr-2" src="../images/logo.png" width="128px" height="50px">
    </a>
    <!--
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    -->
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="../patient/patientLanding.php">Home <span class="sr-only">(current)</span> </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../patient/patientUpdateProfile.php"> Profile </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../patient/patientViewAllDoctors.php"> Book an appointment </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../patient/patientConsultation.php"> Consultation </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"> Bookings </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"> Payments </a>
            </li>
        </ul>
        <!--Create another ul with the class ml-auto to align it to the right-->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link" id="usernamedisplay">  </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php?signout=true"> Sign out </a>
            </li>
        </ul>
    </div>
</nav>
<script>
//Show the currently logged in username
$('#usernamedisplay').html(sessionStorage.getItem('username'))
</script>
</body>
</html>