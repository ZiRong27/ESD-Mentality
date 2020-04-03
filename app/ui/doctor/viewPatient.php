<?php
require_once '../include/common.php';
require_once '../include/protect.php';
$accountType = "doctor";
?>
<html>

<head>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "include/stylesheet.css"/>
</head>

<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/doctorNavbar.php';?>

<div id="main-container" class="container">

    <div class="my-2" id="doctorName">        
            <br>
            <h2>Patient Information:</h2>
    </div> 

    <form>

    <table class="table table-striped table-light table-hover text-center" id="appointmentTable">
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>  

    <hr>
    <div style="font-weight:bold;">Allergies:</div>
    <div style="border:1px solid #e8e8e8; border-radius:3px;">
    <div class="text-center" id="viewallergiesTable" style="background-color:white;">
          <thead>
          </thead>
          <tbody>
          </tbody>
    </div> 
    </div>
    
    <br>

    <div style="font-weight:bold;">Medical History:</div>
    <div style="border:1px solid #e8e8e8; border-radius:3px;">
    <div class="text-center" id="viewMedicalHistoryTable" style="background-color:white;">
          <thead>
          </thead>
          <tbody>
          </tbody>
    </div> 
    </div>

    <br><hr>

      <div class="form-group">
        <label for="diagnosisInformation" style="font-weight: bold;"> Diagnosis: </label>
        <textarea class="form-control" id="diagnosisInformation" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label for="prescriptionInformation" style="font-weight: bold;"> Prescription: </label>
        <textarea class="form-control" id="prescriptionInformation" rows="2"></textarea>
      </div>

      <div class="form-group">
        <label for="notesInformation" style="font-weight: bold;"> Notes: </label>
        <textarea class="form-control" id="notesInformation" rows="5"></textarea>
      </div>
      
      <div class="text-right">
      <button type="submit" class="btn btn-primary" id="registerAppointment"> Submit </button>
      </div>

</div>

<script>
$(() => {
  $("#appointment").addClass("active");
});

