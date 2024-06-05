<?php
$page_title = 'Admin Home Page';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
// Check if user level is within 1-3
$user_level = current_user()['user_level'];
if (!in_array($user_level, array(1, 2, 3))) {
    redirect('dashboard.php', false);
}
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
$c_categorie     = count_by_id('categories');
$c_product       = count_by_id('products');
$c_sale          = count_by_id('sales');
$c_user          = count_by_id('users');
$products_sold   = find_higest_saleing_product('10');
$recent_products = find_recent_product_added('5');
$recent_sales    = find_recent_sale_added('5')
?>
<?php include_once('layouts/header.php'); ?>

<style>
  .panel.panel-box.clearfix {
    border-radius: 30px;
    border-top-left-radius: 100px;
    border-bottom-left-radius: 100px;
  }

  .panel-icon.pull-left {
    border-radius: 100px;
  }

  .img-avatar {
    cursor: pointer;
  }
</style>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <a href="users.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left" style="background-color: #FF6F61;">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_user['total']; ?> </h2>
          <p class="text-muted">Users</p>
        </div>
      </div>
    </div>
  </a>

  <a href="categorie.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left" style="background-color: #F2D632;">
          <i class="glyphicon glyphicon-th-large"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_categorie['total']; ?> </h2>
          <p class="text-muted">Categories</p>
        </div>
      </div>
    </div>
  </a>

  <a href="product.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left" style="background-color:#41C7C6;">
          <i class="glyphicon glyphicon-shopping-cart"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_product['total']; ?> </h2>
          <p class="text-muted">Products</p>
        </div>
      </div>
    </div>
  </a>

  <a href="sales.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left" style="background-color: #F79D3A;">
          <span style="color: white; font-size:45px;">₱</span>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php echo $c_sale['total']; ?></h2>
          <p class="text-muted">Sales</p>
        </div>
      </div>
    </div>
  </a>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading" style="background: linear-gradient(to right, #FF80C9,#FF70C2,#F464B6,#EF5DB1,#FF43AE); color:ghostwhite">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Highest Selling Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th>Title</th>
              <th>Total Sold</th>
              <th>Total Quantity</th>
            <tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as  $product_sold) : ?>
              <tr>
                <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                <td><?php echo (int)$product_sold['totalSold']; ?></td>
                <td><?php echo (int)$product_sold['totalQty']; ?></td>
              </tr>
            <?php endforeach; ?>
          <tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading" style="background: linear-gradient(to right, #FF80C9,#FF70C2,#F464B6,#EF5DB1,#FF43AE); color:ghostwhite">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>LATEST SALES</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product Name</th>
              <th>Date</th>
              <th>Total Sale</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as  $recent_sale) : ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td>
                  <a href="edit_sale.php?id=<?php echo (int)$recent_sale['id']; ?>">
                    <?php echo remove_junk(first_character($recent_sale['name'])); ?>
                  </a>
                </td>
                <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
                <td>₱<?php echo remove_junk(first_character($recent_sale['price'])); ?></td>
              </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading" style="background: linear-gradient(to right, #FF80C9,#FF70C2,#F464B6,#EF5DB1,#FF43AE); color:ghostwhite">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Recently Added Products</span>
        </strong>
      </div>
      <div class="panel-body">

          <div class="list-group">
          <?php foreach ($recent_products as  $recent_product) : ?>
            <div class="list-group-item clearfix">
              <div class="image-container">
                <?php if ($recent_product['media_id'] === '0') : ?>
                  <img class="img-avatar img-circle product-image" src="uploads/products/no_image.png" alt="" title="View Image" data-toggle="tooltip">
                <?php else : ?>
                  <img class="img-avatar img-circle product-image" src="uploads/products/<?php echo $recent_product['image']; ?>" alt="" title="View Image" data-toggle="tooltip" />
                <?php endif; ?>
              </div>
              <div class="product-details">
                <h4 class="list-group-item-heading">
                  <?php echo remove_junk(first_character($recent_product['name'])); ?>
                  <span class="label label-success pull-right">
                    ₱<?php echo (int)$recent_product['sale_price']; ?>
                  </span>
                </h4>
                <span class="list-group-item-text pull-right">
                  <?php echo remove_junk(first_character($recent_product['categorie'])); ?>
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal HTML -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="imageModalLabel">Product Image</h4>
      </div>
      <div class="modal-body">
        <!-- Image will be inserted here by JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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

<div class="row"></div>
<?php include_once('layouts/footer.php'); ?>

<script>
  $(document).ready(function() {
    // Initialize tooltips for all elements
    $('[data-toggle="tooltip"]').tooltip();

    // Add a click event listener to the product image
    $('.product-image').click(function() {
      var imageUrl = $(this).attr('src');

      // Show a modal with the image
      $('#imageModal').find('.modal-body').html('<img src="' + imageUrl + '" class="img-responsive">');
      $('#imageModal').modal('show');
    });
  });
</script>
