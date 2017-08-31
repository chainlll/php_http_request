<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require 'http.php';
    $http=new Http();
    
    $url = $_POST['url'];
    $post = $_POST['post'];
    $newcookie = $_POST['newcookie'];
	$arr = array(
		"url" =>  $url,
		"post" => $post,
		"newcookie" => $newcookie
	);
    $response = $http->post($arr);
    echo json_encode($response);
    
?>
