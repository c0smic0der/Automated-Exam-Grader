<?php
	$response = file_get_contents('php://input');
	$send = json_decode($response,true);
	$field= json_encode($send,true);
	//echo $field;
$curl = curl_init();
 $url="https://web.njit.edu/~smg56/CS490/beta/ExamList.php";

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_POST => 1,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POSTFIELDS => $field
  ));
$resp = curl_exec($curl);
echo json_encode($resp, true);
?>
