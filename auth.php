<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username', 'password');
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);
//Check if both username and password are numeric
if (is_numeric($username) && is_numeric($password)) {
    $session->msg("d", "Username and password is incorrect");
    redirect("index.php", false);
}
if (!preg_match('/^[a-zA-Z]+$/', $username)) {
    $session->msg("d", "Username can only contain alphabetical characters.");
    redirect('index.php', false);
}
if (empty($errors)) {
    $user = find_by_username($username);

    if ($user) {
        // User found, check password
        if (authenticate($username, $password)) {
            // Password is correct, proceed with login
            // Create session with id
            $session->login($user['id']);
            // Update Sign in time
            updateLastLogIn($user['id']);
            // Reset session variables for input fields
            unset($_SESSION['input_username']);
            unset($_SESSION['input_password']);
            $session->msg("s", "Welcome to Inventory Management System");
            redirect('admin.php', false);
        } else {
            // Password is incorrect
            // Set session variable for input password
            $_SESSION['input_username'] = $username;
            unset($_SESSION['input_password']);
            $session->msg("d", "Incorrect password.");
            redirect('index.php', false);
        }
    } else {
        // User not found
        // Set session variable for input username
        $_SESSION['input_password'] = $password;
        unset($_SESSION['input_username']);
        $session->msg("d", "Username not found.");
        redirect('index.php', false);
    }
} else {
    // Validation errors
    $session->msg("d", $errors);
    redirect('index.php', false);
}
?>