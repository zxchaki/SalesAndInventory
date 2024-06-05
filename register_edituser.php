<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

if (isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Instantiation and passing true enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output

        // Send using SMTP
        $mail->isSMTP();

        // Set the SMTP server to send through
        $mail->Host = 'smtp.gmail.com';

        // Enable SMTP authentication
        $mail->SMTPAuth = true;

        // SMTP username
        $mail->Username = 'izakhaleyhernandez@gmail.com'; // Replace with your email address

        // SMTP password
        $mail->Password = 'ilzw jkzg lsvz gyvo'; // Replace with your app-specific password

        // Enable TLS encryption;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // TCP port to connect to, use 465 for PHPMailer::ENCRYPTION_SMTPS above
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('izakhaleyhernandez@gmail.com', 'candy');

        // Add a recipient
        $mail->addAddress($email, $name);

        // Set email format to HTML
        $mail->isHTML(true);

        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $mail->Subject = 'Email verification';
        $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';

        $mail->send();
        // echo 'Message has been sent';

        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

        // Connect with database
        $conn = mysqli_connect("localhost", "root", "", "izak");

        // Insert in users table
        $sql = "INSERT INTO pogi(name, email, password, verification_code, email_verified_at) VALUES ('" . $name . "', '" . $email . "', '" . $encrypted_password . "', '" . $verification_code . "', NULL)";
        mysqli_query($conn, $sql);

        header("Location: otp.php?id=" . $_GET['id'] . "&email=" . $email);
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>


<form method="POST">


    <label for="email">Email:</label>
    <select name="email" id="email" required>
        <option value="izakhaleyhernandez@gmail.com">izakhaleyhernandez@gmail.com</option>

    </select> <br>



    <input type="submit" name="register" value="VERIFY EMAIL">
</form>