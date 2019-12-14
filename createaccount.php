<?php // MAIN INDEX FILE
    require_once "sanitize.php";
    require_once "my_sql.php";
    
    define("MINIMUM_UN_LENGTH", 5);
    define("MINIMUM_PW_LENGTH", 10);
    
    $email = $username = $password = "";
    
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
        $email = entities_fix_string($conn, $_POST['email']);
        $username = entities_fix_string($conn, $_POST['username']);
        $password = entities_fix_string($conn, $_POST['password']);
    }  
    
    $fail = validate_email($email) . validate_username($username) . validate_password($password);
    
    // If validation succeeds, add account to database and moves to login
    if ($fail == "") {  
        $salt1 = generate_random_string();
        $salt2 = generate_random_string();
        $token = hash('ripemd128', "$salt1$password$salt2");
        $query = "INSERT INTO users VALUES" . "('$username', '$email', '$token', '$salt1', '$salt2')";
        $result = $conn->query($query);
        if (!$result) die(mysql_fatal_error());
        echo "Congratulations! Your account has been created! <a href=authenticate.php>Click here to login</a>";
        exit;
    }
    
    echo <<<_END
<html>
    <head>
	<title>Create An Account</title>
	<style>
	.signup {
            border:1px solid #999999; font: normal 14px helvetica; color: #444444;
	}
	</style>
	<script>
        const MINIMUM_UN_LENGTH = 5;
        const MINIMUM_PW_LENGTH = 10;
	function validate(form) {
            fail += validateEmail(form.email.value)
            fail += validateUsername(form.username.value)
            fail += validatePassword(form.password.value)
		
            if (fail == "") return true
            else { alert(fail); return false }
	}
	
       function validateEmail(field) {
            if (field == "") return "No Email was entered.\n"
            else if (!((field.indexOf(".") > 0) && (field.indexOf("@") > 0)) || /^\w+@[a-z]+\.(edu|com)$/.test(field) == false)
		return "Email address is invalid.\n"
            return ""
	}
    
	function validateUsername(field) {
            if (field == "") return "No username was entered.\n"
            else if (field.length < MINIMUM_UN_LENGTH)
		return "Usernames must be at least 5 characters.\n"
            else if (/[^a-zA-Z0-9_-]/.test(field))
		return "Only letters, numbers, underscores, and dashes allowed.\n"
            return ""
	}

	function validatePassword(field) {
            if (field == "") return "No password was entered.\n"
            else if (field.length < MINIMUM_PW_LENGTH)
		return "Passwords must be at least 10 characters.\n"
            else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) ||!/[0-9]/.test(field))
		return "Passwords require at least one uppercase letter, one lowercase letter, and one number.\n"
            return ""
	}
    
	</script>
</head>
<body>
	<table border="0" cellpadding="2" cellspacing="5" bgcolor="#eeeeee">
		<th colspan="2" align="center">Create An Account</th>
                <tr><td colspan="2"><p><font color=red size=3><i>$fail<br></i></font></p></td></tr>
		<form method="post" action="createaccount.php" onsubmit="return validate(this)">
			<tr><td>Email</td>
				<td><input type="text" maxlength="35" name="email"></td></tr>
			<tr><td>Username</td>
				<td><input type="text" maxlength="25" name="username"></td></tr>
			<tr><td>Password</td>
				<td><input type="password" maxlength="50" name="password"></td></tr> 
			<tr><td colspan="2" align="center"><input type="submit"
				value="Sign Up"></td></tr>
		</form>
	</table>
</body>
    <p>Already have an account? <a href=authenticate.php>Click here to login</a></p>
</html>

_END;
    
    // $result->close();
    $conn->close();
    
    function validate_email($field) {
        if ($field == "") return "No email was entered<br>";
        else if (!((strpos($field, ".") > 0) && (strpos($field, "@") > 0)) || !preg_match("/^\w+@[a-z]+\.(edu|com)$/", $field))
                return "Email address is invalid<br>";
        return "";
    }
    
    function validate_username($field) {
        if ($field == "") return "No username was entered<br>";
        else if (strlen($field) < MINIMUM_UN_LENGTH)
            return "Usernames must be at least 5 characters<br>";
        else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
            return "Only letters, numbers, underscores, and dashes allowed<br>";
        return "";
    }
    
    function validate_password($field) {
        if ($field == "") return "No password was entered<br>";
        else if (strlen($field) < MINIMUM_PW_LENGTH)
            return "Passwords must be at least 10 characters<br>";
        else if (!preg_match("/[a-z]/", $field) || !preg_match("/[A-Z]/", $field) || !preg_match("/[0-9]/", $field))
                return "Passwords require at least one uppercase letter, one lowercase letter, and one number<br>";
        return "";
    }
    
    function generate_random_string() {
        $result = bin2hex(random_bytes(20));
        return $result;
    }
?>

