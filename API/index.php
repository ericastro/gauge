<?php
	header('Content-Type: application/json; charset=UTF-8');
	$json_file		= file_get_contents("../brands.json");
	$json_str		= json_encode($json_file);
	echo "$json_str";
?>
