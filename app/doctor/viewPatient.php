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

    <form>

    <table class="table table-striped table-light table-hover text-center" id="appointmentTable">
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>  

    
      <div class="form-group">
        <label for="diagnosisInformation"> Diagnosis </label>
        <textarea class="form-control" id="diagnosisInformation" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label for="prescriptionInformation"> Prescription </label>
        <textarea class="form-control" id="prescriptionInformation" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label for="notesInformation"> Notes </label>
        <textarea class="form-control" id="notesInformation" rows="5"></textarea>
      </div>

      <button type="submit" class="btn btn-primary" id="registerAppointment"> Submit </button>

    </form> 

</div>

<script>
$(document).ready(function() 
{
  // FUNCTION: Retrieve patient and appointment details and display.
  $(async (event) =>
  {
    let params = new URLSearchParams(location.search);
    var appointment_id = params.get("appointmentid");
    var patient_id = params.get("patientid");
    var doctor_id = sessionStorage.getItem("doctor_id");
    var data = await fetchURLs(appointment_id, patient_id) 
    console.log(data);
    
    var patient_information = data[0];
    var appointment_information = data[1];

    row = 
        "<tbody>" + 
            "<tr> <th> Name </th> <td>" + patient_information["salutation"] + ". "+ patient_information["name"] +"</td> </tr>" + 
            "<tr> <th> Username </th> <td>" + patient_information["username"] + "</td> </tr>" + 
            "<tr> <th> Date & Time </th> <td>" + appointment_information["date"] + "," + appointment_information["time"] + "</td> </tr>" + 
        "</tbody>";
        $('#appointmentTable').append(row);
  });


  // FUNCTION: Get appointment and Patient Data from database
  async function fetchURLs(appointment_id, patient_id) 
    {
        var patientURL = "http://127.0.0.1:5001/patient/";
        //var patientURL = "http://" + sessionStorage.getItem("patientip") + "/patient/";
        patientURL = patientURL + patient_id
        
        var appointmentURL = "http://127.0.0.1:5003/appointment-by-id/"; 
        //var appointmentURL = "http://" + sessionStorage.getItem("appointmentip") + "/appointment-by-id/"; 
        appointmentURL = appointmentURL + appointment_id
        console.log(appointmentURL);

        var consultationURL = "http://127.0.0.1:5004/consultation";
        console.log(consultationURL);
    try 
    {
      // Promise.all() lets us coalesce multiple promises into a single super-promise
      var data = await Promise.all
      ([
        fetch(patientURL).then((response) => response.json()),
        fetch(appointmentURL).then((response) => response.json()),
        fetch(consultationURL).then((response) => response.json())
      ]);
      console.log(data);
      console.log(data[2]);
      return data
    } 
    catch (error) 
    {
      console.log(error);
    }
  }

    // FUNCTION: Get appointment and Patient Data from database to create consultation - Part A 
    $("#registerAppointment").click(async(event) =>
    {
        event.preventDefault(); 
        let params = new URLSearchParams(location.search);
        var appointment_id = params.get("appointmentid");
        var patient_id = params.get("patientid");
        var doctor_id = sessionStorage.getItem("doctor_id");
        var data = await fetchURLs(appointment_id, patient_id); 
        console.log(data);
    
        var patient_information = data[0];
        var appointment_information = data[1];
        
        // Create consultation ID 
        var consultation_length = data[2].length;
        consultation_length += 1;

        var appointment_id = appointment_information["appointment_id"];
        var doctor_id = doctor_id;
        var patient_id = patient_information["patient_id"];
        var diagnosis_information = $("#diagnosisInformation").val(); 
        var prescription_information = $("#prescriptionInformation").val();
        var notes_information = $("#notesInformation").val();
                
        var serviceURL = "http://127.0.0.1:5004/convert-to-consultation";
        var requestBody = 
        {
            consultation_id: consultation_length,
            appointment_id: appointment_id,
            doctor_id: doctor_id,
            patient_id: patient_id,
            diagnosis: diagnosis_information,
            prescription: prescription_information,
            notes: notes_information
        }   
        postData(serviceURL, requestBody) 
        });
    // FUNCTION: create consultation - Part B
    async function postData(serviceURL, requestBody) 
    {
        var requestParam = 
        {
            method: 'POST',                
            headers: { "content-type": "application/json;" },
            body: JSON.stringify(requestBody)
        }
        try 
        {
            const response = await fetch(serviceURL, requestParam);
            data = await response.json();               
            console.log("consultation created!:" + data);
            window.location.replace("doctorConsultation.php");
        }       
        catch (error) 
        {
            console.error(error);
        }
    }

    $(async(event) =>
    {
      var doctor_id = sessionStorage.getItem("doctor_id");
      var serviceURL = "http://127.0.0.1:5004/consultation-by-doctor/" + doctor_id;
      try 
      {
        console.log(serviceURL)
        const response = await fetch(serviceURL, { method: 'GET' });
        const doctorConsultation = await response.json();
        if (!doctorConsultation || doctorConsultation.message == "Error retriving consultation.") 
        {
          console.log("error retrieving ids");
        }
        else
        {
          console.log(doctorConsultation);
        }
      }
      catch(error)
      {
        console.log("Error in connecting to Mircoservice!");
      }
    });
});
</script>

</body>