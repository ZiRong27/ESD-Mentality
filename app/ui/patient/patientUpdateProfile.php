<?php
require_once '../include/common.php';

$accountType = "patient";
require_once '../include/protect.php';
?>
<html>
<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>

<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>
</br></br>

<br/>

<!-- Page Content -->
<div class="container">
  <!-- Page Heading -->
  <h1 class="my-4">Edit Profile</h1>
  <h5 class="my-4">Update your phone number or password. <h5>
  <hr>
  <br>

	<div class="row">

    <!-- edit form column -->
    <div class="col-md-9 personal-info">
      <div id="add_alert">
      </div>        
      <form id="loginForm" class="form-horizontal" role="form">
        <div class="form-group">
          <label class="col-lg-3 control-label">Salutation:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="salutation" required readonly>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Name:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="name" required readonly>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Date Of Birth:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="dob" required readonly> 
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Phone:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="phone" required> 
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Gender:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="gender" required readonly> 
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Username:</label>
          <div class="col-lg-8">
            <input class="form-control" type="text" id="username" required> 
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Old Password:</label>
          <div class="col-lg-8">
            <input class="form-control" type="password" id="password" required>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">New Password:</label>
          <div class="col-lg-8">
            <input class="form-control" type="password" id="newpassword">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-3 control-label">Confirm Password:</label>
          <div class="col-lg-8">
            <input class="form-control" type="password" id="newpasswordconfirm"> 
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label"></label>
          <div class="col-md-8">
            <input type="submit" class="btn btn-primary" id="registerBtn" value="Save Changes">
            <span></span>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<hr>

<div class ="index-errormsg"></div>

<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error under the signup/login form
        $('.index-errormsg').html(message)
    }
    //Retrieves the patient details based on his username
    $(async() => { 
        var username = sessionStorage.getItem("username");
        $('#username').val(username);      
        //After inserting the username based on the session variable, set it to readonly so the user cannot edit it
        $('#username').attr("readonly", true);     
        
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        var serviceURL = "http://" + patientip + "/update-profile-process";
        //var serviceURL = "http://" + patientip + "/update-profile-process";
        try {
                //console.log(JSON.stringify({ username: username, password: password,}))
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ username: username})
                                            });
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    //Fills in the patient details
                    $('#name').val(data['name']);
                    $('#dob').val(data['dob']);
                    $('#phone').val(data['phone']);
                    $('#salutation').val(data['salutation']);
                    if (data['gender'] == "female") {
                        $('#gender').val("Female");  
                    }
                    else {
                      $('#gender').val("Male");  
                    }
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
            ('There is a problem retrieving patients data, please try again later. Tip: Did you forget to run patient.py? :)<br />'+error);
            
            }
    });

    function add_alert(message, color){

      if (color == "green"){
        var type = "alert-success";
      } else{
        var type = "alert-danger";
      }
      var alert_value = 
          '<div class="alert ' + type +' alert-dismissable">' +
          '<a class="panel-close close" data-dismiss="alert">×</a>' + 
          '<i class="fa fa-coffee"></i>' + 
          message + 
          '</div>';

      $('#add_alert').empty();
      $('#add_alert').append(alert_value);

    }

    //This is the form id, not the submit button id!
    $("#loginForm").submit(async (event) => {
        event.preventDefault();     
        var name = $('#name').val();
        //Need to use checked to select radio buttons in jquery
        var gender = $("#gender").val();
        var dob = $('#dob').val();
        var phone = $('#phone').val();
        var salutation = $('#salutation').val();
        var username = $('#username').val();
        var password = $('#password').val();
        var newpassword = $('#newpassword').val();
        var newpasswordconfirm = $('#newpasswordconfirm').val();
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        var serviceURL = "http://" + patientip + "/update-profile-update";
        //var serviceURL = "http://" + patientip + "/update-profile-update";
        if (newpassword != newpasswordconfirm) {
            add_alert("New password and Confirm Password do not match", "red");
        }
        else {
            if (newpassword == "") {
                //If new password field is empty, it means the user do not want to update password
                newpassword = password;
            }
            try {
                    //console.log(JSON.stringify({ username: username, password: password,}))
                    const response = await fetch(serviceURL,{method: 'POST',
                                                headers: { "Content-Type": "application/json" },
                                                body: JSON.stringify
                                                ({ name: name, gender: gender, dob: dob, phone: phone, salutation: salutation, username: username, password: newpassword, checkpassword: password})
                                                });
                    const data = await response.json();
                    console.log(data)
                    //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                    if (data['message']) {
                        showError(data['message'])
                    } else {
                        add_alert("Successfully updated account details.", "green");
                        //Refreshes the page
                        // window.location.href = "patientUpdateProfile.php";               
                    }
                } catch (error) {
                    // Errors when calling the service; such as network error, service offline, etc
                    showError
                ('There is a problem updating patients data, please try again later. Tip: Did you forget to run patient.py? :)<br />'+error);
                
                }
            }
        
    });
//Use javascript to retreive the error from the url and display it
//An example of such error is a user tries to enter another page without logging in, causing protect.php to redirect them back to this page
let params = new URLSearchParams(location.search);
showError(params.get('error'))
//There will be ?signout=true on theu rl if the user signed out on the navbar in patientLanding.php(or other pages). Destroy session username if that is the case
if (params.get('signout') == "true") {
    sessionStorage.removeItem('username')
}
</script>

</body>

</html>