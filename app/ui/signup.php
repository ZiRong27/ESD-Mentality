<html>
<header>
    <?php include 'imports/codeLinks.php';?>
    <link rel = "stylesheet" type = "text/css" href = "imports/stylesheet.css" />
</header>

<body>

<div class="container">
<!-- <div class="row"> -->
<div class="justify-content-center">
    <br/>
    <b> <h1>  Sign Up  </h1> </b>
    <br/>
</div>
  
        <form>
            <!-- Name & salutation -->
            <label for="patientname"> <h3> Name : </h3> </label>
            <div class="form-group row">
                <div class="col-8">
                    <input type="text" class="form-control input-group-lg" id="patientname" placeholder="Name">
                </div>
                <div class="col-4">
                    <select class="form-control custom-select input-group-lg" id="salutation">
                        <option selected> Mx. </option>
                        <option value="Mr"> Mr. </option>
                        <option value="Ms"> Miss. </option>
                        <option value="Mdm"> Mdm. </option>
                    </select>
                </div>
            </div>
            </br>
            <!-- Gender -->
            <div>
                <label for="patientgender"> <h3> Gender : </h3> </label>
                    <select class="form-control custom-select input-group-lg" id="patientgender">
                        <option selected> Please select...</option>
                        <option value="female"> Female </option>
                        <option value="male"> Male </option>
                    </select>
            </div>
            </br>
            <!-- Date of Birth -->
            <div>
                <label for="dob"> <h3> Date Of Birth : </h3> </label>
                <input type="date" class="form-control input-group-lg data-date-end-date='0d'" id="dob">
            </div>
            </br>
            <!-- Mobile -->
            <div>
                <label for="phonenumber"> <h3> Mobile : </h3> </label>
                <input type="tel" class="form-control input-group-lg" id="phonenumber" placeholder="Telephone">
            </div>
            </br>
            <!-- email -->
            <div>
                <label for="username"> <h3> Username / Email : </h3> </label>
                <input type="email" class="form-control input-group-lg" id="username" placeholder="Username@outlook.com">
            </div>
            </br>
            <!-- password -->
            <div>
                <label for="password"> <h3> Password : </h3> </label>
                <input type="email" class="form-control input-group-lg" id="password" placeholder="password">
            </div>
            </br>

            <div class="text-right">
                <button type="button" class="btn btn-primary btn-lg" id="signup"> Create Account </button>
                    &nbsp;
                    &nbsp;
                <button type="button" class="btn btn-primary btn-lg btn-danger" id="cancel"> Cancel </button>
            </div>
        </form>

<!-- </div> -->
</div>

<script type="text/javascript">    
    $(document).ready(function() 
    {
        $("#signup").click(function()
        {
            window.location.href = "index.php";
        });
        $("#cancel").click(function()
        {
            window.location.href = "index.php";
        });
    });
</script>


</body>

</html>