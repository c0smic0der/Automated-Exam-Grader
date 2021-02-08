<?php

	$response = file_get_contents('php://input');
	$s = json_decode($response, true);	
	$send = json_encode($s, true);

	$curl = curl_init();
	
  $url = "https://web.njit.edu/~smg56/CS490/beta/createExam.php";
	  
	  curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_FOLLOWLOCATION => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS => $send
	  ));
	$resp = curl_exec($curl);
	echo json_encode($resp, true);

?>
