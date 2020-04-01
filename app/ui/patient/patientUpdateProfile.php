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
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css"/>
</header>


<body style="background:#f8f8f8;">
<!-- Import navigation bar -->
<?php include '../include/patientNavbar.php';?>
<div id="main-container" class="container">
        <!-- <div class="text-center">
            <img src="../images/logo.png" height="200" width="300"> 
        </div> --> 
        <br> <br> 
<!-- <div id=whole style="border:1px solid #696969; border-radius:20px; padding:20px; box-shadow: 2px 3px #989898; background:white;"> -->
<div class="row d-flex justify-content-center">
        <form id ="loginForm" class="col-12 justify-content-center" >
        <div class="my-2">
            <h1>Update account details</h1>
            <hr>
        </div>
        <br/>
        <div class = "row">
            <div class = "col-2">
                <input type="text" class="form-control input-group-lg" id="salutation" placeholder="Salutation" required>
            </div>
            <div class = "col-5">
                <input type="text" class="form-control input-group-lg" id="firstname" placeholder="First name" required>
            </div>
            <div class = "col-5">
                <input type="text" class="form-control input-group-lg" id="surname" placeholder="Surname" required>
            </div>
        </div>
        <br>
        <div class="whitetext" style="color: gray">
            Gender: 
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender1" value="female" required>
                <label class="form-check-label" for="gender1">Female</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender2" value="male" required>
                <label class="form-check-label" for="gender2">Male</label>
            </div>         
        </div>
        <br>
        <div class = "row">
            <div class = "col-6">
                <input type="text" class="form-control input-group-lg" id="dob" placeholder="Date of birth" required>
            </div>
            <div class = "col-6">
                <input type="text" class="form-control input-group-lg" id="phone" placeholder="Phone" required>
            </div>
        </div>
        </br>
        <div class = "row">
            <div class = "col-6">
                <input type="text" class="form-control input-group-lg" id="username" placeholder="Username" required>
            </div>
            <div class = "col-6">
                <input type="password" class="form-control input-group-lg" id="password" placeholder="Original password" required>
            </div>
            </div>
        </div>
        <div class = "row">
            <div class = "col-6">
                <input type="password" class="form-control input-group-lg" id="newpassword" placeholder="New Password (Optional)" >
            </div>
            <div class = "col-6">
                <input type="password" class="form-control input-group-lg" id="newpasswordconfirm" placeholder="Confirm password (Optional)" >
            </div>
        </div>
        </br>
        <div class="text-right">               
            <!-- The button type has to be submit for the below async functions to work!-->
            <!--<button type="button" class="btn btn-primary btn-lg" id="signup"> Sign Up </button>-->
                &nbsp;
                &nbsp;
                <button type="submit" class="btn btn-primary btn-lg" id="registerBtn" style="height:40px; width:160px; padding:1px;"> Update details </button>
        </div>
    </form>
    <div class ="index-errormsg"></div>

</div>
</div>
</div>
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
        //var serviceURL = "http://" + sessionStorage.getItem("patientip") + "/update-profile-process";
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
                    var namearr= data['name'].split(",");
                    $('#surname').val(namearr[0]);
                    $('#firstname').val(namearr[1]);
                    $('#dob').val(data['dob']);
                    $('#phone').val(data['phone']);
                    $('#salutation').val(data['salutation']);
                    if (data['gender'] == "female") {
                        $('#gender1').attr("checked", true);  
                    }
                    else {
                        $('#gender2').attr("checked", true); 
                    }
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
            ('There is a problem retrieving patients data, please try again later. Tip: Did you forget to run patient.py? :)<br />'+error);
            
            }
    });

    //This is the form id, not the submit button id!
    $("#loginForm").submit(async (event) => {
        event.preventDefault();     
        var name = $('#surname').val() + "," + $('#firstname').val();
        //Need to use checked to select radio buttons in jquery
        var gender = $("input[name='gender']:checked").val();
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
        //var serviceURL = "http://" + sessionStorage.getItem("patientip") + "/update-profile-update";
        if (newpassword != newpasswordconfirm) {
            showError("New password and confirm password do not match");
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
                        alert("Successfully updated account details.")
                        //Refreshes the page
                        window.location.href = "patientUpdateProfile.php";               
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