<?php
$page_title = 'Add Product';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);
date_default_timezone_set('Asia/Manila');
$all_categories = find_all('categories');
$all_photo = find_all('media');

// Sort the array of photos alphabetically by file name
usort($all_photo, function ($a, $b) {
  return strcmp($a['file_name'], $b['file_name']);
});
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
if (isset($_POST['add_product'])) {
  $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price', 'saleing-price','product-itemID');
  validate_fields($req_fields);
  if (empty($errors)) {
    $p_name  = remove_junk($db->escape($_POST['product-title']));
    $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
    $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
    $p_buy   = remove_junk($db->escape($_POST['buying-price']));
    $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
    $p_itemID = remove_junk($db->escape($_POST['product-itemID']));
    if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
      $media_id = '0';
    } else {
      $media_id = remove_junk($db->escape($_POST['product-photo']));
    }
    $date    = make_date();
    $query  = "INSERT INTO products (";
    $query .= " name,quantity,buy_price,sale_price,categorie_id,media_id,date,itemID";
    $query .= ") VALUES (";
    $query .= " '{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$media_id}', '{$date}', '{$p_itemID}'";
    $query .= ")";
    $query .= " ON DUPLICATE KEY UPDATE name='{$p_name}'";
    if ($db->query($query)) {
      //Get the name of the added product
      $added_product_name = $p_name;
      $session->msg('s', "Product '{$added_product_name}' has been added. ");
      redirect('add_product.php', false);
    } else {
      $session->msg('d', ' Sorry product: {added_product_name} failed to be added!');
      redirect('product.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('add_product.php', false);
  }
}

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-image: linear-gradient(to right, #FFC0E4, #FFAED7, #FF9CCB, #FF89BF, #FF77B3, #FF64A8, #FF519C, #FF3E90); color:black">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Product</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix">
            <!-- productName input field -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="product-title" placeholder="Product Name" oninput="allowLettersOnly(event)">
              </div>
            </div>
            <!-- itemID input Field -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-tag"></i>
                </span>
                <input type="number" class="form-control" name="product-itemID" placeholder="Item ID">
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <select class="form-control" name="product-categorie">
                    <option value="">Select Product Category</option>
                    <?php foreach ($all_categories as $cat) : ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select class="form-control" name="product-photo">
                    <option value="">Select Product Photo</option>
                    <?php foreach ($all_photo as $photo) : ?>
                      <option value="<?php echo (int)$photo['id'] ?>">
                        <?php echo $photo['file_name'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                    </span>
                    <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group"> <span class="input-group-addon">
                      ₱
                    </span>

                    <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                    <span class="input-group-addon">.00</span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      ₱
                    </span>
                    <input type="number" class="form-control" name="saleing-price" placeholder="Selling Price">
                    <span class="input-group-addon">.00</span>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" name="add_product" class="btn btn-danger">
              <span class="glyphicon glyphicon-plus-sign"></span> Add Product
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
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
<?php include_once('layouts/footer.php'); ?>
<script>
  function allowLettersOnly(event) {
    // Get the input value
    var inputValue = event.target.value;

    // Replace any numbers with an empty string
    var lettersOnly = inputValue.replace(/[0-9]/g, '');

    // Update the input value with letters only
    event.target.value = lettersOnly;
  }
</script>