<html>
<header>
    <?php include '../include/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "../style/style-login.css" />
</header>

<style>
.bg-image {
  background-image: url('../images/doctorlogin.jpg');
  background-size: cover;
  background-position: center;
}
</style>

<div class="container-fluid">
  <div class="row no-gutter">
    <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <img class="d-flex justify-content-center" src="../images/logo_blue.jpg" style="height:200px; width:450px;">
              <h3 class="login-heading mb-4">Doctor Portal</h3>
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
                  <br><hr>
                  <a class="medium font-weight-bold .text-secondary" href="../index.php">Patient Portal</a></div>
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
    //Helper function to display error message
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
        var serviceURL = "http://" + doctorip + "/login-process-doctor";

        try {
                const response = await fetch(serviceURL,{method: 'POST',
                                            headers: { "Content-Type": "application/json" },
                                            body: JSON.stringify
                                            ({ username: username, password: password,})
                                            });
                const data = await response.json();
                console.log(data)
                //The error message is stored in the data array sent by doctor.py! If there is a message variable, it means there is an error
                if (data['message']) {
                    showError(data['message'])
                } else {
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
    //There will be ?signout=true on theu rl if the user signed out on the navbar in doctorLanding.php(or other pages). Destroy session username if that is the case
    if (params.get('signout') == "true") {
        sessionStorage.removeItem('username')
    }
</script>

</body>

</html>