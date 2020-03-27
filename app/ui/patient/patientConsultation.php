
<html>
<header>
    <?php 
        include '../include/codeLinks.php';
        $accountType = "patient";
        require_once '../include/protect.php';
    ?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
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
$( document ).ready(function() 
{
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
            $('#displayMessage').hide();
            $('#conTable').show();
            var keys = Object.entries(data_consultation)
            for (var ele in keys)
            {
                var obj = data_consultation[ele];
                console.log(obj);
                var doctorName = await fetchData(obj["doctor_id"]);
                //var data = await fetchData(obj["doctor_id"], obj["appointment_id"]);
                //var doctorName = data[0];
                //var dateTime = data[1];
                console.log(doctorName);
                var row =
                "<tbody><tr>" + 
                    "<td>" + obj["consultation_id"] + "</td>" + 
                    "<td>" + doctorName + "</td>" + 
                    "<td>" + "Consulataion needs to capture data and time :( " + "</td>" + 
                    "<td> <a href='viewConsultation.php?consultationid=" + obj["consultation_id"] + "&doctorname="+ doctorName +"'> View Consultation </a> </td>" +
                "</tr></tbody>";
                $('#conTable').append(row);
            } // End of for loop
        } // End of else
    } // end of try 
    catch(error)
    {
      console.log("Error in connecting to Consultation Mircoservice!");
    }
}); // End of Function - Part A

// Function: Get appointment and doctor information - Part B
    async function fetchData(doctor_id,appointment_id) 
    {
        //var data = [];
        //var serviceURL_appointment = "http://" + sessionStorage.getItem("appointmentip") + "/appointment-by-id/" + appointment_id;
        var serviceURL_doctor = "http://" + sessionStorage.getItem("doctorip") + "/view-specific-doctor-by-id/" + doctor_id;

        //console.log(serviceURL_appointment)
        console.log(serviceURL_doctor)

        try 
        {
            // retrieve appointment by appointment ID
            const response_doctor = await fetch(serviceURL_doctor, { method: 'GET' });
            const data_doctor = await response_doctor.json(); 
            const doctorName = data_doctor["name"];
            console.log(doctorName);

            //const response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
            //const data_appointment = await response_appointment.json(); 
            //console.log(data_appointment)
            //const datetime = data_appointment["date"];
            //console.log(datetime);

            return doctorName;
        }
        catch(error)
        {
            console.log("Error in connecting to Mircoservice!");
        }
    } // End of function -Part B

}); // End of Document ready
</script>





</body>

</html>