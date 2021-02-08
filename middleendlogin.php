<?php
$response = file_get_contents('php://input');
$data = json_decode($response,true);

$data1 = json_encode($data,true);
$ch = curl_init();
$url = "https://web.njit.edu/~smg56/CS490/Instructorlogin.php";

curl_setopt_array($ch, array(

    CURLOPT_URL => $url,
    CURLOPT_POST => 1,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POSTFIELDS => $data1,
  ));
$DBlogin = curl_exec($ch);
$S = json_decode($DBlogin,true);
$pro = $S['response'];
if ($pro == "Accepts"){
  $S = array();
  $S = array("login" => "teacher");
  echo json_encode($S,true);
}
else {

$ch = curl_init();
$url = "https://web.njit.edu/~smg56/CS490/Studentlogin.php";
  curl_setopt_array($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_POST => 1,
      CURLOPT_FOLLOWLOCATION => 1,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_POSTFIELDS => $data1,
    ));


$DBlogin = curl_exec($ch);
$S = json_decode($DBlogin,true);
$stu = $S['response'];

if($stu == "Accepts"){
  $S = array();
  $S = array("login" => "student");
  echo json_encode($S,true);
}
else
{
  $S = array();
  $S = array("login" => "failed");

  echo json_encode($S);
}
}

?>
