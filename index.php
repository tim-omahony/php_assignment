<?php include 'header.php';?>
<link rel="stylesheet" href="style.css">
	<meta charset="utf-8">
	<title>Home</title>
	
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
$productLines = array();
if ($result = $conn->query("SELECT * FROM productlines")) {
  while($obj = $result->fetch_object()) {
    $productLines[] = $obj;
  }

    $result->close();
} else {
  echo "No result";
}
$products = array();
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchProducts'])) {
  $products = fetchProductsForProductLine($conn, $_POST['fetchProducts']);
}

function fetchProductsForProductLine($conn, $productLineName) {
  if ($result = $conn->query("SELECT * FROM products WHERE productLine = '$productLineName'")) {
    $products = [];
    while($product = $result->fetch_assoc()) {
      $products[] = $product;
    }
    return $products;
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
  
<?php if (!empty($products)): ?>
<table>
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', array_keys(current($products))); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($products as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<form id="generate-form" method="POST">
    <?php foreach($productLines as $product): ?>
        <label value="<?php echo $product->productLine; ?>">
            <input type="submit" name="fetchProducts" value="<?php echo $product->productLine; ?>" />
            <?php echo "<p>'$product->textDescription': </p>";  ?>
        </label><br>
    <?php endforeach; ?>
</form>
</body>

<?php include 'footer.php';?>