<?php
    require_once "my_sql.php";
    require_once "sanitize.php";
    
    // If user types in username and password, check if credentials match existing account
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
	$un_temp = entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
	$pw_temp = entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);

	$query = "SELECT * FROM users WHERE username='$un_temp'";
	$result = $conn->query($query); 
	if (!$result) die(mysql_fatal_error());
        
	else if ($result->num_rows) {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
            $salt1 = $row[3]; 
            $salt2 = $row[4];
            $token = hash('ripemd128', "$salt1$pw_temp$salt2");
            if ($token == $row[2]) {
		session_start();	
		$_SESSION['username'] = $un_temp;
		$_SESSION['password'] = $pw_temp;
		$_SESSION['email'] = $row[1];
                // $_SESSION['check'] to prevent session hijacking
                $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
		echo "Hi $row[0], you are now logged in!";
		die ("<p><a href=program.php>Click here to continue</a></p>");
            }
            else die("Invalid username/password combination");
	}
	else die("Invalid username/password combination");
    }
    else {
	header('WWW-Authenticate: Basic realm="Restricted area"');
	header('HTTP/1.0 401 Unauthorized');
        die ("Please enter your username and password");
    }
    
    $conn->close();
?>

