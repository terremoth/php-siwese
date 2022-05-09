<?php

function error($socket, $line): string
{
    socket_shutdown($socket);
    socket_close($socket);
    return '[Line: '.$line.'] '.socket_strerror(socket_last_error($socket)).PHP_EOL;
}

$host = "127.0.0.1";
$port = 8080;

set_time_limit(5);

while(1) {

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die(error($socket, __LINE__));

    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 2048);

    $result = socket_bind($socket, $host, $port) or die(error($socket, __LINE__));

    $result = socket_listen($socket) or die(error($socket, __LINE__));

    $spawn = socket_accept($socket) or die(error($socket, __LINE__));

    $input = socket_read($spawn, 2048) or die(error($socket, __LINE__));

    $input = PHP_EOL.trim($input);

    echo $input . PHP_EOL;

    $http =
        "HTTP/1.1 200 OK\r\n".
        "content-type: text/html; charset: utf-8\r\n".
        "server: SIWESE\r\n".
        "connection: keep-alive\r\n".
        "x-frame-options: SAMEORIGIN\r\n".
        "\r\n";

    $output = $http."<!DOCTYPE html><html lang=\"en\"><h1>Hello World!</h1></html>\r\n";

    socket_write($spawn, $output, strlen($output)) or die(error($socket, __LINE__));

    // close sockets
    socket_shutdown($spawn);
    socket_shutdown($socket);
    socket_close($spawn);
    socket_close($socket);
}

