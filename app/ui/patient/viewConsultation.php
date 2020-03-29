
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

<br><br><br>
<div id="main-container" class="container" style="border:1px solid #696969; border-radius:20px; padding:10px; box-shadow: 2px 3px #989898; background:white;">
    <div class = "whitetextbig" style="color: black; font-weight: bold; font-size: 150%;" id="">        
            Consultation Information: 
    </div> 
        <br>
        <table class="table table-striped table-light table-hover text-center" id="consultationTable">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table> 
    </div> 
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
    let params = new URLSearchParams(location.search);
    var consultationt_id = params.get("consultationid");
    var doctor_name = params.get("doctorname");
    var serviceURL_consultation = "http://" + consultationip + "/consultation-by-consultationid/" + consultationt_id;

    try 
    {
    // retrieve consultation data by patient
      const response_consultation = await fetch(serviceURL_consultation, { method: 'GET' });
      const data_consultation = await response_consultation.json(); 
      console.log(data_consultation)

    // If retrieve data failed, result to no data
      if (!data_consultation) 
      {
        console.log("error retrieving consultation");
      } 
      // else display
      else
      {
        console.log(data_consultation);
        console.log(doctor_name);
        row = 
        "<tbody>" + 
            "<tr> <th> Doctor </th> <td>" + doctor_name +"</td> </tr>" + 
            "<tr> <th> Diagnosis </th> <td>" + data_consultation["diagnosis"] + "</td> </tr>" + 
            "<tr> <th> Notes </th> <td>" + data_consultation["notes"] + "</td> </tr>" + 
            "<tr> <th> Prescription </th> <td>" + data_consultation["prescription"] + "</td> </tr>" + 
        "</tbody>";
        $('#consultationTable').append(row);
      } 
    }
    catch(error)
    {
      console.log("Error in connecting to Consultation Mircoservice!");
    }
  });

}); // End of document ready 
</script>

</body>

</html>