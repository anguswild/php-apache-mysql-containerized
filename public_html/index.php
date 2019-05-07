<h1>Hello Cloudreach!</h1>
<h4>Attempting MySQL connection from php...</h4>
<?php

$mysqli = new MySQLi("mysql","root","rootpassword");
echo "<pre>";
print_r($mysqli);
echo "</pre>";

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Connected to MySQL successfully!";
}

?>
