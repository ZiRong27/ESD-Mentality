<?php
require_once 'common.php';
?>
<script>
	//Redirects the user to index page if not logged in
	if (sessionStorage.getItem("username") === null) {
		window.location.href = "../index.php?error=Please login!"; 	
	}
</script>