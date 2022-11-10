<?php
require_once 'openid.php';
require_once 'jwt.php';

if (!isset($_COOKIE[session_name()])) {
    // Session cookie missing, try recovery.
    $repost = $_POST;
    array_walk($repost, function(&$value, $key) { $value = "{$key}={$value}"; });
    header('Location: auth.php?' . implode('&', $repost));
    exit();
}

session_start();

$csrf = $_SESSION['csrf'];
$csrf_check = $_REQUEST['state'];

// Validate CSRF
if ($csrf != $csrf_check) {
    http_response_code(400);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>400 Bad Request</title>
<meta charset="utf=8" />
</head>
<body>
<h1>400 Bad Request</h1>
<p>CSRF Token Mismatch. Expected <?php echo $csrf_check; ?> get <?php echo $csrf; ?></p>
</body>
</html>
<?php
    exit();
}

// Get the long term tokens

// Microsoft gives us JWT tokens directly.
$openid = $_SESSION['openid'];
$code = $_REQUEST['code'];
$jwt_token = $_REQUEST['id_token'];

if (empty($jwt_token) && !empty($code)) {
    // Google requires an additional step to convert the code.
    $jwt_token = $openid->code2token($code);
}

$jwt = jwt_decode($jwt_token);
$email = $jwt['email'];

if (!$openid->check_email($email)) {
    if (empty($_SESSION['login-recovery']) && !empty($openid->recovery())) {
        $_SESSION['login-recovery'] = true;
        header('Location: ' . $openid->authorization_url($csrf, $openid->recovery()));
        exit();
    } else {
        http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>403 Authorization Required</title>
<meta charset="utf=8" />
</head>
<body>
<h1>403 Authorization Required</h1>
<p>Authorization failed. <a href="login.php">Please retry.</a></p>
</body>
</html>
<?php
        exit();
    }
}

$_SESSION['id_token'] = $jwt_token;
$_SESSION['email'] = $email;

$for = $_SESSION['for'];
$target = $GLOBALS['config']['for'][$for];
if (empty($target)) {
    header('Location: index.php');
} else {
    header('Location: ' . $target . '?email=' . $email . '&provider=' . $openid->provider() . '&id_token=' . $jwt_token);
}
exit();
?>
