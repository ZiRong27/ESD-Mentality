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


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/doctorNavbar.php';?>
</br></br>

<br/>
<div id="main-container" class="container" style="border:1px solid #696969; border-radius:20px; padding:10px; box-shadow: 2px 3px #989898; background:white;">
    <div class = "whitetextbig" style="color: black; font-weight: bold; font-size: 180%;">        
            My Appointments:
    </div> 
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="apptTable" style="border:3px solid #f0f0f0;">
    <thead>
        <tr >
        <th scope="col"># Appointment ID</th>
        <th scope="col">Patient Name</th>
        <th scope="col">Date</th>
        <th scope="col">Time</th>
        <th scope="col">View profile</th>
        </tr>
    </thead>
    </table>  
</div>
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }
/*
    async function fetchURLs() 
    {
      try 
      {
        // Promise.all() lets us coalesce multiple promises into a single super-promise
        var data = await Promise.all
        ([
          //fetch("http://127.0.0.1:5002/view-all-doctors").then((response) => response.json()),
          //fetch("http://127.0.0.1:5001/view-all-patients").then((response) => response.json())
          fetch("http://" + sessionStorage.getItem("doctorip") + "/view-all-doctors").then((response) => response.json()),
          fetch("http://" + sessionStorage.getItem("patientip") + "/view-all-patients").then((response) => response.json())
        ]);
        
        doctor = {}
        
          for (var obj of data[0]) 
          {
              doctor_id = obj["doctor_id"];
              doctor[doctor_id] = obj["price"];
          }

        patient = {}
      
          for (var obj of data[1]) 
          {
              patient_id = obj["patient_id"];
              patient[patient_id] = obj["name"];
          }

        var result = [data[0], data[1]]
        
        //console.log(patient);
        //console.log(doctor);
        return result;

      } 
      catch (error) 
      {
          console.log(error);
      }
    }
   */ 

  // This function here show all appointments by doctor_ID
  $(async (event) =>
  {
    var doctor_id = sessionStorage.getItem("doctor_id");
    var appointment_serviceURL = "http://" + sessionStorage.getItem("appointmentip") + "/appointments-by-doctor/" + doctor_id;

    try 
    {
      const appointment_response = await fetch(appointment_serviceURL, { method: 'GET' });
      const appointment_data = await appointment_response.json(); 

      console.log(appointment_data)

      if (!appointment_data || appointment_data["message"] == "Appointment not found.") 
      {
        console.log(data['message']);
      } 
      else
      {
        var keys = Object.entries(appointment_data)
        console.log(keys)
        for (i = 0; i < keys.length; i++)
        {
          var obj = keys[i][1]
          var patient_id = obj["patient_id"]
          var patient_serviceURL = "http://" + sessionStorage.getItem("patientip") + "/patient/" + patient_id;
          try{
              const patient_response = await fetch(patient_serviceURL, { method: 'GET' });
              const patient_data = await patient_response.json(); 
              console.log(patient_data)
              var row = 
              "<tbody><tr>" + 
                  "<td>" + obj["appointment_id"] + "</td>" + 
                  "<td>" + patient_data["name"] + "</td>" + 
                  "<td>" + obj["date"] + "</td>" + 
                  "<td>" + obj["time"] + "</td>" +
                  "<td> <a href='viewPatient.php?appointmentid=" + obj["appointment_id"] + "&patientid="+ obj["patient_id"]+"'> View Patient </a> </td>" +
              "</tr></tbody>";
            $('#apptTable').append(row);
          }catch(error){
            console.log("Error in connecting to Patient Mircoservice!");
          }
        } // end of For loop
      } // end of else
    }// end of try
    catch(error)
    {
      console.log("Error in connecting to Appointment Mircoservice!");
    }
  });

</script>
</body>

</html>