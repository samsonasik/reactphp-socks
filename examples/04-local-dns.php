<?php

use Clue\React\Socks\Client;
use React\Socket\TcpConnector;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

require __DIR__ . '/../vendor/autoload.php';

$proxy = isset($argv[1]) ? $argv[1] : '127.0.0.1:1080';

$loop = React\EventLoop\Factory::create();

// set up DNS server to use (Google's public DNS)
$client = new Client($proxy, new TcpConnector($loop));
$connector = new Connector($loop, array(
    'tcp' => $client,
    'timeout' => 3.0,
    'dns' => '8.8.8.8'
));

echo 'Demo SOCKS client connecting to SOCKS server ' . $proxy . PHP_EOL;

$connector->connect('tls://www.google.com:443')->then(function (ConnectionInterface $stream) {
    echo 'connected' . PHP_EOL;
    $stream->write("GET / HTTP/1.0\r\n\r\n");
    $stream->on('data', function ($data) {
        echo $data;
    });
}, 'printf');

$loop->run();
