<html>
<header>
    <?php 
        include '../include/codeLinks.php';
        $accountType = "patient";
        require_once '../include/protect.php';
    ?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body  style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>

</br>
<br/>

<div class="row d-flex justify-content-center">
<div id="main-container" class="container">
    <div class = "my-2">        
        <h1>My Appointment History</h1>
        <hr>
    </div> 
    <div class ="index-errormsg"></div>
 
    <table class="table table-striped table-light table-hover text-center" id="apptHisTable">
    <thead>
        <tr>
            <th scope="col"> Appointment ID </th>
            <th scope="col"> Doctor </th>
            <th scope="col"> Date </th>
            <th scope="col"> Time </th>
            <!-- <th scope="col"> Paid Amount </th> -->
        </tr>
    </thead>
    </table>  
    <!-- If not Data display -->
    <br>
    <br>
    <h1 id="displayMessage" class="text-center" style="font-size:20px">No appointment history available </h1>
    <br><br><br><br><br>

</div>

<script>   

$(() => {
    $("#history").addClass("active");
});

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
    var serviceURL_appointment = "http://" + appointmentip + "/get-all-appointment-history/" + patient_id;
    try 
    {
    // retrieve consultation data by patient
      const response_appointment = await fetch(serviceURL_appointment, { method: 'GET' });
      const data_appointment = await response_appointment.json(); 
      console.log(data_appointment)

    // If retrieve data failed, result to no data
    if (!data_appointment || data_appointment["message"] == "history appointment by patient id not found.") 
    {
        $('#displayMessage').show();
    }
    else
    {
            if (data_appointment.length == 0) 
            {
                $('#displayMessage').show();
            } 
            else
            {
                $('#displayMessage').hide();
                var keys = Object.entries(data_appointment)
                for (var ele in keys)
                {
                    var obj = data_appointment[ele];
                    console.log(obj);
                    var doctorName = await fetchData(obj["doctor_id"]);
                    console.log(doctorName);
                    var row =
                    "<tbody><tr>" + 
                        "<td>" + obj["appointment_id"] + "</td>" + 
                        "<td>" + doctorName + "</td>" + 
                        "<td>" + obj["date"] + "</td>" + 
                        "<td>" + obj["time"] + "</td>" + 
                        //"<td>" + obj["payment_id"] +
                    "</tr></tbody>";
                    $('#apptHisTable').append(row);
                } // End of for loop
            } // End of else
        }// End of esle
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
        //var serviceURL_appointment = "http://" + appointmentip + "/appointment-by-id/" + appointment_id;
        var serviceURL_doctor = "http://" + doctorip + "/view-specific-doctor-by-id/" + doctor_id;

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