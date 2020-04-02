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
            <h1>My Appointments:</h1>
            <hr> 
        <div class ="index-errormsg"></div>
        <br>  
        <table class="table table-striped table-light table-hover text-center" id="apptTable" style="border:3px solid #f0f0f0;">
          <thead>
              <tr>
                <th scope="col">Appointment ID</th>
                <th scope="col">Patient</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">View profile</th>
              </tr>
          </thead>
        </table> 
        <br><br>
        <h1 id="displayMessage" class="text-center" style="font-size:20px">No Appointments available</h1> 
  </div>
</div>
</div>
<script>    

    $(() => {
      $("#appointment").addClass("active");
    });
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }

/*
    async function fetchURLs() {
    try {
      // Promise.all() lets us coalesce multiple promises into a single super-promise
      var data = await Promise.all([
        //fetch("http://"  + doctorip + "  /view-all-doctors").then((response) => response.json()),
        //fetch("http://" + patientip + "/view-all-patients").then((response) => response.json())
        fetch("http://" + doctorip + "/view-all-doctors").then((response) => response.json()),
        fetch("http://" + patientip + "/view-all-patients").then((response) => response.json())
      ]);
      doctor = {}
      
        for (var obj of data[0]) {
            doctor_id = obj["doctor_id"];
            doctor[doctor_id] = obj["price"];
        }

      patient = {}
     
        for (var obj of data[1]) {
            patient_id = obj["patient_id"];
            patient[patient_id] = obj["name"];
        }
      
      console.log(patient);
      console.log(doctor);
      return doctor, patient;

    } catch (error) {
      console.log(error);
    }
  }
  */

  // This function here show all appointments by doctor_ID
  $(async (event) =>
  {
    var doctor_id = sessionStorage.getItem("doctor_id");
    var serviceURL = "http://" + appointmentip + "/appointments-by-doctor/" + doctor_id;
    //var serviceURL = "http://" + appointmentip + "/view-all-appointments";

    try 
    {
      const response = await fetch(serviceURL, { method: 'GET' });
      const data = await response.json(); 
      console.log(data)

      if (!data || data.length == 0) 
      {
        $("#displayMessage").show();
      } 
      else
      {
        $("#displayMessage").hide();
        for (var ele in data)
        {
          var obj = data[ele]
          var patientName = await fetchData(obj["patient_id"])
          row = 
              "<tbody><tr>" + 
                  "<td>" + obj["appointment_id"] + "</td>" + 
                  "<td>" + patientName + "</td>" + 
                  "<td>" + obj["date"] + "</td>" + 
                  "<td>" + obj["time"] + "</td>" +
                  "<td> <a href='viewPatient.php?appointmentid=" + obj["appointment_id"] + "&patientid="+ obj["patient_id"]+"'> View Patient </a> </td>" +
              "</tr></tbody>";
            $('#apptTable').append(row);
        }
      }
    }
    catch(error)
    {
      console.log("Error in connecting to Mircoservice!");
    }

// Function: Get appointment and doctor information - Part B
async function fetchData(patient_id) 
    {
        var serviceURL_Patient = "http://" + patientip + "/patient/" + patient_id;
        console.log(serviceURL_Patient)
        try 
        {
            // retrieve appointment by appointment ID
            const response_patient = await fetch(serviceURL_Patient, { method: 'GET' });
            const data_patient = await response_patient.json(); 
            var patientName = data_patient["name"];
            console.log(patientName);
            return patientName;
        }
        catch(error)
        {
            console.log("Error in connecting to Mircoservice!");
        }
    } // End of function -Part B





  });

</script>
</body>

</html>