
<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';
?>

<html>
<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        /*.table-bordered th, .table-bordered td { border: 2px solid #ddd!important }*/
    </style>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "include/stylesheet.css" />
</header>


<body>
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>


</br>
<div id="main-container" class="container">
    <div class = "whitetextbig" style="color: white; font-weight: bold; font-size: 200%;">        
            Transaction History
    </div> 
    <br> 
    <div class ="index-errormsg"></div>
    <br>  
    <table class="table table-striped table-light table-hover text-center" id="paymentHistoryTable">
    <thead>
        <tr >
        <th scope="col">#</th>
        <th scope="col">Timestamp</th>
        <th scope="col">Amount</th>
        </tr>
    </thead>
    </table>   
</div>

<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error on top of the table
        $('.index-errormsg').html(message)
    }

    $(async() => { 
        var patient_id = sessionStorage.getItem("patient_id");
        $('#patient_id').val(patient_id); 

        var serviceURL = "http://127.0.0.1:5005/transactionhistory-by-id/" + patient_id;

        try {
                const response =
                 await fetch(
                   serviceURL, { method: 'GET' }
                );
                const data = await response.json();

                if (data['message']) {
                    showError(data['message'])
                } else {
                    // For loop to display all transaction history rows
                    $('#paymentHistoryTable').append("<tbody>");
                    var history = data["payment_history"]
      
                    for (i = 0; i < history.length; i++) { 
                        Row =
                        "<tr><th scope='row'>" + history[i].payment_id + "</th>" +
                        "<td>" + history[i].date+ "</td>" +
                        "<td>" + history[i].amount / 100 + "</td>" +
                        "<td></td></tr>";

                        $('#paymentHistoryTable').append(Row);
                    } 
                    
                    //Add the t body
                    $('#paymentHistoryTable').append("</tbody>");              
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError('There is a problem retrieving payments data, please try again later. Tip: Did you forget to run payment.py? :)<br />' + error);
               
            } 
    });
    

</script>

</body>

</html>