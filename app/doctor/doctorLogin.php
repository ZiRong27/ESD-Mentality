<html>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../include/stylesheet.css" />
</header>


<body>

<div id="main-container" class="container">
<div class="row d-flex justify-content-center">
        <form id ="loginForm" class="col-12 justify-content-center" >
        <div class="text-center">
            <img src="../images/logo.png" height="200" width="300"> 
        </div>
            <div class = "whitetextbig">     
                Login (Doctor)
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
            <a class = "whitetext" href="../index.php"> Login as a user</a>
            <div class="text-right">"
                
                
                <!-- The button type has to be submit for the below async functions to work!-->
                <!--<button type="button" class="btn btn-primary btn-lg" id="signup"> Sign Up </button>-->
                    &nbsp;
                    &nbsp;
                    <button type="submit" class="btn btn-primary btn-lg" id="loginBtn"> Login </button>
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
        var username = $('#username').val();
        var password = $('#password').val();
        //This is the url found above the login function in patient.py. Basically you are trying to send data(username and password) to that url using post and receive its response
        //The response you get is found is sent by the json function of the Patient class in patient.py
        //var serviceURL = "http://127.0.0.1:5002/login-process-doctor";
        var serviceURL = "http://54.169.208.175:5002/login-process-doctor";
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
                    sessionStorage.setItem('doctor_id', data['doctor_id'])
                    window.location.href = "doctorLanding.php";               
                }
            } catch (error) {
                // Errors when calling the service; such as network error, service offline, etc
                showError
              ('There is a problem retrieving doctors data, please try again later. Tip: Did you forget to run doctor.py? :)<br />'+error);
               
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