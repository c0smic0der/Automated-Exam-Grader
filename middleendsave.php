<?PHP

$response = file_get_contents('php://input');

$decode = json_decode($response, true);

$num_of_q = count($decode);
$username = $decode[0]['username'];
$examname= $decode[0]['examname'];

//echo $decode;

function loop($studentcode){
  if (strpos($studentcode, "for") !== false){
    //echo "Contains for loop, correct. ";
    return 0;

  }
  else{
    //echo "No for loop, incorrect. ";
    return -2;
  }
}


function colon(&$studentcode){
  if (preg_match('/(.*?)\n/', $studentcode, $match1) == 1) //grabs value between ) and \n
  {
      $colon = $match1[1];
      if(strpos($colon, ":") !== false){
          //echo "Correct colon. ";
          return 0;
      }
      else{
        $correctedcolon= str_replace(")", "):", $colon);
        $studentcode= str_replace($colon, $correctedcolon, $studentcode);
        //echo "Incorrect colon. ";
        return -2;

      }
  }
  else{
    return 0;
  }
}

//checks function name
function getFunctionName($functionname,&$studentcode, $question){


  if (preg_match('/def (.*?)\(/', $studentcode, $match2) == 1) //function name in students answer
  {
      $studentfunctionname = $match2[1];
  }

  if($studentfunctionname == $functionname){
      //echo "Correct function name. ";
      return 0;
  }
  else{
      $studentcode = str_replace($studentfunctionname, $functionname, $studentcode); //replace with correct function name
      //echo "Wrong function name. ";
      return -2;
  }
}

function executeCode($answer,$studentcode,$testcase,$printconstraint,$functionname,$question, &$printpoints){
  $casepoints = array();
  $count = 1;
  $filename = 'pythonexecutable.py';
  //$shebang = "#!/usr/bin/env python3";
  //file_put_contents($filename, $shebang."\n");



  $iterator = new MultipleIterator;
  $iterator->attachIterator(new ArrayIterator($testcase));
  $iterator->attachIterator(new ArrayIterator($answer));


  //case[0] is each testcase, case[1] is each correct answer for that respective testcase
  foreach($iterator as $case){
    $execresult = array();
    //$execresult = [];
    //echo $case[0];
    //put students correct code into file
    file_put_contents($filename, $studentcode."\n");
    //append to file with function call and test case, then execute the code
    file_put_contents($filename, "print(".$functionname."(".$case[0]."))" , FILE_APPEND);


    //code execution
    //local path /Users/Shiv/Desktop/gradetester/pythonexecutable.py
    //njit full path /afs/cad.njit.edu/u/s/s/ss3359/public_html/pythonexecutable.py
    exec('python /afs/cad.njit.edu/u/s/s/ss3359/public_html/pythonexecutable.py', $execresult);

    //echo "-111111-".$execresult[0]. "-111111-";
    //exec[0] is the output, exec[1] is 'None' or NULL, depending on if the student prints or returns correctly
    if ($printconstraint == "1"){
      if ($execresult[0] == $case[1]){ //output check
        //echo json_encode($execresult[0], true);

        //echo "correct output. ";
        $casepoints += ["testcase".$count => 0];
        $count +=1;
      }
      else{
        //echo json_encode($execresult[0], true);
        //echo "wrong output. ";
        $casepoints += ["testcase".$count => -5];
        $count +=1;
      }

      if ($execresult[1] != "None"){ //print constraint on -> student returns
        $printpoints = -2;
      }

    }
    else{
      if ($execresult[1] == "None"){ //print constraint off -> student prints
        echo json_encode($execresult[1]);
        $printpoints = -2;
      }
      if ($execresult[0] == $case[1]){ //output
        //echo json_encode($execresult[0], true);
        //echo "correct output. ";
        $casepoints += ["testcase".$count => 0];
        $count +=1;
      }
      else{
        //echo json_encode($execresult[0], true);
        //echo "wrong output. ";
        $casepoints += ["testcase".$count => -5];
        $count +=1;
      }

    }
  }
  //array_push($casepoints, $printpoints); //last number in array is $printpoints
  return $casepoints;
  //var_dump($execresult)."----";
  //echo $answer;
}



