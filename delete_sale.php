<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  // Get categories
$categories = find_all('categories'); // Modified: Added code to fetch categories
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 'all'; // Modified: Get selected category

// Fetch products based on selected category
if ($category_id == 'all') {
  $products = join_product_table();
} else {
  $products = find_products_by_category($category_id); // Modified: Fetch products by category
}

// Step 1: Identify Critical Stock Products
$critical_stock_products = array(); // Initialize array to hold critical stock products
foreach ($products as $product) {
  if ($product['quantity'] < 10) {
    $critical_stock_products[] = $product; // Add critical stock product to array
  }
}

// Step 2: Pass the Count to Header.php
$_SESSION['critical_stock_count'] = count($critical_stock_products);
?>
<?php
  //Retrieve the sale details
  $d_sale = find_by_id('sales',(int)$_GET['id']);
  //Check if the sale exists
  if(!$d_sale){
    $session->msg("d","Missing sale id.");
    redirect('sales.php');
  }
  // Retrieve the product details of the sale
  $product = find_by_id('products', $d_sale['product_id']);

  // Check if the product exists
  if (!$product) {
    $session->msg("d", "Missing product id.");
    redirect('sales.php');
  }
  //Assign the value of $d_sale and $d_date to a variable $saleName and $saleDate 
  $saleName = $product['name']; // Product name
  $saleDate = $d_sale['date']; // Sale date
  ?>
  <?php
  //delete the sale
  $delete_id = delete_by_id('sales',(int)$d_sale['id']);
  //check if the sale was successfully deleted
  if($delete_id){
      $session->msg("s","Sale '$saleName' on '$saleDate' has been deleted. ");
      redirect('sales.php');
  } else {
      $session->msg("d","Sale deletion failed.");
      redirect('sales.php');
  }
?>
<!-- Step 3: Low Stock Modal -->
<div class="modal fade" id="lowStockModal" tabindex="-1" role="dialog" aria-labelledby="lowStockModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="lowStockModalLabel"><b>Low Stock Products</b></h4>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>Product Name</th>
              <th >Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($critical_stock_products as $critical_product) : ?>
              <tr>
                <td><em><?php echo remove_junk($critical_product['name']); ?></em></td>
                <td style="text-align: center; color:red"><b><?php echo remove_junk($critical_product['quantity']); ?></b></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-danger" data-dismiss="modal">
          <span class="glyphicon glyphicon-remove"></span> Close
        </button>
      </div>
    </div>
  </div>
</div>