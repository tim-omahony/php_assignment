<?php include 'header.php';?>
	<meta charset="utf-8">
	<title>Offices</title>
	<link rel="stylesheet" href="style.css">
	

<body>
<br>
<?php

$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, "classicmodels");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$offices = array();
if ($result = $conn->query("SELECT addressLine1, addressLine2, city, phone, officeCode FROM offices")) {
  while($obj = $result->fetch_assoc()) {
    $offices[] = $obj;
  }

    $result->close();
} else {
  echo "No result";
}

$employees = array();
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchEmployees'])) {
  $employees = fetchEmployeesForOffices($conn, $_POST['fetchEmployees']);
}

function fetchEmployeesForOffices($conn, $officeCode) {
  if ($result = $conn->query("SELECT lastName, firstName, jobTitle, employeeNumber, email FROM employees WHERE officeCode = '$officeCode' GROUP BY jobTitle")) {
    $employees = [];
    while($employee = $result->fetch_assoc()) {
      $employees[] = $employee;
    }
    return $employees;
  } 
}
?>

<!--below error taken from W3 Schools tutorial on error handling.-->
  
<?php

function customError($errno, $errstr) {
  echo "<b>Error:</b> [$errno] $errstr<br>";
  echo "Ending Script";
  die();
}

set_error_handler("customError",E_USER_WARNING);

if ($conn->connect_error) {
  trigger_error("Connection failed",E_USER_WARNING);
}
?>  
  
<div class="officeSection">
  
<form id="generate-form" method="POST"> 
<?php if (!empty($offices)): ?>
<table>
  <thead>
    <tr>
      <th>Address line 1</th>
      <th>Address line 2</th>
      <th>City</th>
      <th>Phone Number</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($offices as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo $row['addressLine1'] ?></td>
      <td><?php echo $row['addressLine2'] ?></td>
      <td><?php echo $row['city'] ?></td>
      <td><?php echo $row['phone'] ?></td>            
      <td><button type="submit" name="fetchEmployees" value="<?php echo $row['officeCode']; ?>">Employees</button></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
<?php endif; ?>
</form>
  
</div>
  
  <br>

<div class="employeeSection">
  
<?php if (!empty($employees)): ?>
<table>
<thead>
  <tr>
    <th><?php echo implode('</th><th>', array_keys(current($employees))); ?></th>
  </tr>
</thead>
<tbody>
<?php foreach ($employees as $row): array_map('htmlentities', $row); ?>
  <tr>
    <td><?php echo implode('</td><td>', $row); ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<?php endif; ?>
  
</div>
  
<?php include 'footer.php';?>