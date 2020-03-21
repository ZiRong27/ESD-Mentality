<html>
<header>
    <?php include 'include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "include/stylesheet.css" />
</header>


<body style="background:#f8f8f8;">

<div id="main-container" class="container">
    <div class="row d-flex justify-content-center">
            <form id ="loginForm" class="col-12 justify-content-center" >
            <div class="text-center">
                <img src="images/logo.png" height="200" width="300"> ! chnage logo color !
            </div>
            <div class = "whitetextbig" style="color:black;">     
                Patient Login:
            </div>
            <br/>
                <div class>
                    <input type="text" class="form-control input-group-lg" id="username" placeholder="Username" required>
                </div>

                </br>

                <div class>
                    <input type="password" class="form-control input-group-lg" id="password" placeholder="Password" required>
                </div>

                </br>
               <!-- <a class = "whitetext" href="doctor/doctorLogin.php" style="color:#505050;"> Or login as a doctor</a> -->
                <div class="text-right">
                    <!-- The button type has to be submit for the below async functions to work!-->
                    <!--<button type="button" class="btn btn-primary btn-lg" id="signup"> Sign Up </button>-->
                        &nbsp;
                        &nbsp;
                        <button type="submit" class="btn btn-primary btn-lg" id="loginBtn" style="width:180px; height:45px;"> Login </button>
                </div>
                <br>
                <!-- <a class = "whitetext" href="doctor/doctorLogin.php" style="color:black; font-size:15px;"> Login as a doctor</a> -->
                <div class="text-right">
                    <a class = "whitetext" href="patient/register.php" style="color:black; font-size:15px;"> No account? Sign up now!</a>
                    <br>
                    <a class = "whitetext" href="doctor/doctorLogin.php" style="color:black; font-size:15px; text-align: center;"> OR login as a doctor</a>
                </div>  
            </form>
            <div class ="index-errormsg"></div>

    </div>
</div>
<script>    
    //IMPORTANT Set AWS IP address for each microservices here
    //All these are working! Uncomment to try them, you will NOT need to run any of these microservices yourself
    //sessionStorage.setItem('patientip', "13.250.127.183:5001")
    //sessionStorage.setItem('doctorip', "54.169.208.175:5002")
    //sessionStorage.setItem('appointmentip', "13.229.101.26:5003")
    sessionStorage.setItem('patientip', "127.0.0.1:5001")
    sessionStorage.setItem('doctorip', "127.0.0.1:5002")
    sessionStorage.setItem('appointmentip', "127.0.0.1:5003")   
    sessionStorage.setItem('consultationip', "127.0.0.1:5004")   
    //Retrieve with sessionStorage.getItem("patientip")
    // Helper function to display error message
    function showError(message) {
        console.log('Error logged')
        // Display an error under the signup/login form
        $('.index-errormsg').html(message)
    }
    //This is the form id, not the submit button id!
    $("#loginForm").submit(async (event) => {
        event.preventDefault();     
        var username = $('#username').val();
        var password = $('#password').val();
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        //var serviceURL = "http://" + sessionStorage.getItem("patientip") + "/login-process";
        //var serviceURL = "http://" + sessionStorage.getItem("patientip") + "/login-process";
        var serviceURL = "http://127.0.0.1:5001/login-process";
        //var serviceURL = "http://54.255.225.231:5001/login-process";
        try {
                //console.log(JSON.stringify({ username: username, password: password,}))
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ username: username, password: password,})
                                            });
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by patient.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
                    //alert("Successfully logged in!")
                    //Comment this line if you want to debug the response using console.log(JSON.stringify({ title: title, price: price,availability: availability })) 
                    //Set the session to username so it wont log the user out.
                    sessionStorage.setItem('username', data['username'])
                    sessionStorage.setItem('patient_id', data['patient_id'])
                    window.location.href = "patient/patientLanding.php";               
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving patients data, please try again later. Tip: Did you forget to run patient.py? :)<br />'+error);
               
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