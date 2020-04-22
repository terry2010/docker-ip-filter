<?php


while (true) {
    if (file_exists(__DIR__ . "/config/refresh.php")) {
        $config = require(__DIR__ . "/config/refresh.php");

        foreach ($config["prot"] as $k => $v) {
            $oldIdList = findID($config["old_ip"], $config["port"], $v);
            if (count($oldIdList) > 0) {

                foreach ($oldIdList as $k2 => $v2) {

                    if ($v2["ip"] != $config["new_ip"]) {
                        $cmd = "iptables -R DOCKER {$v2["id"]} -p $v -m $v -s {$config["new_ip"]} --dport {$config["port"]} -j ACCEPT";
                        exec($cmd, $output);
                        var_dump("replace:", $cmd, "\n", $output);
                    }

                }
            } else {
                foreach ($config['prot'] as $k => $v) {
                    $newIdList = findID($config["new_ip"], $config["port"], $v);
                    if (count($newIdList) > 0) {

                    } else {
                        $cmd = "iptables -A DOCKER  -p $v -m $v -s {$config["new_ip"]} --dport {$config["port"]} -j ACCEPT";
                        exec($cmd, $output);
                        var_dump("add:", $cmd, "\n", $output);
                    }
                }


            }
        }
//        unlink(__DIR__."/config/refresh.php");
    }
    sleep(1);

}


function findID($ip, $port, $prot)
{

    $list = getRules();
    $ret = array();
    foreach ($list as $k => $v) {
        if ($v[2] == $prot && $v[4] == $ip && $v[7] == "dpt:" . $port) {
            $ret[$v[0]] = array("id" => $v[0], "prot" => $v[2]);
        }
    }
    return $ret;
}


/**
 *
 * Chain DOCKER (1 references)
 * num  target     prot opt source               destination
 * 1    ACCEPT     tcp  --  0.0.0.0/0            172.17.0.2           tcp dpt:9999
 * 2    ACCEPT     udp  --  0.0.0.0/0            172.17.0.2           udp dpt:9999
 * 3    ACCEPT     tcp  --  0.0.0.0/0            172.17.0.3           tcp dpt:9999
 * 4    ACCEPT     udp  --  192.168.1.196        172.17.0.3           udp dpt:9999
 *   array(8) {
 * [0]=>
 * string(1) "4"
 * [1]=>
 * string(6) "ACCEPT"
 * [2]=>
 * string(3) "udp"
 * [3]=>
 * string(2) "--"
 * [4]=>
 * string(9) "192.168.1.196"
 * [5]=>
 * string(10) "172.17.0.3"
 * [6]=>
 * string(3) "udp"
 * [7]=>
 * string(8) "dpt:9999"
 * }
 */
function getRules()
{
    $context = exec("iptables -L DOCKER -n --line-number", $output);
//    var_dump($output);

    foreach ($output as $k => $v) {
        if (strlen($v) > 3) {
            $tmpArr = explode(" ", $v);
            if ((int)$tmpArr[0] == $tmpArr[0] && (int)$tmpArr[0] > 0) {
                $tmpCLeanArr = array();
                foreach ($tmpArr as $k1 => $v1) {
                    if (strlen($v1) > 0) {
                        $tmpCLeanArr[] = $v1;
                    }
                }
                $ret[] = $tmpCLeanArr;
            }
        }
    }
    return $ret;
}