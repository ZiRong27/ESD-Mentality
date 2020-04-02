<html>
<head>
    <!--Install jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../style/style-login.css" />
</header>


<body>
<div class="container-fluid">
  <div class="row no-gutter">
    <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <h3 class="login-heading mb-4">Patient Portal Registration</h3>
              <form id="loginForm">
                <div class="form-label-group">
                  <input id="salutation" class="form-control" placeholder="Salutation" required autofocus>
                  <label for="salutation">Salutation</label>
                </div>
                <div class="form-label-group">
                  <input id="firstname" class="form-control" placeholder="First name" required autofocus>
                  <label for="firstname">First name</label>
                </div>
                <div class="form-label-group">
                  <input id="surname" class="form-control" placeholder="Surname" required autofocus>
                  <label for="surname">Surname</label>
                </div>
                <div class="form-label-group">
                  <input id="dob" class="form-control" placeholder="Date of birth (yyyy-mm-dd)" required autofocus>
                  <label for="dob">Date of birth (yyyy-mm-dd)</label>
                </div>
                <div class="form-label-group">
                  <input id="phone" class="form-control" placeholder="Phone" required autofocus>
                  <label for="phone">Phone</label>
                </div>  
                <div class="form-label-group">
                  <input id="username" class="form-control" placeholder="Username" required autofocus>
                  <label for="username">Username</label>
                </div>
                <div class="form-label-group">
                  <input type="password" id="password" class="form-control" placeholder="Password" required>
                  <label for="inputPassword">Password</label>
                </div>
                <div class="text-left">
                  <br>
                  <a class="medium .text-primary">Gender:</a>
                  <br>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender1" value="female" required>
                    <label class="form-check-label" for="gender1">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender2" value="male" required>
                    <label class="form-check-label" for="gender2">Male</label>
                </div>   
                <div class="text-left">
                  <br>
                </div>
                <button id="registerBtn" class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign Up</button>
                <div class="text-center">
                  <br> 
                  <a class="medium font-weight-bold .text-secondary" href="../index.php">Back to login</a></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>

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
        var serviceURL = "http://" + patientip + "/register-process";
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
                    window.location.href = "index.php";               
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



</html>