$(document).ready(function() 
{
  // FUNCTION: Retrieve patient and appointment details and display.
  $(async (event) =>
  {
    let params = new URLSearchParams(location.search);
    var appointment_id = params.get("appointmentid");
    var patient_id = params.get("patientid");
    var doctor_id = sessionStorage.getItem("doctor_id");
    console.log("Appointment id " + appointment_id)
    console.log("Patient id " + patient_id)
    var data = await fetchURLs(appointment_id, patient_id) 
    console.log(data);
    
    var patient_information = data[0];
    var appointment_information = data[1];

    row = 
        "<tbody>" + 
            "<tr> <th> Name </th> <td>" + patient_information["salutation"] + ". "+ patient_information["name"] +"</td> </tr>" + 
            "<tr> <th> Date & Time </th> <td>" + appointment_information["date"] + "," + appointment_information["time"] + "</td> </tr>" + 
        "</tbody>";
        $('#appointmentTable').append(row);

    // Display patients Allergies
    var data = await fetchpatientallergiesURLs(patient_id)  
    console.log("allegise" + data);
    $('#viewallergiesTable').append(data["allergies"]);


    // Display patients Medical History
    var data = await fetchpatientmedicalhistoryURLs(patient_id)  
    console.log("medical History" + data);
    $('#viewMedicalHistoryTable').append(data["medical_history"]);
  });


  // FUNCTION: Get appointment and Patient Data from database
  async function fetchURLs(appointment_id, patient_id) 
    {
        var patientURL = "http://" + patientip + "/patient/";
        patientURL = patientURL + patient_id
        
        var appointmentURL = "http://" + appointmentip + "/appointment-by-id/"; 
        appointmentURL = appointmentURL + appointment_id
        console.log(appointmentURL);

        var consultationURL  = "http://" + consultationip + "/consultation";
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
      console.log("data result:" + data);
      console.log("data[2] result:" +  data[2]);
      return data
    } 
    catch (error) 
    {
      console.log(error);
    }
  }
  // End of Function 

  // FUNCTION: Display Patient's allergises from database
  async function fetchpatientallergiesURLs(patient_id) 
  {
    var patientallergiesURL = "http://" + patientip + "/allergies/";
    patientallergiesURL = patientallergiesURL + patient_id;
    try 
    {
      const response = await fetch(patientallergiesURL, { method: 'GET' });
      const data = await response.json(); 
      console.log(data)

      if (!data || data.message == "allergies data missing error.") 
      {
        console.log(data['message']);
      } 
      else
      {
        return data
      }
    }
    catch (error) 
    {
      console.error(error);
    }
  };
  // End of Function 


// FUNCTION: Display Patient Medical History
async function fetchpatientmedicalhistoryURLs(patient_id) 
  {
    var medicalhistoryURL = "http://" + patientip + "/history/";
    medicalhistoryURL = medicalhistoryURL + patient_id;
    try 
    {
      const response = await fetch(medicalhistoryURL, { method: 'GET' });
      const data = await response.json(); 
      console.log(data)

      if (!data || data.message == "patient history data missing.") 
      {
        console.log(data['message']);
      } 
      else
      {
        return data
      }
    }
    catch (error) 
    {
      console.error(error);
    }
  };
  // End of Function 

    // FUNCTION: Get appointment and Patient Data from database to create consultation - Part A 
    $("#registerAppointment").click(async(event) =>
    {
        event.preventDefault(); 
        let params = new URLSearchParams(location.search);
        var appointment_id = params.get("appointmentid");
        console.log(appointment_id);
        var patient_id = params.get("patientid");
        var doctor_id = sessionStorage.getItem("doctor_id");
        var data = await fetchURLs(appointment_id, patient_id); 
        console.log(data);
    
        var patient_information = data[0];
        var appointment_information = data[1];
        
        // Create consultation ID 
        //var consultation_length = data[2].length;
        //consultation_length += 1;

        var appointment_id = appointment_information["appointment_id"];
        var date = appointment_information["date"];
        console.log("Test" + date);
        var time = appointment_information["time"];
        console.log("Test" + time);
        var payment_id = appointment_information["payment_id"];
        var doctor_id = doctor_id;
        var patient_id = patient_information["patient_id"];
        var diagnosis_information = $("#diagnosisInformation").val(); 
        var prescription_information = $("#prescriptionInformation").val();
        var notes_information = $("#notesInformation").val();

        // Consultation -> create consultation       
        var serviceURL_consultation  = "http://" + consultationip + "/convert-to-consultation";
        var requestBody_consultation = 
        {
            consultation_id: appointment_id,
            appointment_id: appointment_id,
            doctor_id: doctor_id,
            patient_id: patient_id,
            diagnosis: diagnosis_information,
            prescription: prescription_information,
            notes: notes_information
        }   
        console.log(requestBody_consultation);
        // Appointment History -> create appointment history 
        var serviceURL_appointment  = "http://" + appointmentip + "/appointment-history";
        var requestBody_appointment = 
        {
            appointment_id: appointment_id,
            doctor_id: doctor_id,
            patient_id: patient_id,
            date: date,
            time: time,
            payment_id: payment_id
        }  
        console.log(requestBody_appointment);
        
        await postDataConsultation(serviceURL_consultation, requestBody_consultation) 
        await postDataAppointmentHistory(serviceURL_appointment, requestBody_appointment)
        await postDataDeleteHistory(appointment_id)
        window.location.replace("doctorConsultation.php");
        /*
        if (r1 == true && r2 == true && r3 == true)
        {
            window.location.replace("doctorConsultation.php");
        }
        else
        {
            console.log("cannot go next page!")
        }
        */

        /* triple checklist 
        var r3 = await postDataDeleteHistory(appointment_id)
        if(r3 == true) // if true, create the consultation
        {
          var r1 = await postDataConsultation(serviceURL_consultation, requestBody_consultation)
          if(r1 == true) // if true, create appointment history
          {
            var r2 = await postDataAppointmentHistory(serviceURL_appointment, requestBody_appointment)
            if(r2 == true) // if true, go to conultation page
            {
              window.location.replace("doctorConsultation.php");
            }
          }
        }
      // End of if statement.
      */
    });
    // FUNCTION: create consultation - Part B
    async function postDataConsultation(serviceURL_consultation, requestBody_consultation) 
    {
        var requestParam_consultation = 
        {
            method: 'POST',                
            headers: { "content-type": "application/json;" },
            body: JSON.stringify(requestBody_consultation)
        }
        try 
        {
            const response_consultation = await fetch(serviceURL_consultation, requestParam_consultation);
            data_consultation = await response_consultation.json();               
            console.log("consultation created!:" + data_consultation);
            return true;
        }       
        catch (error) 
        {
            console.log("Unable to connect to consultation");
            //return false;
        }
    }
    // End of Function 
    // FUNCTION: create appointment History - Part B
    async function postDataAppointmentHistory(serviceURL_appointment, requestBody_appointment) 
    {
      console.log("this is the object " + requestBody_appointment);
      console.log(serviceURL_appointment);
      var requestParam_appointment = 
      {
            method: 'POST',                
            headers: { "content-type": "application/json;" },
            body: JSON.stringify(requestBody_appointment)
      }
      try
      {
            const response_appointment = await fetch(serviceURL_appointment, requestParam_appointment);
            data_appointment = await response_appointment.json();               
            console.log("history created!:" + data_appointment);
            //return true;
      }
      catch(error)
      {
            //return false;
      }
    }
    // End of Function 
    // FUNCTION: delete from appointment - Part B
    async function postDataDeleteHistory(appointment_id) 
    {
      var requestBody_deleteAppointment = 
      {
        appointment_id: appointment_id,
      }  

      var requestParam_deleteAppointment = 
      {
        method: 'POST',                
        headers: { "content-type": "application/json;" },
        body: JSON.stringify(requestBody_deleteAppointment)
      }
      var appointment_serviceURL = "http://" + appointmentip + "/delete-appointment";
      console.log(requestParam_deleteAppointment)
      try
      {
        const response_deleteAppointment = await fetch(appointment_serviceURL, requestParam_deleteAppointment);
        data_deleteAppointment = await response_deleteAppointment.json();               
        console.log("appointment delete!:" + data_deleteAppointment);
        //return true;
      }
      catch(error)
      {
        //return false;
      }
      //var appointment_serviceURL = "http://" + appointmentip + "/delete-appointment/" + appointment_id;

      /*
      console.log(appointment_serviceURL);
      try
      {
          const appointment_response = await fetch(appointment_serviceURL, { method: 'POST' });
          const appointment_data = await appointment_response.json(); 
          // window.location.replace("doctorConsultation.php");
          //return true;
      }
      catch(error)
      {
            console.log("Unable to connect to appointment");
            // window.location.replace("doctorConsultation.php"); // This is a temporary solution
            return false;
      }
      */
    }
    // End of Function 
});
</script>

</body>