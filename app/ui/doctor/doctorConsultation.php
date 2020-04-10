<?php
require_once '../include/common.php';

$accountType = "doctor";
require_once '../include/protect.php';
?>
<html>
<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css"/>
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/doctorNavbar.php';?>
</br></br>


<div class="row d-flex justify-content-center">
    <div id="main-container" class="container" style="border:1px;">        
              <h1>My Consultations:</h1>
              <hr> 
        <div class ="index-errormsg"></div>
        <br>  
          <table class="table table-striped table-light table-hover text-center" id="conTable" style="border:3px solid #f0f0f0;">
            <thead>
                <tr >
                    <th scope="col"> Consultation ID </th>
                    <th scope="col"> Patient Name </th>
                    <th scope="col"> Date </th>
                    <th scope="col"> Time </th>
                    <th scope="col"> View Consultation </th>
                </tr>
            </thead>
          </table>  
          <br><br>
          <h1 id="displayMessage" class="text-center" style="font-size:20px">No consultations available </h1>
    </div>
</div>

<script> 

    $(() => {
      $("#consultation").addClass("active");
    });

    // Helper function to display error message
    function showError(message) 
    {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }
  // Function: Get all consultation by doctor_ID
  $(async (event) =>
  {
    var doctor_id = sessionStorage.getItem("doctor_id");
    var serviceURL_consultation = "http://" + consultationip + "/consultation-by-doctor/" + doctor_id;
    var serviceURL_patients = "http://" + patientip + "/view-all-patients";
    try 
    {
    // retrieve consultation data by doctor
      const response_consultation = await fetch(serviceURL_consultation, { method: 'GET' });
      const data_consultation = await response_consultation.json(); 
      console.log(data_consultation)
    // retrieve patients
      const response_patients = await fetch(serviceURL_patients, { method: 'GET' });
      const data_patients = await response_patients.json(); 
      console.log(data_patients)

    // If retrieve data failed, result to no data
      if (!data_consultation || data_consultation["message"] == "consultation by doctor id not found." || data_consultation.length == 0) 
      {
        $("#displayMessage").show();
      } 
      // else display
      else
      {
        $("#displayMessage").hide();
        console.log(Object.values(data_consultation))
        // loop through consultation data by doctor
        for (i = 0; i < data_consultation.length; i++) 
        { 
            // loop through patient, matching patient ID to Patient name
            for (j = 0; j < data_patients.length; j++)
            {
                if(data_consultation[i]["patient_id"] == data_patients[j]["patient_id"])
                {
                    var name = data_patients[j]["name"];
                }
            }

            date = await getDate(data_consultation[i]["appointment_id"])
            time = await getTime(data_consultation[i]["appointment_id"])

            row = 
              "<tbody><tr>" + 
                  "<td>" + data_consultation[i]["consultation_id"] + "</td>" + 
                  "<td>" + name + "</td>" + 
                  "<td>" + date + "</td>" + 
                  "<td>" + time + "</td>" + 
                  "<td> <a href='viewConsultation.php?consultationtid=" + data_consultation[i]["consultation_id"] + "&patientname=" + name +"'> View Consultation </a> </td>" +
              "</tr></tbody>";
            $('#conTable').append(row);
        }
      }
    }
    catch(error)
    {
      console.log("Error in connecting to Mircoservice!");
    }

    async function getDate(data) 
    {
      var serviceURL_appointment = "http://" + appointmentip + "/get-appointment-id-history/" + data;
      try 
      {
        var response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
        var data_appointment = await response_appointment.json(); 
        return data_appointment["date"]
       }       
      catch (error) 
      {
         console.log("Unable to connect to Appointment History to retrive Date");
       }
    }

    async function getTime(data) 
    {
      var serviceURL_appointment = "http://" + appointmentip + "/get-appointment-id-history/" + data;
      try 
      {
        var response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
        var data_appointment = await response_appointment.json(); 
        return data_appointment["time"]
       }       
      catch (error) 
      {
         console.log("Unable to connect to Appointment History to retrive Date");
       }
    }

  });
</script>
</body>

</html>