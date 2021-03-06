<?php

if (!extension_loaded('redis')) {
    die("redis extension not loaded\n");
};

$opt = getopt('h:p:s:n:');

$host = isset($opt['h']) ? $opt['h'] : '127.0.0.1';
$port = isset($opt['p']) ? $opt['p'] : 6379;
$socket = isset($opt['s']) ? $opt['s'] : null;
$dbnum = isset($opt['n']) ? $opt['n'] : 0;

$redis = new Redis();

try {
    $socket && $redis->connect($socket) || $redis->connect($host, $port);
    $redis->select($dbnum);
} catch (Exception $e) {
    die("cannot connect to redis\n");
};

$input = fopen('php://stdin', 'r');

while(!feof($input)) {
    fscanf($input, "%s %d %s\n", $key, $ttl, $value);
    $redis->restore($key, $ttl, hex2bin($value));
};

$redis->close();

