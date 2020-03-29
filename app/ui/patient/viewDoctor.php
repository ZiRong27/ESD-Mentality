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
        #doctorPicture {
            display: block;
            margin: 0 auto;
        }

        #doctorName {
            text-align: center;
        }

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

<!-- Doctor Summary -->
<div class="row">
  <div id="picture" class="col-md-6">
  </div>
  <div class="col-md-5">
    <h2 id="name"></h2>
    <h5 id="experience" class= "text-secondary"></h5>
    <dl class="row">
      <dt class="col-sm-3">Age</dt>
      <dd id="age" class="col-sm-9"></dd>
      <dt class="col-sm-3">Price</dt>
      <dd id= "price" class="col-sm-9">$</dd>
      <dt class="col-sm-3">Specialisation</dt>
      <dd id = "specialisation" class="col-sm-9"></dd>
    </dl>
    <!-- Date Form -->
<<<<<<< HEAD
    <hr>
    <span style="font-weight:bold;">Select Appointment Date:</span>
    <form id='dateForm' class="form-inline">
      <div class=text-right>
        <input type="date" class="form-control" id="booking_date" style="height:37px">  &ensp;
      </div>
      <button type="date_submit" class="btn btn-primary mb-2" id="date_submit" style="width:80px; height:37px; padding:1px">Submit</button>
=======
    <form id='dateForm' class="form-inline">
      <div class="form-group mx-sm-3 mb-2">
        <input type="date" class="form-control" id="booking_date">
      </div>
      <button type="date_submit" class="btn btn-primary mb-2" id="date_submit">Choose Date</button>
>>>>>>> 38a6fea689e71e6d616d679033951e915ff7dcfd
    </form>
  </div>
</div>


<div>
<form id='bookForm'> 
  <br>
  <h4 id='timeslot-header'></h4>
  <br>
        <!-- <div class="text-right">     -->

<table class="table table-hover text-center" id="timeslotTable"> 

</table>
    <!-- <input type='date' name='booking_date' id='booking_date'>
    <input type='text' name='booking_time' id='booking_time'>
    <button type='submit' class="btn btn-primary btn-lg" id='booking_submit'>Submit booking</button> --> 
</form>


    <!-- This form will be automatically submitted upon booking-->   
    <form method="POST" action="checkout.php" id="checkoutForm">
    </form>
</div>
</div>
<!-- /.container -->

