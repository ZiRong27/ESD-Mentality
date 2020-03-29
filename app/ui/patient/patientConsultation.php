
<html>
<header>
    <?php 
        include '../include/codeLinks.php';
        $accountType = "patient";
        require_once '../include/protect.php';
    ?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css"/>
    <script>
        $('#displayMessage').hide();
        $('#conTable').show();
    </script>
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>

</br>
<br/>

<div id="" class="container">
    <div class="my-2">        
        <h1> My Consultation(s) </h1>
        <hr>
    </div> 

    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="conTable" style="border: 1px solid #e0e0e0;">
    <thead>
        <tr >
        <th scope="col"> Consultation ID</th>
        <th scope="col"> Doctor </th>
        <th scope="col"> Date & Time </th>
        <th scope="col"> View Consultation </th>
        </tr>
    </thead>
    </table>  
    <!-- If not Data display -->
    <hr>
    <br><br>
    <h1 id="displayMessage" class="text-center" style="font-size:20px"> You do not have any consultations </h1>
    <br><br><br><br><br>

</div>

<script>    
// Helper function to display error message
function showError(message) 
{
    console.log('Error logged')
    // Display an error on top of the table
    $('.index-errormsg').html(message)
}
// Function: Get all consultation by patient_ID - Part A
  $(async (event) =>
  {
    var patient_id = sessionStorage.getItem("patient_id");
    var serviceURL_consultation = "http://" + consultationip + "/consultation-by-patient/" + patient_id;
    try 
    {
    // retrieve consultation data by patient
      const response_consultation = await fetch(serviceURL_consultation, { method: 'GET' });
      const data_consultation = await response_consultation.json(); 
      console.log(data_consultation)

    // If retrieve data failed, result to no data
      if (!data_consultation) 
      {
        $('#displayMessage').show();
        $('#conTable').hide();
      } 
      // else display
      else
      {
          var appointment_id = data_consultation["appointment_id"];
          var doctor_id = data_consultation["doctor_id"];
          var data = fetchData(appointment_id, doctor_id);
      } 
    }
    catch(error)
    {
      console.log("Error in connecting to Mircoservice!");
    }

// Function: Get appointment and doctor information - Part B
    async function fetchData(appointment_id, doctor_id) 
    {
        var data = [];
        var serviceURL_appointment = "http://" + appointmentip + "/appointment-by-id/" + appointment_id;
        var serviceURL_doctor = "http://" + doctorip + "/view-specific-doctor-by-id/" + doctor_id;
        try 
        {
            // retrieve appointment by appointment ID
            const response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
            const data_appointment = await response_appointment.json(); 
            console.log(data_appointment)

            // retrieve appointment by appointment ID
            const response_doctor = await fetch(serviceURL_doctorip, { method: 'GET' });
            const data_doctor = await response_doctor.json(); 
            console.log(data_doctor)
        }
        catch(error)
        {
            console.log("Error in connecting to Mircoservice!");
        }
    }

/*
    sessionStorage.setItem('patientip', "" + patientip + " ")
    sessionStorage.setItem('doctorip', ""  + doctorip + "  ")
    sessionStorage.setItem('appointmentip', "" + appointmentip + " ")   
    sessionStorage.setItem('consultationip', "" + consultationip + " ")   
*/
  });
</script>





</body>

</html>