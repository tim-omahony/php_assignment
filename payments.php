<?php include 'header.php';?>
	<link rel="stylesheet" href="style.css">
	<meta charset="utf-8">
<title>Payments</title>
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
$payments = array();
if ($result = $conn->query("SELECT checkNumber, paymentDate, amount, customerNumber FROM payments ORDER BY paymentDate LIMIT 20")) {
  while($obj = $result->fetch_assoc()) {
    $payments[] = $obj;
  }

    $result->close();
} else {
  echo "No result";
}
$customer = null;
$customer_payments = array();
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchCustomers'])) {
  $customer = fetchCustomerForPayment($conn, $_POST['fetchCustomers']);
  $customer_payments = fetchPaymentsForCustomer($conn, $customer);
}

function fetchCustomerForPayment($conn, $customerNumber) {
  if ($result = $conn->query("SELECT cu.phone, em.firstName AS salesRepFirstName, em.lastName AS salesRepLastName, cu.creditLimit, cu.customerNumber FROM customers AS cu JOIN employees AS em WHERE customerNumber = '$customerNumber'")) {
    return $result->fetch_object();
  } 
}

function fetchPaymentsForCustomer($conn, $customer) {
  if ($result = $conn->query("SELECT checkNumber, paymentDate, amount, customerNumber FROM payments WHERE customerNumber = '$customer->customerNumber'")) {
    $customerPayments = [];
    while($payment = $result->fetch_assoc()) {
      $customerPayments[] = $payment;
    }
    return $customerPayments;
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
  
<div class="paymentSummary">
  
<form id="generate-form" method="POST"> 
<?php if (!empty($payments)): ?>
<table>
  <thead>
    <tr>
      <th>Check Number</th>
      <th>Payment Date</th>
      <th>Amount</th>
      <th>Customer Number</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($payments as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo $row['checkNumber'] ?></td>
      <td><?php echo $row['paymentDate'] ?></td>
      <td><?php echo $row['amount'] ?></td>
      <td><input type="submit" name="fetchCustomers" value="<?php echo $row['customerNumber']; ?>" /></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</form>

</div>

<div class="customerSection">

<?php if ($customer != null): ?>
<ul class="customerSummary">
<li><strong>Customer Phone:</strong><?php echo $customer->phone; ?></li><br>
<li><strong>Sales Rep:</strong><?php echo "$customer->salesRepFirstName $customer->salesRepLastName"; ?></li><br>
<li><strong>Credit Limit:</strong><?php echo $customer->creditLimit; ?></li>
</ul>
<?php if (!empty($customer_payments)): ?>
<div><strong>Total:</strong><?php echo array_sum(array_column($customer_payments,'amount')); ?></div>
<table>
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', array_keys(current($customer_payments))); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($customer_payments as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
<p>No payments</p>
<?php endif; ?>
<?php endif; ?>

</div>

</body>

<?php include 'footer.php';?>