<script>    

    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        console.log(message)
    }
    $(async() => { 
        //This is the url found above the get_all function in doctor.py. 
        // Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the doctor class in doctor.py
        //Get the doctor username from the url
        let params = new URLSearchParams(location.search);
        username = params.get('username')
        var serviceURL = "http://" + doctorip + "/view-specific-doctor/" + username;
        //var serviceURL = "http://" + doctorip + "/view-specific-doctor/" + username ;
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
                    //Display the doctor name
                    $('#doctorName').html(data.name);
                    //Display the doctor picture
                    $('#doctorPicture').attr("src", "../images/doctors/" + data["doctor_id"] + ".jpg");
                    $('#doctorsTable').append("<tbody>");
                    //get current year
                    var currentyear = new Date().getFullYear();
                    //Js month is off by 1
                    var currentmonth = new Date().getMonth() + 1;
                    var currentday = new Date().getDate();
                    //Calculate age using date of birth, dob needs to be str not date in mysql or its format will be very weird
                    //Split the dob, first element is year, 2nd is month, 3rd is day
                    dobarr = data.dob.split("-") ;
                    age =  currentyear - dobarr[0];
                    console.log(age,currentmonth, currentday, dobarr[1], dobarr[2]);
                    //If havent past birthday yet, deduct age by one
                    if ( (currentmonth < dobarr[1]) || (currentmonth == dobarr[1] && currentday < dobarr[2]) ) {
                        age = age - 1;
                    } 

                    // append doctor's summary
<<<<<<< HEAD
                    $('#picture').append('<img class="img-fluid rounded mb-3 mb-md-0" src="../images/doctors/' + data.doctor_id + '.jpg" alt="">');
=======
                    $('#picture').append('<img class="img-fluid rounded mb-3 mb-md-0" src="../images/doctors/' + data.doctor_id + '.png" alt="">');
>>>>>>> 38a6fea689e71e6d616d679033951e915ff7dcfd
                    $('#name').append(data.name);
                    $('#experience').append(data.experience);
                    $('#price').append(data.price);
                    $('#specialisation').append(data.specialisation);
                    $('#age').append(age);

                    price = data.price;
                    doctor_id = data.doctor_id;
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving doctors data, please try again later. Tip: Did you forget to run doctor.py? :)<br />'+error);
               
            } 
    });


    // timeslot table appear
    $("#dateForm").submit(async (event) => {
        event.preventDefault();     
        var date = String($('#booking_date').val());

        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
<<<<<<< HEAD
        var serviceURL = "http://" + appointmentip + "/appointment-by-date/" + date;
=======
        var serviceURL = "http://" + appointmentip + "/appointment-by-date/" + date + "/" + doctor_id;
>>>>>>> 38a6fea689e71e6d616d679033951e915ff7dcfd
    
        try {
                //console.log(JSON.stringify({ username: username, password: password,}))
                const response = await fetch(
                   serviceURL, { method: 'GET' }
                );
                const data = await response.json();

                timings = [];
                for (var obj of data) {
                    time = obj["time"];
                    timings.push(time);
                }
                console.log(timings);
                
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    var hidden_input = 
                            '<input type="hidden" id="doctor_id" value="' + doctor_id + '" />' +
                            '<input type="hidden" id="cost" value="' + price + '" />';
                    $('#bookForm').append(hidden_input);

<<<<<<< HEAD
                    $('#timeslot-header').append("Select Your Preferred Timeslot");

                    timeslots_display = ['09:00 AM - 10:00 AM','10:00 AM - 11:00 AM','11:00 AM - 12:00 PM','12:00 PM - 13:00 PM','13:00 PM - 14:00 PM','14:00 PM - 15:00 PM','15:00 PM - 16:00 PM','16:00 PM - 17:00 PM','17:00 PM - 18:00 PM']
                    timeslots = ['09:00 AM','10:00 AM','11:00 AM','12:00 PM','13:00 PM','14:00 PM','15:00 PM','16:00 PM','17:00 PM']

=======
                    $('#timeslot-header').empty();
                    $('#timeslot-header').append("Select Your Prefered Timeslot");

                    timeslots_display = ['09:00 AM - 10:00 AM','10:00 AM - 11:00 AM','11:00 AM - 12:00 PM','12:00 PM - 13:00 PM','13:00 PM - 14:00 PM','14:00 PM - 15:00 PM','15:00 PM - 16:00 PM','16:00 PM - 17:00 PM','17:00 PM - 18:00 PM']
                    timeslots = ['09:00 AM','10:00 AM','11:00 AM','12:00 PM','13:00 PM','14:00 PM','15:00 PM','16:00 PM','17:00 PM']
                    
                    $('#timeslotTable').empty(); 
>>>>>>> 38a6fea689e71e6d616d679033951e915ff7dcfd
                    for (i = 0; i < timeslots.length; i++){
                        if (jQuery.inArray(timeslots[i], timings) == -1){ // if timing is available
                            Row = 
                              "<tr><td class='text-left'>" + timeslots_display[i] + "</td><td class='text-right'>" + 
                              "<button type='submit' value='" + timeslots[i] + "' class='btn btn-success' id='booking_submit'>Submit booking</button></td></tr>";
                            $('#timeslotTable').append(Row); 
                        }
                    }
                    
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
            ('There is a problem retrieving available timeslots, please try again later. Tip: Did you forget to run appointment.py? :)<br />'+error);
            
            }
        
    });
    

    // onclick "book an appointment", sends doctor_id and patient_id
    
        $("#bookForm").submit(async (event) => {
            event.preventDefault();     
            var booking_date = $('#booking_date').val();
            //var booking_time = $("#booking_submit").val();
            var booking_time = $(document.activeElement).val()
            var doctor_id = $("#doctor_id").val();
            var price = $("#cost").val();
            var patient_id = sessionStorage.getItem("patient_id");

            $('#patient_id').val(patient_id); 
            
            var serviceURL = "http://" + paymentip + "/checkout";
            // var serviceURL = "http://" + appointmentip + "/create-appointment";
            try {
                console.log(JSON.stringify({ doctor_id: doctor_id,
                                            patient_id: patient_id,
                                            price: price,
                                            date: booking_date,
                                            time: booking_time}))
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ doctor_id: doctor_id,
                                               patient_id: patient_id,
                                               price: price,
                                               date: booking_date,
                                               time: booking_time})
                                            });

                const data = await response.json();

                // The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    alert("You will be redirected to payment")

                    var hidden_input = 
                            '<input type="hidden" name="CHECKOUT_SESSION_ID" value="' + data['CHECKOUT_SESSION_ID'] + '" />' +
                            '<input type="hidden" name="pub_key" value="' + data['pub_key'] + '" />';
                    $('#checkoutForm').append(hidden_input);

                    $('#checkoutForm').submit();

                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving appointment data, please try again later. Tip: Did you forget to run appointment.py? :)<br />'+error);
               
            } 

       
    });

</script>
</body>

</html>