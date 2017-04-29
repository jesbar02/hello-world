<!DOCTYPE html>
<html>
<head>
        <title>Searching for specific Employess</title>
</head>

<body>
<h1>Employees database search</h1>
<br/><p>"The present query, lists employees that are <b>Male</b>
which birth date is <b>1965-02-01</b>
and the hire date is greater than <b>1990-01-01</b>
ordered by the <b>Full Name</b>
of the employee."

<?php

$servername = "mysql-server"; // Please modify this value with the corresponding servername
$username = "user";
$password = "password";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<br><br>Connected successfully to the database !!!<br />";

$sql = "SELECT first_name,
                       last_name
                FROM   employees
                WHERE  gender = 'M'
                   AND birth_date = '1965-02-01'
                   AND `hire_date` > '1990-01-01'
                ORDER  BY first_name";

$result = $conn->query($sql);

echo "<p>Number of employees found: ".$result->num_rows."</p>";
echo "<p>full names: </p>";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
     echo $row["first_name"]. " " . $row["last_name"]. "<br>";
    }
}  else {
        echo "0 results";
}

$conn->close();

?>
</body>
</html>
