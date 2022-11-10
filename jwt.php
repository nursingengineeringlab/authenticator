<?php
function jwt_decode($jwt) {
    $components = explode('.', $jwt);
    $b64string = str_replace('_', '/', str_replace('-', '+', $components[1]));
    $json = base64_decode($b64string);
    return json_decode($json, true);
}
?>
