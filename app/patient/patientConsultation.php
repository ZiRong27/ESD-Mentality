
<html>
<header>
    <?php 
        include '../include/codeLinks.php';
        $accountType = "patient";
        require_once '../include/protect.php';
    ?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
    <script>
        $('#displayMessage').hide();
        $('#conTable').show();
    </script>
</header>


<body>
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>

</br>
<br/>

<div id="" class="container">
    <div class = "whitetextbig" style="color: white; font-weight: bold; font-size: 200%;">        
            My Consultation
    </div> 
    <br> 
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="conTable">
    <thead>
        <tr >
        <th scope="col"> # Consultation ID</th>
        <th scope="col"> Doctor </th>
        <th scope="col"> Date & Time </th>
        <th scope="col"> View Consultation </th>
        </tr>
    </thead>
    </table>  
    <!-- If not Data display -->
    <h1 id="displayMessage"> You do not have any Consultation </h1>

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
    var serviceURL_consultation = "http://" + sessionStorage.getItem("consultationip") + "/consultation-by-patient/" + patient_id;
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
        var serviceURL_appointment = "http://" + sessionStorage.getItem("appointmentip") + "/appointment-by-id/" + appointment_id;
        var serviceURL_doctor = "http://" + sessionStorage.getItem("doctorip") + "/view-specific-doctor-by-id/" + doctor_id;
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
    sessionStorage.setItem('patientip', "127.0.0.1:5001")
    sessionStorage.setItem('doctorip', "127.0.0.1:5002")
    sessionStorage.setItem('appointmentip', "127.0.0.1:5003")   
    sessionStorage.setItem('consultationip', "127.0.0.1:5004")   
*/
  });
</script>





</body>

</html>