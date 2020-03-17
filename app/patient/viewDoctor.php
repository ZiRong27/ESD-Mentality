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


<body>
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>
</br></br>

<br/>
<div id="main-container" class="container">
    <div class = "whitetextbig" style="color: white; font-weight: bold; font-size: 200%;" id="doctorName">        
            Book an appointment
    </div> 
    <div>
        <img id = "doctorPicture" width = '150px' height = '150px'>
    </div>
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="doctorsTable">
    <thead>
    </thead>
    <tbody>
    </tbody>
    </table>  
    <!-- <form id='bookForm'>
        <div class="text-right">
            <p style="color: white; font-size: 100%;">Date: <input type='date' name='booking_date' id='booking_date'>
            <p style="color: white; font-size: 100%;">Time: <input type='text' name='booking_time' id='booking_time'>     
            <button type='submit' class="btn btn-primary btn-lg" id='booking_submit'>Submit booking</button>
        </div>
    </form> -->
    <div>
    <form id='dateForm'>    
            <input type='date' name='booking_date' id='booking_date'>
            <!-- <input type='text' name='booking_time' id='booking_time'> -->
            <button type='submit' class="btn btn-primary btn-lg" id='date_submit'>Choose Date</button>
    </form>
    </div>
    <!-- <div>
    <form id='bookForm'>    
            <button type='submit' class="btn btn-primary btn-lg" id='booking_submit'>Submit Booking</button>
    </form>
    </div> -->
    
    
    <form id='bookForm'> 
        <!-- <div class="text-right">     -->
        <div id='timeslotTable'>
            <!-- <input type='date' name='booking_date' id='booking_date'>
            <input type='text' name='booking_time' id='booking_time'>
            <button type='submit' class="btn btn-primary btn-lg" id='booking_submit'>Submit booking</button> -->
        </div>  
    </form>
    
    <!-- <table class="table table-borderless table-hover text-center" id="TimeslotsTable">
    <thead>
    </thead>
    <tbody>
    </tbody>
    </table> -->
</div>
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        console.log(message)
    }
    $(async() => { 
        //This is the url found above the get_all function in doctor.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the doctor class in doctor.py
        //Get the doctor username from the url
        let params = new URLSearchParams(location.search);
        username = params.get('username')
        var serviceURL = "http://127.0.0.1:5002/view-specific-doctor/" + username;
        //var serviceURL = "http://" + sessionStorage.getItem("doctorip") + "/view-specific-doctor/" + username ;
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
                    $('#doctorPicture').attr("src", "../images/doctors/" + data["doctor_id"] + ".png");
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
                    Row =
                        "<tr><th>Gender</th><td>" + data.gender + "</td></tr>" +
                        "<tr><th>Age</th><td>" + age + "</td></tr>" +
                        "<tr><th>Experience</th><td>" + data.experience + "</td></tr>" +
                        "<tr><th>Specialisation</th><td>" + data.specialisation + "</td></tr>";
                    $('#doctorsTable').append(Row);
                    price = data.price;
                    doctor_id = data.doctor_id;
                    //Add the t body
                    $('#doctorsTable').append("</tbody>");              
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
        
        //var appointment_id = $('#appointment_id').val();
        //var doctor_name = $('#doctor_name').val();
        var date = String($('#booking_date').val());
        //console.log(date);
        //var time = $('#time').val();
        //var price = $('#price').val();
        
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        var serviceURL = "http://127.0.0.1:5003/appointment-by-date/" + date;
    
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
                    //Refreshes the page
                    //window.location.href = "patientUpdateAppts.php"; 
                    // $('#TimeslotsTable').append("<tbody>"); 
                    // $('#TimeslotsTable').append("<tr><form id='bookForm'>"); 
                    
                    timeslots = ['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00']
                    //console.log(timeslots.length);
                    for (i = 0; i < timeslots.length; i++){
                        //console.log("yo");
                        if (jQuery.inArray(timeslots[i], timings) == -1){ // if timing is available
                            //console.log("hey");
                            Row = "<button type='submit' class='btn btn-success btn-sm' name='booking_time' value=" + timeslots[i] + "'>" + timeslots[i] + "</button>";
                            $('#timeslotTable').append(Row); 
                            console.log(Row);
                        }
                    }
                    //$('#TimeslotsTable').append("</form></tr>"); 
                    // $('#TimeslotsTable').append("<tr><td>");
                    // $('#TimeslotsTable').append("<form id='bookForm'><div class='text-right'>");
                    // $('#TimeslotsTable').append("<button type='submit' class='btn btn-primary btn-lg' id='booking_submit'>Submit Booking</button>");
                    // $('#TimeslotsTable').append("</form></div>");
                    // $('#TimeslotsTable').append("</td></tr>"); 
                    //$('#TimeslotsTable').append("</tbody>"); 
                    
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

            var booking_time = $('#timeslotTable').val();
            //var booking_time = $('#time').val();
            var patient_id = sessionStorage.getItem("patient_id");
            $('#patient_id').val(patient_id); 
            
            var serviceURL = "http://127.0.0.1:5003/create-appointment";
            //var serviceURL = "http://" + sessionStorage.getItem("appointmentip") + "/create-appointment";
            try {
                console.log(JSON.stringify({ doctor_id: doctor_id,
                                            patient_id: patient_id,
                                            date: booking_date,
                                            time: booking_time}))
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ doctor_id: doctor_id,
                                               patient_id: patient_id,
                                               date: booking_date,
                                               time: booking_time})
                                            });
              
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    alert("Appointment successfully booked!")
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving appointment data, please try again later. Tip: Did you forget to run appointment.py? :)<br />'+error);
               
            } 

       
    });

</script>

    <!-- <table class="table table-striped table-light table-hover text-center" id="TimeslotsTable">
    <thead>
    </thead>
    <tbody>
    </tbody>
    </table> -->
</body>

</html>