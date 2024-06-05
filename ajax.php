<?php
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
  redirect('index.php', false);
}
?>

<?php
$html = '';
if (isset($_POST['product_name']) && strlen($_POST['product_name'])) {
  $partial_name = remove_junk($db->escape($_POST['product_name'])); // Get the partial input text
  $products = find_product_by_partial_name($partial_name); // Modify this function to retrieve matching product names
  if ($products) {
    foreach ($products as $product) :
      $html .= "<li class=\"list-group-item\">";
      $html .= $product['name'];
      $html .= "</li>";
    endforeach;
  } else {
    $html .= '<li class="list-group-item">';
    $html .= 'Not found';
    $html .= "</li>";
  }

  echo json_encode($html);
}
?>

<?php
// Find all product
if (isset($_POST['p_name']) && strlen($_POST['p_name'])) {
  $html = '';
  $product_title = remove_junk($db->escape($_POST['p_name']));
  if ($results = find_all_product_info_by_title($product_title)) {
    foreach ($results as $result) {
      $html .= "<tr>";
      $html .= "<td>{$result['itemID']}</td>";
      $html .= "<td>{$result['name']}</td>";
      $html .= "<input type=\"hidden\" name=\"s_id\" value=\"{$result['id']}\">";
      $html .= "<td><input type=\"text\" class=\"form-control\" name=\"price\" value=\"{$result['sale_price']}\"></td>";
      $html .= "<td><input type=\"text\" class=\"form-control\" name=\"quantity\" value=\"1\"></td>";
      $html .= "<td><input type=\"text\" class=\"form-control\" name=\"total\" value=\"{$result['sale_price']}\"></td>";
      $html .= "<td><input type=\"date\" class=\"form-control datePicker\" name=\"date\" data-date data-date-format=\"yyyy-mm-dd\"></td>";
      $html .= "<td><button type=\"submit\" name=\"add_sale\" class=\"btn btn-primary\"><span class=\"glyphicon glyphicon-plus-sign\"></span> Add sale</button></td>";
      $html .= "</tr>";
    }
  } else {
    $html = '<tr><td>Product name not registered in database</td></tr>';
  }

  echo json_encode($html);
}
?>
