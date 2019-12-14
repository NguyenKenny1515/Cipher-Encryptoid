<?php 
    require_once "sanitize.php";
    require_once "my_sql.php";
    require_once "ciphers.php";
    
    session_start();
    
    // Prevents session fixation
    if (!isset($_SESSION['initiated'])) {
	session_regenerate_id();
	$_SESSION['initiated'] = 1;
    }

    if (isset($_SESSION['username'])) {
        // Prevents session hijacking. If different IP and user agent string, destroy session
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) 
            different_user();

	$username = entities_fix_string($conn, $_SESSION['username']);
	$email = entities_fix_string($conn, $_SESSION['email']);
	$password = entities_fix_string($conn, $_SESSION['password']);

        echo <<< _END
            <html><head><title>PHP Form Upload</title></head></title><body>

            <form method='post' action='program.php' enctype='multipart/form-data'>
                <b>Upload Text File:</b> 
                <input type='file' name='filename' size='10'><br>
                <br><b>Cipher:</b>
            <select name="cipher" size="1">
                <option value="Simple Substitution">Simple Substitution</option>
                <option value="Double Transposition">Double Transposition</option>
                <option value="RC4">RC4</option>
            </select>
                <br><br><b>Task:</b>
            <label>Encrypt<input type="radio" name="task" value="1"></label>
            <label>Decrypt<input type="radio" name="task" value="2"></label>
                <pre>      <input type='submit' value='Generate'></pre>
            </form><br><br>

            <form method='post' action='program.php' enctype='multipart/form-data'>
                <b>Enter Text:</b> 
                <input type="text" name="text" size="60"><br>
                <br><b>Cipher:</b>
            <select name="cipher" size="1">
                <option value="Simple Substitution">Simple Substitution</option>
                <option value="Double Transposition">Double Transposition</option>
                <option value="RC4">RC4</option>
            </select>
                <br><br><b>Task:</b>
            <label>Encrypt<input type="radio" name="task" value="1"></label>
            <label>Decrypt<input type="radio" name="task" value="2"></label>
                <pre>      <input type='submit' value='Generate'></pre>
            </form><br>
                
            <form method='post' action='program.php' enctype='multipart/form-data'>
                <b>Done?</b> 
                <input type="hidden" name="logout" value="yes">
                <pre><input type='submit' value='Logout'></pre>
            </form><br><br><br>
_END;
        
        // When logout button pressed, destroy session and send user to create account page
        if(isset($_POST['logout'])) {
            destroy_session_and_data();
            header("Location: createaccount.php");
        }
        
        // If the user is using the upload txt file for encryption/decryption
        if ($_FILES && isset($_POST['cipher']) && isset($_POST['task'])) {
            $name = $_FILES['filename']['name'];
            $ext = $_FILES['filename']['type'];

            // Check if file is txt file
            if ($ext == "text/plain") {
                move_uploaded_file($_FILES['filename']['tmp_name'], $name); 
                $text = file_get_contents($name);

                $cipher = entities_fix_string($conn, $_POST['cipher']);
                $task = entities_fix_string($conn, $_POST['task']);

                if ($cipher == "Simple Substitution") {
                    if ($task == "1")
                        echo simple_sub_encrypt($text);
                    else
                        echo simple_sub_decrypt($text);
                }
                else if ($cipher == "Double Transposition") {
                    if ($task == "1")
                        echo double_transposition_encrypt($text);
                    else
                        echo double_transposition_decrypt($text);
                }
                else {
                    if ($task == "1")
                        echo bin2hex(rc4($text, true));
                    else
                        echo rc4($text, false);
                }
                date_default_timezone_set('America/Los_Angeles');
                $timestamp = date("M,d,Y H:i:s");
                $query = "INSERT INTO history VALUES" . "('$text', '$cipher', '$timestamp')";
                $result = $conn->query($query);
                if (!$result) die(mysql_fatal_error());
            }
            else
                echo "Incompatible file type. Only plain txt files allowed"; 
        }
        
        // If user is types in text box for encryption/decryption
        if (isset($_POST['text']) && isset($_POST['cipher']) && isset($_POST['task'])) {
            $text = entities_fix_string($conn, $_POST['text']);
            $cipher = entities_fix_string($conn, $_POST['cipher']);
            $task = entities_fix_string($conn, $_POST['task']);

            if ($cipher == "Simple Substitution") {
                if ($task == "1")
                    echo simple_sub_encrypt($text);
                else
                    echo simple_sub_decrypt($text);
            }
            else if ($cipher == "Double Transposition") {
                if ($task == "1")
                    echo double_transposition_encrypt($text);
                else
                    echo double_transposition_decrypt($text);
            }
            else {
                if ($task == "1")
                    echo bin2hex(rc4($text, true));
                else
                   echo rc4($text, false);
            }
            $timestamp = date("M,d,Y H:i:s");
            $query = "INSERT INTO history VALUES" . "('$text', '$cipher', '$timestamp')";
            $result = $conn->query($query);
            if (!$result) die(mysql_fatal_error());
        }

        echo "</body></html>"; // End webpage
    }
    else echo "Please <a href='authenticate.php'>click here</a> to log in.";
    
    // $result->close();
    $conn->close();
    
    function destroy_session_and_data() {
	$_SESSION = array();
	setcookie(session_name(), '', time() - 2592000, '/');
	session_destroy();
    }
    
    function different_user() {
        destroy_session_and_data();
        echo "Error! <a href='authenticate.php'>Please click here</a> to log in.";
    }
?>
