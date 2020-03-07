
<html>
<header>
    <?php include 'imports/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "imports/stylesheet.css" />
</header>


<body>

<div class="container">
<div class="row d-flex justify-content-center">


        <form class="col-12 justify-content-center">

        <div class="text-center">
            <img src="images/logo.png" height="200" width="300"> 
        </div>

            <div class>
                <label for="username"> <h3> Username: </h3> </label>
                <input type="text" class="form-control input-group-lg" id="username">
            </div>

            </br>

            <div class>
            <label for="password"> <h3> Password: </h3> </label>
                <input type="password" class="form-control input-group-lg" id="password">
            </div>

            </br>

            <div class="text-right">
                <button type="button" class="btn btn-primary btn-lg" id="login"> Login </button>
                    &nbsp;
                    &nbsp;
                <button type="button" class="btn btn-primary btn-lg btn-secondary" id="signup"> Sign Up </button>
            </div>
        </form>

</div>
</div>

<script>    
        // anonymous async function 
        // - using await requires the function that calls it to be async
        $(document).ready(function() 
        {
            $("#login").click(function()
            {
                    console.log(username)
                    var username = $("#username").val();   
                    var password = $("#password").val();  

                    console.log(username)
                    if(username == "a" && password =="a")  
                    {
                        window.location.href = "patientLanding.php";
                    } 
                    else if(username == "b" && password =="b")  
                    {
                        window.location.href = "doctorLanding.php";
                    }             
            });

            $("#signup").click(function()
            {
                window.location.href = "signup.php";
            });
        });
    </script>

</body>

</html>