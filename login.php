<?php
session_start();

require_once 'openid.php';

$email = $_REQUEST['email'];

function check_email($email) {
    if (empty($email)) { return ""; }

    try {
        $openid = new OpenID($email);
        $csrf = bin2hex(random_bytes(128 / 8));
        $_SESSION['openid'] = $openid;
        $_SESSION['csrf'] = $csrf;
        $_SESSION['for'] = $_REQUEST['for'];

        header('Location: ' . $openid->authorization_url($csrf));
        exit();
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

$errormsg = check_email($email);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Login</title>
<meta charset="utf-8" />
</head>
<body>
<h1>Login</h1>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
<label for="email">Email: </label><input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email) ?>" />
<?php if (!empty($errormsg)) {?>
<span style="color: red;">* <?php echo htmlspecialchars($errormsg); ?></span>
<?php } ?>
<br />
<input type="submit" value="Login" />
</form>
</body>
</html>

