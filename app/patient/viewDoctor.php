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
<div id="main-container" class="container">
<div id=whole style="border:1px solid #696969; border-radius:20px; padding:10px; box-shadow: 2px 3px #989898; background:white;">
    <div style="align:center">
        <img id = "doctorPicture" width = '150px' height = '150px'>
    </div>
    <div style="color: #383838; font-weight: bold; font-size: 200%;" id="doctorName">       
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
    <form id='bookForm'> 
        <div class="text-right">    
            <input type='date' name='booking_date' id='booking_date'>
            <input type='text' name='booking_time' id='booking_time'>
            <button type='submit' class="btn btn-primary btn-lg" id='booking_submit'>Submit booking</button>
        </div>  
    </form>
</div>
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
        var serviceURL = "http://" + sessionStorage.getItem("doctorip") + "/view-specific-doctor/" + username ;
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
                        "<tr><th>Specialisation</th><td>" + data.specialisation + "</td></tr>" +
                        "<th colspan='2'> <a href='#'>Book an appointment</a> </th></tr>";
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

    // onclick "book an appointment", sends doctor_id and patient_id
    
        $("#bookForm").submit(async (event) => {
            event.preventDefault();     
            var booking_date = $('#booking_date').val();
            var booking_time = $('#booking_time').val();
            var patient_id = sessionStorage.getItem("patient_id");
            $('#patient_id').val(patient_id); 
            
            var serviceURL = "http://" + sessionStorage.getItem("appointmentip") + "/create-appointment";
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
</body>

</html>