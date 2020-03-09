<?php
require_once '../include/common.php';

$accountType = "doctor";
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
    <div class = "whitetextbig" style="color: white; font-weight: bold; font-size: 200%;">        
            My Appointments
    </div> 
    <br> 
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="apptTable">
    <thead>
        <tr >
        <th scope="col"># Appointment ID</th>
        <th scope="col">Patient</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Price</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    </table>   
</div>
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }
    //This is the form id, not the submit button id!
    $(async() => { 
        var doctor_id = sessionStorage.getItem("doctor_id");
        $('#doctor_id').val(doctor_id); 
        //This is the url found above the get_all function in doctor.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the doctor class in doctor.py
        var serviceURL = "http://127.0.0.1:5003/view-all-appointments";
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
                    $('#apptTable').append("<tbody>");

                    
                    for (i = 0; i < data.length; i++) { 
                        if (data[i].doctor_id == doctor_id){
                            Row =
                            "<tr><th scope='row'>" + data[i].appointment_id + "</th>" +
                            "<td>" + "patientName" + "</td>" +
                            "<td>" + data[i].date + "</td>" +
                            "<td>" + data[i].time + "</td>" +
                            "<td>" + data[i].price + "</td>" +
                            "<td></td></tr>";
                            $('#apptTable').append(Row);
                        } 
                    }
                    //Add the t body
                    $('#apptTable').append("</tbody>");              
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving appointments data, please try again later. Tip: Did you forget to run appointment.py? :)<br />'+error);
               
            } 
    });
</script>
</body>

</html>