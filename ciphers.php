<?php
    
    define("ALPHABET", array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
    define("KEY", array('p', 'h', 'q', 'g', 'i', 'u', 'm', 'e', 'a', 'y', 'l', 'n', 'o', 'f', 'd', 'x', 'j', 'k', 'r', 'c', 'v', 's', 't', 'z', 'w', 'b'));
    
    /**
     * Encrypts the inputted text using the Simple Substitution cipher
     * 
     * @param string $text The text to be encrypted
     * @return string The encrypted text
     */
    function simple_sub_encrypt($text) {
        $encrypted_text = "";
        $found = false;
        
        for ($i = 0; $i < strlen($text); $i++) {
            for ($j = 0; $j < sizeof(ALPHABET); $j++) {
                if ($text[$i] == ALPHABET[$j]) {
                    $encrypted_text .= KEY[$j];
                    $found = true;
                    break;
                }
            }
            if ($found)
                $found = false;
            else
                $encrypted_text .= $text[$i];
        }
        return $encrypted_text;
    }
    
    /**
     * Decrypts the inputted text using the Simple Substitution cipher
     * 
     * @param string $text The text to be decrypted
     * @return string The decrypted text 
     */
    function simple_sub_decrypt($text) {
        $decrypted_text = "";
        $found = false;
        
        for ($i = 0; $i < strlen($text); $i++) {
            for ($j = 0; $j < sizeof(KEY); $j++) {
                if ($text[$i] == KEY[$j]) {
                    $decrypted_text .= ALPHABET[$j];
                    $found = true;
                    break;
                }
            }
            if ($found)
                $found = false;
            else
                $decrypted_text .= $text[$i];
        }
        return $decrypted_text;
    }
    
    /**
     * Encrypts the inputted text using the Double Transposition cipher
     * 
     * @param string $text The text to be encrypted
     * @return string The encrypted text
     */
    function double_transposition_encrypt($text) {
        $first_key = "ZEBRAS";
        $first_transposition = transposition_encrypt($first_key, $text);
        $second_key = "ACTION";
        $second_transposition = transposition_encrypt($second_key, $first_transposition);
        return $second_transposition;
    }
    
    /**
     * Helper method for double_transposition_encrypt that does the actual encrypting.
     * Exists because different keys may be used.
     * 
     * @param string $key The key to be used for encrypting
     * @param string $text The text to be encrypted
     * @return string The encrypted text
     */
    function transposition_encrypt($key, $text) {
        $key_row = str_split($key);
        
        $no_punctuation_text = remove_puncation_and_spaces($text);
        $upper_case_text = strtoupper(preg_replace('/\s+/', '', $no_punctuation_text));
        
        $grid = create_grid($key_row, $upper_case_text);
        $letter_columns = attach_columns_to_keyword_letter($grid, $key_row);
        $result = create_encryped_message($key_row, $letter_columns);
        
        return $result;
    }
    
     /**
     * Arranges the letters of the text into a grid. Creates an array for every 
     * keyword.length letters of the text and adds them to the grid
     * 
     * @param array $keyword_array The letters of the keyword split into an array
     * @param string $text The text to be arranged into a grid
     * @return array A 2D array of letters of text arranged in a grid layout
     */
    function create_grid($keyword_array, $text) {
        $grid = array();
        for ($i = 0; $i < strlen($text); $i += sizeof($keyword_array)) {
            $row = array();
            for ($j = $i; $j < sizeof($keyword_array) + $i; $j++) {
                if ($j == strlen($text))
                    break;
                array_push($row, $text[$j]);
            }
            array_push($grid, $row);
        }
        return $grid;
    }
    
    /**
     * Connects every column of the grid to a letter of the keyword
     * 
     * @param array $grid The grid that the columns will be extracted from
     * @param array $key_row The letters of the keyword split into an array
     * @return array Dictionary containing the columns using letters of the keyword as keys
     */
    function attach_columns_to_keyword_letter($grid, $key_row) {
        
        // Determine maximum number of columns
        $maximumColumns = 0;
        foreach ($grid as $row) {
            if (sizeof($row) > $maximumColumns)
                $maximumColumns = sizeof($row);
        }
        
        $letter_columns = array();
        for ($column = 0; $column < $maximumColumns ; $column++) {
            $result = "";
            for ($row = 0; $row < sizeof($grid); $row++) {
                if (!($column >= sizeof($grid[$row])))
                    $result .= $grid[$row][$column];
            }
            if (strlen($result) != sizeof($grid))
                $result .= randLetter();
            $letter_columns[$key_row[$column]] = $result;
        }
        return $letter_columns;
    }
    
    /**
     * Takes column connected to letters of the keyword in alphabetical order
     * and creates the encrypted message using them
     * 
     * @param array $key_row The letters of the keyword split into an array
     * @param array $letter_columns Dictionary containing the columns using letters of the keyword as keys
     * @return string The encrypted message
     */
    function create_encryped_message($key_row, $letter_columns) {
        $result = "";
        $sorted_key_row = $key_row;
        asort($sorted_key_row);
        foreach ($sorted_key_row as $letter)
            $result .= $letter_columns[$letter] . " ";
        return $result;
    }
    
     /**
     * Decrypts the inputted text using the Double Transposition cipher
     * 
     * @param string $text The text to be decrypted
     * @return string The decrypted text 
     */
    function double_transposition_decrypt($text) {
        $no_punctuation_text = remove_puncation_and_spaces($text);
        
        $second_key = "ACTION";
        $pre_result = transposition_decrypt($second_key, $no_punctuation_text);
        
        $first_key = "ZEBRAS";
        $result = transposition_decrypt($first_key, $pre_result);
        
        return $result;
    }
    
     /**
     * Helper method for double_transposition_decrypt that does the actual decrypting.
     * Exists because different keys may be used.
     * 
     * @param string $key The key to be used for decrypting
     * @param string $text The text to be decrypted
     * @return string The decrypted text
     */
    function transposition_decrypt($key, $text) {
        $key_row = str_split($key);
        sort($key_row);
        $num_of_rows = strlen($text) / sizeof($key_row);
        
        $letter_columns = attaches_text_to_alphabetized_letters_of_keyword($text, $key_row, $num_of_rows);
        $grid = put_text_of_letters_of_keyword_onto_grid($key, $letter_columns, $num_of_rows);
        $result = create_decrypted_message($grid, $key_row);
        
        return $result;
    }
    
    /**
     * The encrypted text is split into columns of keyword.length and attached
     * to a letter of the alphabetized keyword
     * 
     * @param string $text The encrypted text to be split into columns
     * @param array $key_row The sorted letters of the keyword split into an array
     * @param int $num_of_rows The number of rows of the grid
     * @return array Dictionary containing the columns using letters of the keyword as keys
     */
    function attaches_text_to_alphabetized_letters_of_keyword($text, $key_row, $num_of_rows) {
        $count = 0;
        $column = "";
        $letter_columns = array();
        
        for ($i = 0; $i < strlen($text); $i++) {
            if ($i % $num_of_rows == 0 && $i != 0) {
                $letter_columns[$key_row[$count]] = $column;
                $count++;
                $column = "";
            }
            else if ($i + 1 == strlen($text)) {
                $column .= $text[$i];
                $letter_columns[$key_row[$count]] = $column;
                $count++;
                $column = "";
            }
            $column .= $text[$i];
        }
        return $letter_columns;
    }
    
    /**
     * Organizes the columns of letters of the text into a grid
     * 
     * @param string $key The key being used for decryption. Not alphabetically sorted
     * @param array $letter_columns Dictionary containing the columns using letters of the keyword as keys
     * @param int $num_of_rows The number of rows of the grid
     * @return type
     */
    function put_text_of_letters_of_keyword_onto_grid($key, $letter_columns, $num_of_rows) {
        $second_key_row_unsorted = str_split($key);
        $grid = array();
        for ($row = 0; $row < sizeof($letter_columns); $row++) {
            $column = $letter_columns[$second_key_row_unsorted[$row]];
            for ($col = 0; $col < $num_of_rows; $col++) {
                $grid[$col][$row] = $column[$col];
            }
        }
        return $grid;
    }
    
    /**
     * Goes through each letter of the grid and creates the decrypted message
     * 
     * @param array $grid The 2D array that will be parsed
     * @param array $key_row The sorted letters of the keyword split into an array
     * @return string The decrypted message
     */
    function create_decrypted_message($grid, $key_row) {
        $result = "";
        for ($i = 0; $i < sizeof($grid); $i++) {
            for ($j = 0; $j < sizeof($key_row); $j++) {
                $result .= $grid[$i][$j];
            }
        }
        return $result;
    }
    
    /**
     * Symmetric function that encrypts/decrypts the inputted text using the RC4 cipher
     * 
     * @param string $text The text to be encrypted/decrypted
     * @param boolean $encrypt_or_decrypt True = encrypt, false = decrypt
     * @return string The encrypted/decrypted text. Encrypted text is given in binary format
     */
    function rc4($text, $encrypt_or_decrypt) {
        $key = "ultimatekey";
        $arr = array();
        $result = "";

        if(!$encrypt_or_decrypt)
          $text = hex2bin($text);

        for($i = 0; $i<256; $i++)
          $arr[$i] = $i;

        for($i= 0; $i < 256; $i++){
          $index = ($arr[$i] + ord($key[$i % strlen($key)])) % 256;
          $temp = $arr[$i];
          $arr[$i] = $arr[$index];
          $arr[$index] = $temp;
        }

        for($i = 0, $j = 0, $index = 0; $i < strlen($text); $i++){
          $j = ($j + 1) % 256;
          $index = ($index + $arr[$j]) % 256;
          $temp = $arr[$j];
          $arr[$j] = $arr[$index];
          $arr[$index] = $temp;
          $result .= chr(ord($text[$i])^$arr[($arr[$j]+ $arr[$index]) % 256]);
        }

        return $result;
    }
    
    function remove_puncation_and_spaces($text) {
        return preg_replace('/[^a-z0-9]+/i', '', $text);
    }
    
    function randLetter(){
        $int = rand(0,25);
        $a_z = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $rand_letter = $a_z[$int];
        return $rand_letter;
    }
    
