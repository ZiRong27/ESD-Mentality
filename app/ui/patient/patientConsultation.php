
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

<div id="main-container" class="container" style="border:1px solid #696969; border-radius:20px; padding:10px; box-shadow: 2px 3px #989898; background:white;">
    <div class = "whitetextbig" style="color: black; font-weight: bold; font-size: 200%;">        
            My Consultation
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
    if (!data_consultation || data_consultation["message"] == "consultation by patient id not found.") 
    {
        console.log("error retriving");
    }
    else
    {
            if (!data_consultation) 
            {
                $('#displayMessage').show();
                $('#conTable').hide();
            } 
            else
            {
                $('#displayMessage').hide();
                $('#conTable').show();
                var keys = Object.entries(data_consultation)
                for (var ele in keys)
                {
                    var obj = data_consultation[ele];
                    console.log(obj);
                    //var doctorName = await fetchData(obj["doctor_id"]);
                    var data = await fetchData(obj["doctor_id"],obj["appointment_id"]);
                    var doctorName = data[0];
                    var dateTime = data[1];
                    console.log(doctorName);
                    var row =
                    "<tbody><tr>" + 
                        "<td>" + obj["consultation_id"] + "</td>" + 
                        "<td>" + doctorName + "</td>" + 
                        "<td>" + dateTime + "</td>" + 
                        "<td> <a href='viewConsultation.php?consultationid=" + obj["consultation_id"] + "&doctorname="+ doctorName +"'> View Consultation </a> </td>" +
                    "</tr></tbody>";
                    $('#conTable').append(row);
                } // End of for loop
            } // End of else
        }// End of esle
    } // end of try 
    catch(error)
    {
      console.log("Error in connecting to Mircoservice!");
    }

// Function: Get appointment and doctor information - Part B
    async function fetchData(doctor_id, appointment_id) 
    {
        var data = [];
        var serviceURL_appointment = "http://" + appointmentip + "/get-appointment-id-history/" + appointment_id;
        var serviceURL_doctor = "http://" + doctorip + "/view-specific-doctor-by-id/" + doctor_id;
        try 
        {
            // retrieve appointment by appointment ID
            const response_doctor = await fetch(serviceURL_doctor, { method: 'GET' });
            const data_doctor = await response_doctor.json(); 
            const doctorName = data_doctor["name"];
            console.log(doctorName);

            const response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
            const data_appointment = await response_appointment.json(); 
            console.log(data_appointment);
            console.log(data_appointment["date"]);
            console.log(data_appointment["time"]);
            var datetime = "Date: " + data_appointment["date"] + " - " + "Time: " + data_appointment["time"];
            console.log(datetime);

            data = [doctorName, datetime];
            return data;
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