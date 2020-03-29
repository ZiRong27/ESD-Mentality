<html>
<header>
    <?php include 'include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "style/style-login.css"/>
</header>


<div class="container-fluid">
  <div class="row no-gutter">
    <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <img class="d-flex justify-content-center" src="images/logo_blue.jpg" style="height:200px; width:450px;">
              <h3 class="login-heading mb-4">Patient Portal</h3>
              <form id="loginForm">
                <div class="form-label-group">
                  <input id="username" class="form-control" placeholder="Username" required autofocus>
                  <label for="inputEmail">Username</label>
                </div>
                <div class="form-label-group">
                  <input type="password" id="password" class="form-control" placeholder="Password" required>
                  <label for="inputPassword">Password</label>
                </div>
                <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign in</button>
                <div class="text-center">
                  <br> 
                  <a class="medium font-weight-bold .text-secondary" href="patient/register.php">Register new account</a>
                  <hr>
                  <a class="medium font-weight-bold .text-secondary" href="doctor/doctorLogin.php">Doctor Portal</a>
                </div>
              </form>
              <div class='text-center'>
                <div class ="index-errormsg" style="background-color: #f8d7da; color: #8b3f46;">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script>    
    //IMPORTANT Set AWS IP address for each microservices here
    //Retrieve with patientip
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
        //var serviceURL = "http://" + patientip + "/login-process";
        //var serviceURL = "http://" + patientip + "/login-process";
        var serviceURL = "http://" + patientip + "/login-process";

        try {
                //console.log("PO")
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