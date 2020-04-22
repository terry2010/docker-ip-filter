<?php

function getIps()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $IP = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $IP = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $IP = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $IP = $_SERVER['REMOTE_ADDR'];
    }
    return $IP ? $IP : "unknow";
}

$demoSubConfig["ip"] = $ip;
$demoSubConfig["dockerID"] = array(1, 2);

$ip = getIps();

$id = @$_GET['id'];

if ($id != "home" && $id != "work") {
    echo $ip;
    return;
}

$config = inlcude(__DIR__ . "/config/data.php");

if (isset($config[$id])) {
    if ($config[$id]["new_ip"] != $ip) {
        $config[$id]["old_ip"] = $config[$id]["new_ip"];
        $config[$id]["new_ip"] = $ip;
        file_put_contents(__DIR__ . "/data.php", "<?php return " . var_export($config) . ";?>", 0777);
        file_put_contents(__DIR__ . "/refresh.php", "<?php return " . var_export($config[$id]) . ";?>", 0777);
    }
    echo $id . ":" . $ip;
} else {
    echo $ip;
    return;
}


