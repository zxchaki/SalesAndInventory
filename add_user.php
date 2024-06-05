<?php
$page_title = 'Add User';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(1);
$groups = find_all('user_groups');
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
if (isset($_POST['add_user'])) {

  $req_fields = array('full-name', 'username', 'password', 'level');
  validate_fields($req_fields);

  if (empty($errors)) {
    $name   = remove_junk($db->escape($_POST['full-name']));
    $username   = remove_junk($db->escape($_POST['username']));
    $password   = remove_junk($db->escape($_POST['password']));
    $user_level = (int)$db->escape($_POST['level']);
    $password = sha1($password);
    $query = "INSERT INTO users (";
    $query .= "name,username,password,user_level,status";
    $query .= ") VALUES (";
    $query .= " '{$name}', '{$username}', '{$password}', '{$user_level}','1'";
    $query .= ")";
    if ($db->query($query)) {
      //sucess
      $session->msg('s', "User account has been created! ");
      redirect('add_user.php', false);
    } else {
      //failed
      $session->msg('d', ' Sorry failed to create account!');
      redirect('add_user.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('add_user.php', false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>
<?php echo display_msg($msg); ?>
<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading" style="background-image: linear-gradient(to right, #FFC0E4, #FFAED7, #FF9CCB, #FF89BF, #FF77B3, #FF64A8, #FF519C, #FF3E90); color:black" >
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>Add New User</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-6">
        <form method="post" action="add_user.php">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="full-name" placeholder="Full Name" oninput="allowLettersOnly(event)">
          </div>
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" oninput="allowLettersOnly(event)">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
          </div>
          <div class="form-group">
            <label for="level">User Role</label>
            <select class="form-control" name="level">
              <?php foreach ($groups as $group) : ?>
                <option value="<?php echo $group['group_level']; ?>"><?php echo ucwords($group['group_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="add_user" class="btn btn-primary">
              <span class="glyphicon glyphicon-plus-sign"></span> Add User
            </button>
          </div>
        </form>
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