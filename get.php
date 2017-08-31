<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require 'http.php';
    $http=new Http();
    
    $url = $_POST['url'];
    $get = $_POST['get'];
    $newcookie = $_POST['newcookie'];
    $arr = array(
        "url" => $url,
        "get" => $get,
        "newcookie" => $newcookie
    );
    
    $response = $http->get($arr);
    echo json_encode($response);
    
?>
