<?php
    $host = "http://95.217.33.122";
    $port = $_GET['port'];
    $path = $_GET['path'];

    $correct_url = "$host:$port/$path";
    $data = file_get_contents('php://input');

    $curl = curl_init($correct_url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    http_response_code($status);

    echo($json_response);
?>