for($i = 1; $i<$num_of_q; $i++){

$printpoints = 0;

$qid = $decode[$i]['ID'];
$studentcode = $decode[$i]['answer_body'];




$originalcode = $studentcode;

$qid1 = array("ID"=>$qid);
$data = json_encode($qid1,true);

//point system:
//-5 for each wrong test case
//-3 for anything else wrong

$ch = curl_init();
$url = "https://web.njit.edu/~smg56/CS490/beta/caseMiddle.php";

curl_setopt_array($ch, array(

    CURLOPT_URL => $url,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
  ));

$result = curl_exec($ch);

curl_close($ch);
$dc = json_decode($result, true);

$forloopconstraint = $dc['forc'];
$printconstraint = $dc['printc'];
$question = $dc['question'];

$testcase = array();
$answer = array();

if(!($dc['case1']== "")){

    $a1 = array("case1" => $dc['case1']);
    $b1 = array("answer1" => $dc['ans1']);
    $testcase = array_merge($testcase, $a1);
    $answer = array_merge($answer, $b1);
}
if(!($dc['case2']== "")){
  $a2 = array("case2" => $dc['case2']);
  $b2 = array("answer2" => $dc['ans2']);
  $testcase = array_merge($testcase, $a2);
  $answer = array_merge($answer, $b2);
}
if(!($dc['case3']== "")){
  $a3 = array("case3" => $dc['case3']);
  $b3 = array("answer3" => $dc['ans3']);
  $testcase = array_merge($testcase, $a3);
  $answer = array_merge($answer, $b3);
}
if(!($dc['case4']== "")){
  $a4 = array("case4" => $dc['case4']);
  $b4 = array("answer4" => $dc['ans4']);
  $testcase = array_merge($testcase, $a4);
  $answer = array_merge($answer, $b4);
}

/*//these cases are comin in as NULL for some reason
$testcase = array("case1" => $dc['case1'],
							    "case2" => $dc['case2'],
							    "case3" => $dc['case3'],
							    "case4" => $dc['case4']);

$answer = array("answer1" => $dc['ans1'],
              	"answer2" => $dc['ans2'],
              	"answer3" => $dc['ans3'],
              	"answer4" => $dc['ans4']);

*/

echo json_encode($testcase, true);
echo json_encode($answer, true);


$totalpoints = $dc['points'];
$totalpoints = (int)$totalpoints;

$looppoints = 0;
$printorreturnpoints = 0;
$colonpoints = 0;
$functionnamepoints = 0;
$execpoints = 0;

if (preg_match('/named (.*?) /', $question, $match) == 1) //correct function name, taken from question
{
    $functionname = $match[1];
}

if (strpos($functionname, "()") !== false){ //if the function name from question contains (), then remove the ()

  $functionname = str_replace("()","",$functionname);

}


//must execute in this order
$functionnamepoints = getFunctionName($functionname,$studentcode, $question);
$colonpoints = colon($studentcode);
if ($forloopconstraint == "1"){
  $looppoints = loop($studentcode);
}

echo json_encode($looppoints."LOOP");
$execpoints = executeCode($answer,$studentcode,$testcase,$printconstraint,$functionname,$question,$printpoints); //last number in this array is printpoints

echo json_encode($execpoints, true);

$printorreturnpoints = $printpoints; //putting the last element (printpoints) in variable, so that its only the points given for each testcase
$totalexecpoints = array_sum($execpoints);

echo json_encode($printorreturnpoints."PRINT");
//keep $totalpoints on the right side because it holds the number of points given by the user, its not 0
$totalpoints = $totalpoints + $functionnamepoints + $colonpoints + $looppoints + $printorreturnpoints +$totalexecpoints;

echo json_encode($totalpoints);
if($totalpoints < 0){
  $totalpoints =0;
}

$exampoints = array(
                      "username" => $username,
                      "answer_body" => $originalcode,
                      "ID" => $qid,
                      "examname" => $examname,
                      "functionnamepoints" => $functionnamepoints,
											"colonpoints" => $colonpoints,
											"looppoints" => $looppoints,
											"printpoints"=>$printorreturnpoints,
                      "totalpoints" => $totalpoints,
                      "Taken" => 1);
											//"execpoints" => $execpoints,
											//"totalpoints" => $totalpoints);


$exampoints += $execpoints;

echo json_encode($exampoints, true);

$data1 =json_encode($exampoints, true);

$ch1 = curl_init();
$url1 = "https://web.njit.edu/~smg56/CS490/beta/studentAnswers.php";

curl_setopt_array($ch1, array(

    CURLOPT_URL => $url1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $data1,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
  ));

$result1 = curl_exec($ch1);

curl_close($ch1);
$dc1 = json_decode($result1, true);

$dc2 = json_encode($dc1, true);

echo $dc2;

//sleep(3);
}



?>
