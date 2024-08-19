<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(null);
if (
    isset($_GET['host'])
    && !$_GET['host'] == null
    && isset($_GET['port'])
    && !$_GET['port'] == null
    && isset($_GET['username'])
    && !$_GET['username'] == null
    && isset($_GET['name'])
    && !$_GET['name'] == null
) {
    $hostname = $_GET['host'];
    $port = (int) $_GET['port'];
    $username = $_GET['username'];
    $password = $_GET['password'];
    $name = $_GET['name'];
    $waitTimeoutInSeconds = 1;

    try {
        if (isValidIP($hostname)) {
            try {
                $fp = fsockopen($hostname, $port, $errCode, $errStr, $waitTimeoutInSeconds);
            } catch (Exception $e) {
                exit($e->getMessage());
            }
            if ($fp) {
                $conn = new mysqli($hostname, $username, $password, $name, $port);

                if ($conn->connect_error) {
                    exit($conn->connect_error);
                } else {
                    echo 'OK';
                }
                $conn->close();
            } else {
                exit('Failed to ping: ' . $hostname . ':' . $port);
            }

            exit;
        } else {
            exit('Please provide an valid ipv4/ipv6!');
        }
    } catch (Exception $e) {
        exit($e->getMessage());
    }
} else {
    exit('You are missing the required connection details');
}

function isValidIP($ip)
{
    // IPv4 pattern
    $ipv4_pattern = '/^(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})){3}$/';

    // IPv6 pattern (optional, you can skip this if not needed)
    $ipv6_pattern = '/^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/';

    return preg_match($ipv4_pattern, $ip) || preg_match($ipv6_pattern, $ip);
}
