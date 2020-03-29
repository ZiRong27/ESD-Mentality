<html>
<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body>

<div id="main-container" class="container">
<br><br><br><br>
<!-- <div id=whole style="border:1px solid #696969; border-radius:20px; padding:20px; box-shadow: 2px 3px #989898; background:white;"> -->
<div class="row d-flex justify-content-center">
        <form id ="loginForm" class="col-12 justify-content-center" >
        <div class="text-center">
            <img src="../images/logo_blue.jpg" height="180" width="400"> 
        </div>
        <div class = "whitetextbig">     
                Register an account
            </div>
        <br/>
            <div class = "row">
                <div class = "col-2">
                    <input type="text" class="form-control input-group-lg" id="salutation" placeholder="Salutation" required style="background-color:#f5f5f5">
                </div>
                <div class = "col-5">
                    <input type="text" class="form-control input-group-lg" id="firstname" placeholder="First name" required style="background-color:#f5f5f5">
                </div>
                <div class = "col-5">
                    <input type="text" class="form-control input-group-lg" id="surname" placeholder="Surname" required style="background-color:#f5f5f5">
                </div>
            </div>
            <br>
            <div class="whitetext">
                Gender: 
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender1" value="female" required >
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
                    <input type="text" class="form-control input-group-lg" id="dob" placeholder="Date of birth" required style="background-color:#f5f5f5">
                </div>
                <div class = "col-6">
                    <input type="text" class="form-control input-group-lg" id="phone" placeholder="Phone" required style="background-color:#f5f5f5">
                </div>
            </div>
            </br>
            <div class = "row">
                <div class = "col-6">
                    <input type="text" class="form-control input-group-lg" id="username" placeholder="Username" required style="background-color:#f5f5f5">
                </div>
                <div class = "col-6">
                    <input type="password" class="form-control input-group-lg" id="password" placeholder="Password" required style="background-color:#f5f5f5">
                </div>
                </div>
            </div>
            <div class="text-right">
                <!-- The button type has to be submit for the below async functions to work!-->
                <!--<button type="button" class="btn btn-primary btn-lg" id="signup"> Sign Up </button>-->
                    &nbsp;
                    &nbsp;
                    <button type="submit" class="btn btn-primary btn-lg" id="registerBtn" style="width:180px; height:50px;"> Register </button>
            </div>
            <br>
            <div class="text-right">
                    <a class = "whitetext" href="../index.php" style="color:black; font-size:15px;">Already have an account? <br> Sign in here!</a>
                </div>  
        </form>
        <div class ="index-errormsg"></div>

</div>
</div>
<script>    
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error under the signup/login form
        $('.index-errormsg').html(message)
    }
    //This is the form id, not the submit button id!
    $("#loginForm").submit(async (event) => {
        event.preventDefault();     
        //Use comma to seperate the name for easy splitting in patientUpdateProfile.php
        var name = $('#surname').val() + "," + $('#firstname').val();
        //Need to use checked to select radio buttons in jquery
        var gender = $("input[name='gender']:checked").val();
        var dob = $('#dob').val();
        var phone = $('#phone').val();
        var salutation = $('#salutation').val();
        var username = $('#username').val();
        var password = $('#password').val();
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        var serviceURL = "http://" + sessionStorage.getItem("patientip") + "/register-process";
        try {
                //console.log(JSON.stringify({ username: username, password: password,}))
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ name: name, gender: gender, dob: dob, phone: phone, salutation: salutation, username: username, password: password})
                                            });
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    alert("Successfully created an account. Please sign in!")
                    //Redirects the user to sign in page
                    window.location.href = "../index.php";               
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
            ('There is a problem sending patients data, please try again later. Tip: Did you forget to run patient.py? :)<br />'+error);
            
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