<?php
date_default_timezone_set("Asia/Bangkok");
$servername = "us-cdbr-iron-east-04.cleardb.net";
$username = "bd846d39c4a64c";
$password = "7587373d";
$dbname = "heroku_4ec96a11d22cba3";

/// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

if($_POST['json']){
    $json = json_encode($_POST['json'], JSON_PRETTY_PRINT);
    $json = str_replace('&nbsp;', ' ',strval($json));
    $json = get_string_between($json, '"{\\', '}}"');
    $sql = "SELECT * FROM heroku_4ec96a11d22cba3.covid";
    $result =$conn->query($sql);
    $row = mysqli_num_rows($result);
    if($row !== 0) //if table has at least one row
    {
        while($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        //Update statement
        $sql2 = "UPDATE heroku_4ec96a11d22cba3.covid
                SET jsons = \"{\\".$json."}}\",
                    update_at = \"".date("Y-m-d H:i:s")."\"
                WHERE id = ".$id;
    }
    else
    {
        //Insert statement
        $sql2 = "INSERT INTO covid (jsons,update_at) VALUES (\"".$json."\",\"".date("Y-m-d H:i:s")."\")";
    }

    
    
    if ($conn->query($sql2) === TRUE) {
        echo "Fetching: Successfully";
    } else {
        echo "Error: " . $sql2 . "<br>" . $conn->error;
    }
}

$conn->close();
?>