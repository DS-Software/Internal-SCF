<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $host = "http://95.217.33.122";
    $port = $_GET['port'];
    $path = $_GET['path'];

    $correct_url = "$host:$port/$path";
    $data = file_get_contents('php://input');

    echo("REQ SENT TO " . $correct_url . "<br>");
    echo("DATA " . $data . "<br>");

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'GET',
            'content' => $data,
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($correct_url, false, $context);
    if ($result === false) {
        echo("ERROR");
    }

    var_dump($result);

    /*$curl = curl_init($correct_url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
    //curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    http_response_code($status);

    echo($json_response);*/
?>