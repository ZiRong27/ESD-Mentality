<?php
require_once '../include/common.php';
require_once '../include/protect.php';
$accountType = "doctor";
?>
<html>
<head>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "include/stylesheet.css" />
</head>
<body>
<!-- Import navigation bar -->
<?php include '../include/doctorNavbar.php';?>

<div id="main-container" class="container">

    <div class = "whitetextbig" style="color: white; font-weight: bold; font-size: 200%;" id="doctorName">        
            Patient Information
    </div> 

    <table class="table table-striped table-light table-hover text-center" id="doctorsTable">
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>  

</div>

<script>
    // This function here show all appointments by doctor_ID
  $(async (event) =>
  {

    let params = new URLSearchParams(location.search);
    var appointment_id = params.get("appointmentid");
    var patient_id = params.get("patientid");
    console.log(appointment_id);
    console.log(patient_id);
    var doctor_id = sessionStorage.getItem("doctor_id");

    fetchURLs(appointment_id, patient_id) 
    /*
    try 
    {
      const response = await fetch(serviceURL, { method: 'GET' });
      const data = await response.json(); 
      console.log(data)

      if (!data || data.message == "Appointment(s) not found.") 
      {
        console.log(data['message']);
      } 
      else
      {
        console.log(Object.values(data))
        for (i = 0; i < data.length; i++) 
        { 
          if(data[i]["doctor_id"] == doctor_id)
          {
            console.log("hello")
            row = 
              "<tbody><tr>" + 
                  "<td>" + data[i]["appointment_id"] + "</td>" + 
                  "<td>" + data[i]["patient_id"] + "</td>" + 
                  "<td>" + data[i]["date"] + "</td>" + 
                  "<td>" + data[i]["time"] + "</td>" +
                  "<td> <a href='viewPatient.php?appointmentid=" + data[i]["appointment_id"] + "&patientid="+ data[i]["patient_id"]+"'> View Patient </a> </td>" +
              "</tr></tbody>";
            $('#apptTable').append(row);
          }
          
        }
      }
    }
    catch(error)
    {
      console.log("Error in connecting to Mircoservice!");
    }
    */
  });


  async function fetchURLs(appointment_id, patient_id) 
    {
        var patientURL = "http://127.0.0.1:5001/patient/";
        patientURL = patientURL + patient_id

        var appointmentURL = "http://127.0.0.1:5003/appointment/"; 
        appointmentURL = appointmentURL + appointment_id
        console.log(appointmentURL);

    try 
    {
      // Promise.all() lets us coalesce multiple promises into a single super-promise
      var data = await Promise.all
      ([
        fetch(patientURL).then((response) => response.json()),
        fetch(appointmentURL).then((response) => response.json())
      ]);
      var patient = data[0]
      var appointment = data[1]

      /*
        for (var obj of data[0]) 
        {
            doctor_id = obj["doctor_id"];
            doctor[doctor_id] = obj["price"];
        }

      patient = {}
     
        for (var obj of data[1]) {
            patient_id = obj["patient_id"];
            patient[patient_id] = obj["name"];
        }
        */
        console.log(data);
      console.log(patient);
      console.log(appointment);
      /*return doctor, patient;*/

    } 
    catch (error) 
    {
      console.log(error);
    }
  }
</script>

</body>