<?php
// header("Access-Control-Allow-Origin: *");
// // Takes raw data from the request
// $json = file_get_contents('php://input');
// // Converts it into a PHP object
// $data = json_decode($json);
$pub_key = $_POST['pub_key'];
$checkout_session_id = $_POST['CHECKOUT_SESSION_ID'];
// $pub_key = $data->pub_key;
// $checkout_session_id = $data->checkout_session_id;

?> 


<html>

<head>
    <script src="https://js.stripe.com/v3/"></script>
<head>

<body>


<script>
    var pub_key  = '<?php echo $pub_key;?>';
    var CHECKOUT_SESSION_ID  = '<?php echo $checkout_session_id;?>';

    var stripe = Stripe(pub_key);
    stripe.redirectToCheckout({
    // Make the id field from the Checkout Session creation API response
    // available to this file, so you can provide it as parameter here
    // instead of the {{CHECKOUT_SESSION_ID}} placeholder.
    sessionId: CHECKOUT_SESSION_ID
    }).then(function (result) {
    // If `redirectToCheckout` fails due to a browser or network
    // error, display the localized error message to your customer
    // using `result.error.message`.
    });

</script>

</body> 

</html>