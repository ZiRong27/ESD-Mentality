<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';

if (isset($_GET['session_id'])){
    $session_id = $_GET['session_id'];
}

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
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css"/>
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>
</br></br>

<div id="appointmentSuccess">

</div>


<div id="main-container" class="container">
    <div class = "my-2">        
        <h1>My Appointment(s)</h1>
        <hr>
    </div> 
    
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="apptTable" style="border: 1px solid #e0e0e0;">
    <thead>
        <tr >
        <th scope="col">Appointment ID</th>
        <th scope="col">Doctor</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">Price</th>
        </tr>
    </form>
    </thead>
    <tbody>
    </tbody>
    </table>  
    <br> 
</div>
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged');
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }
    function showAppointmentAdded(appointment){
    

    }

    async function fetchURLs() {
    try {
      // Promise.all() lets us coalesce multiple promises into a single super-promise
      var data = await Promise.all([
        fetch("http://" + doctorip + "/view-all-doctors").then((response) => response.json())
        // fetch("http://" + appointmentip + "/view-all-appointments").then(
        //     (response) => response.json()),// parse each response as json
        //fetch("http://" + doctorip + "/view-all-doctors").then((response) => response.json())
      ]);
      doctor = {}
      
        for (var obj of data[0]) {
            doctor_id = obj["doctor_id"];
            doctor[doctor_id] = [obj["name"],obj["price"]];
        }
      
      console.log(doctor);
      return doctor;

    } catch (error) {
      console.log(error);
    }
  }


    //This is the form id, not the submit button id!
    $(async() => { 

        var session_id = "<?php if (isset($session_id)) { echo $session_id; } else { echo 'false'; } ?>";

        // Check if this page is accessed from a successful payment event
        // In which case it will double check payment status
        // And inform user regarding a successful booking creation
        if (session_id != "false"){
                    
            var serviceURL = "http://" + paymentip + "/success/" + session_id;

            const response =
                await fetch(
                serviceURL, { method: 'GET' }
                );

            const data = await response.json();
            
            if (data['message']){
                showError(data['message']);
            }else{
                console.log(data['appointment']);
                $('#appointmentSuccess').append(
                    '<div class="alert alert-success" role="alert">Appointment added successfully: <b>' +
                    data['appointment']['date'] + 
                    ', ' +
                    data['appointment']['time'] +
                    '</div>'
                );
            }
        }

        fetchURLs();
        var patient_id = sessionStorage.getItem("patient_id");
        $('#patient_id').val(patient_id); 
        //This is the url found above the get_all function in doctor.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the doctor class in doctor.py
        //var serviceURL = "http://" + appointmentip + "/view-all-appointments";
        var serviceURL = "http://" + appointmentip + "/view-all-appointments";
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
                        if (data[i].patient_id == patient_id){
                            doctorName = doctor[data[i].doctor_id][0];
                            price = doctor[data[i].doctor_id][1];
                            Row =
                            "<tr><th scope='row'>" + data[i].appointment_id + "</th>" +
                            "<td>" + doctorName + "</td>" +
                            "<td>" + data[i].date + "</td>" +
                            "<td>" + data[i].time + "</td>" +
                            "<td>" + price + "</td>" +
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