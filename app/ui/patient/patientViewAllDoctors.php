<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';
?>
<html>

<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        /*.table-bordered th, .table-bordered td { border: 2px solid #ddd!important }*/
    </style>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>
</br></br>

<br/>
<!-- Page Content -->
<div class="container">

<!-- Page Heading -->
<h1 class="my-4">Book an Appointment
  <!-- <small>with our therapists</small> -->
</h1>
<br>

<!-- List of Doctors (to be appended) -->
<div id="doctor-list">
</div>


</div>
<!-- /.container -->
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }
    //This is the form id, not the submit button id!
    $(async() => { 
        //This is the url found above the get_all function in doctor.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the doctor class in doctor.py
        var serviceURL = "http://" + doctorip + "/view-all-doctors";
        //var serviceURL = "http://" + doctorip + "/view-all-doctors";
        try {
                //console.log(JSON.stringify({ username: username, password: password,}))
                const response =
                 await fetch(
                   serviceURL, { method: 'GET' }
                );
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    // for loop to setup all table rows with obtained doctors data
                    //data = data["doctors"];
                    //get current year
                    var currentyear = new Date().getFullYear();
                    //Js month is off by 1
                    var currentmonth = new Date().getMonth() + 1;
                    var currentday = new Date().getDate();
                    for (i = 0; i < data.length; i++) {
                        //Calculate age using date of birth, dob needs to be str not date in mysql or its format will be very weird
                        //Split the dob, first element is year, 2nd is month, 3rd is day
                        dobarr = data[i].dob.split("-") ;
                        age =  currentyear - dobarr[0];
                        console.log(age,currentmonth, currentday, dobarr[1], dobarr[2]);
                        //If havent past birthday yet, deduct age by one
                        if ( (currentmonth < dobarr[1]) || (currentmonth == dobarr[1] && currentday < dobarr[2]) ) {
                            age = age - 1;
                        }            
                        Row =
                            '<div class="row">' + 
                            '<div class="col-md-6">' +
                            '<a href="#">' +
                            '<img class="img-fluid rounded mb-3 mb-md-0" src="../images/doctors/' + data[i].doctor_id + '.png" alt="">' +
                            '</a>' +
                            '</div>' +
                            '<div class="col-md-5">' +
                            '<h2>' + data[i].name + '</h2>' + 
                            '<h5 class= "text-secondary">' + data[i].experience + '</h5>' + 
                            '<dl class="row">' +
                            '<dt class="col-sm-3">Age</dt>'+
                            '<dd class="col-sm-9">' + age + '</dd>' +
                            '<dt class="col-sm-3">Price</dt>' +
                            '<dd class="col-sm-9">$' +  data[i].price + '</dd>' + 
                            '<dt class="col-sm-3">Specialisation</dt>' +
                            '<dd class="col-sm-9">' +  data[i].specialisation + '</dd>' +
                            '</dl>' +
                            '<a class="btn btn-primary" href="viewDoctor.php?username=' + data[i].username + '">Book Now</a>' +
                            '</div>' +
                            '</div>' +
                            '<hr>';
                        $('#doctor-list').append(Row);
                    }
                    //Add the t body
                    $('#doctorsTable').append("</tbody>");              
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving doctors data, please try again later. Tip: Did you forget to run doctor.py? :)<br />'+error);
               
            } 
    });
</script>
</body>

</html>