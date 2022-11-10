<?php
require_once 'openid.php';

session_start();

$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>OpenID Connect Demo Application</title>
<meta charset="utf-8" />
<style type="text/css">
	pre {
		white-space: pre-wrap;       /* Since CSS 2.1 */
		white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
		white-space: -pre-wrap;      /* Opera 4-6 */
		white-space: -o-pre-wrap;    /* Opera 7 */
		word-wrap: break-word;       /* Internet Explorer 5.5+ */
	}
</style>
</head>
<body>
<h1>Hello, <?php echo empty($email) ? "Guest" : htmlspecialchars($email) ?>.</h1>
<?php if (!empty($email)) { ?>
<p><a href="logout.php">Logout</a></p>
<hr />
<p>Provider: <?php echo $_SESSION['openid']->provider(); ?></p>
<pre><?php echo $_SESSION['id_token']; ?></pre>
<?php } else { ?>
<p><a href="login.php">Login</a></p>
<?php } ?>
</body>
</html>
