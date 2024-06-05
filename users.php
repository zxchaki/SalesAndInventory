<?php
  $page_title = 'All User';
  require_once('includes/load.php');
  
?>
<?php
// Checkin What level user has permission to view this page
 page_require_level(1);
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
//pull out all user form database
 $all_users = find_all_user();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix"style="background-image: linear-gradient(to right, #FFC0E4, #FFAED7, #FF9CCB, #FF89BF, #FF77B3, #FF64A8, #FF519C, #FF3E90); color:black">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Users</span>
       </strong>
         <a href="add_user.php" class="btn btn-info pull-right" style="background-color: #525EDE;" onmouseover="this.style.backgroundColor='#3175B8';" onmouseout="this.style.backgroundColor='#525EDE';">
         <span class="glyphicon glyphicon-plus"></span> Add New User
         </a>
      </div>
     <div class="panel-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px;">#</th>
            <th>Name </th>
            <th>Username</th>
            <th class="text-center" style="width: 15%;">User Role</th>
            <th class="text-center" style="width: 10%;">Status</th>
            <th style="width: 20%;">Last Login</th>
            <th class="text-center" style="width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($all_users as $a_user): ?>
          <tr>
           <td class="text-center"><?php echo count_id();?></td>
           <td><?php echo remove_junk(ucwords($a_user['name']))?></td>
           <td><?php echo remove_junk(ucwords($a_user['username']))?></td>
           <td class="text-center"><?php echo remove_junk(ucwords($a_user['group_name']))?></td>
           <td class="text-center">
           <?php if($a_user['status'] === '1'): ?>
            <span class="label label-success"><?php echo "Active"; ?></span>
          <?php else: ?>
            <span class="label label-danger"><?php echo "Deactive"; ?></span>
          <?php endif;?>
           </td>
           <td><?php echo read_date($a_user['last_login'])?></td>
           <td class="text-center">
             <div class="btn-group">
                <a href="register_edituser.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                  <i class="glyphicon glyphicon-pencil"></i>
               </a>
               <!-- Confirmation Before Deletion-->
                <a href="register_deleteuser.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove" onclick="return confirm('Are you sure you want to delete this user?');">
                  <i class="glyphicon glyphicon-remove"></i>
                </a>
                </div>
           </td>
          </tr>
        <?php endforeach;?>
       </tbody>
     </table>
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
