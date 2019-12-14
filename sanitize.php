<?php

    function entities_fix_string($conn, $string) {
        return htmlentities(fix_string($conn, $string));
    }

    function fix_string($conn, $string) {
        if (get_magic_quotes_gpc())
            $string = stripslashes($string);
        return $conn->real_escape_string($string);
    }
?>
