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
            Consultation Information
    </div> 
    <table class="table table-striped table-light table-hover text-center" id="consultationTable">
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table> 
</div>

<script>
$(document).ready(function() 
{
  // FUNCTION: Retrieve patient name, consultationt id and doctor id and display.
  $(async (event) =>
  {
    let params = new URLSearchParams(location.search);
    var consultationt_id = params.get("consultationtid");
    var patient_name = params.get("patientname");
    var doctor_id = sessionStorage.getItem("doctor_id");
    var data = await fetchURLs(consultationt_id) 
    console.log(data);

    row = 
        "<tbody>" + 
            "<tr> <th> Name </th> <td>" + patient_name +"</td> </tr>" + 
            "<tr> <th> Consultation ID </th> <td>" + data["consultation_id"] + "</td> </tr>" + 
            "<tr> <th> Diagnosis </th> <td>" + data["diagnosis"] + "</td> </tr>" + 
            "<tr> <th> Prescription </th> <td>" + data["prescription"] + "</td> </tr>" + 
            "<tr> <th> Notes </th> <td>" + data["notes"] + "</td> </tr>" + 
        "</tbody>";
        $('#consultationTable').append(row);
  });

  // FUNCTION: Get consultation by consultation id and return data from database
  async function fetchURLs(consultationt_id) 
    {
    // var serviceURL_consultation = "http://127.0.0.1:5004/consultation-by-consultationid/" + consultationt_id;
    var serviceURL_consultation  = "http://" + sessionStorage.getItem("consultationip") + "/consultation-by-consultationid/" + consultationt_id;
    try 
    {
    // retrieve consultation data by consultationt id
        const response_consultation = await fetch(serviceURL_consultation, { method: 'GET' });
        const data_consultation = await response_consultation.json(); 
        console.log(data_consultation)
        if(!data_consultation || data_consultation["message"] == "consultation not found.")
        {
            console.log("consultation not found")
        }
        else
        {
            return data_consultation
        }
    } 
    catch (error) 
    {
      console.log(error);
    }
  }

});
</script>

</body>