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
        <th scope="col">Consultation ID</th>
        <th scope="col"> Patient ID </th>
        <th scope="col"> Patient Name </th>
        <th scope="col"> Appointment ID </th>
        <th scope="col"> View Consultation </th>
        </tr>
    </thead>
    </table>  
</div>
</div>
</div>
<script>    
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
    //var serviceURL_consultation = "http://" + consultationip + "/consultation-by-doctor/" + doctor_id;
    var serviceURL_consultation = "http://" + consultationip + "/consultation-by-doctor/" + doctor_id;
    //var serviceURL_patients = "http://" + patientip + "/view-all-patients";
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
      if (!data_consultation && !data_patients) 
      {
        console.log("Error retrving from Database");
      } 
      // else display
      else
      {
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
            row = 
              "<tbody><tr>" + 
                  "<td>" + data_consultation[i]["consultation_id"] + "</td>" + 
                  "<td>" + data_consultation[i]["patient_id"] + "</td>" + 
                  "<td>" + name + "</td>" + 
                  "<td>" + data_consultation[i]["appointment_id"] + "</td>" + 
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
  });
</script>
</body>

</html>