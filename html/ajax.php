<?php
	include_once dirname(__FILE__) . '/PHPExcel/PHPExcel/IOFactory.php';
	$err = "";
	$stop = false;
	$new_row = "";	

	$servername = "localhost";
	$username = "root";
	$password = "10020808";
	$dbname = "wa";
	$conn = new mysqli($servername, $username, $password, $dbname);
	mysqli_set_charset($conn,"utf8");
	if ($conn->connect_error !== null) {
		$err = "Connection failed: " . $conn->connect_error;
		$stop = 1;
	}


	function clean($data)
	{
	    $data = htmlspecialchars($data);
	    $data = stripslashes($data);
	    $data = trim($data);
	    return $data;
	}

	
	function encode_base64($file){
		$data = file_get_contents($file);
		return base64_encode($data);
	}
	function sendFile ($mobile , $message, $file , $token){
		$url = "https://api.wallmessage.com/api/sendFile?token={$token}";
		$fileData = encode_base64($file['tmp_name']);
		$ch = curl_init($url);
		// Check if initialization had gone wrong
		if ($ch == false) {
			return 'failed to initialize';
		}
		$data = array(
			'mobile' => "{$mobile}",
			'caption' => "{$message}",
			'mimeType' => "{$file['type']}",
			'fileName' => "{$file['name']}",
			'fileData' => "{$fileData}"
		);
		$payload = json_encode($data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_CAINFO, "path/to/cacert.pem");
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	
	function sendMessage ($mobile , $message , $token){
		$url = "https://api.wallmessage.com/api/sendMessage?token={$token}";
		$ch = curl_init($url);
		// Check if initialization had gone wrong
		if ($ch == false) {
			return 'failed to initialize';
		}
		$data = array(
			'mobile' => "{$mobile}",
			'message' => "{$message}"
		);
		$payload = json_encode($data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_CAINFO, "path/to/cacert.pem");
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	if(isset($_POST['action']) && $_POST['action'] === 'send'){
		$id = clean($_POST["id"]);
		$file = !empty($_FILES['message_file']) ? $_FILES['message_file'] : 0;
//		$message = trim(preg_replace('/\s\s+/', ' ', $_POST["message"]));
$message = $_POST['message'];
		if($file === 0)
			$send_res = sendMessage(clean($_POST['number']) ,$message ,clean( $_POST['token']));
		else{
			$send_res = sendFile(clean($_POST['number']) , $message , $file , clean($_POST['token']));
			if ($file["type"] === 'application/pdf')
				$send_res2 = sendMessage(clean($_POST['number']) ,$message ,clean( $_POST['token']));
		}
		$send_arr = json_decode($send_res, true);

		switch($send_arr['message']){
			case "Mobile number is not registered":
				$sql = "UPDATE series SET faileds = faileds + 1 WHERE id = '".$id."'";
				break;
			case "Invalid mobile number!":
				$sql = "UPDATE series SET faileds = faileds + 1 WHERE id = '".$id."'";
				break;
			case "Success":
				$sql = "UPDATE series SET send = send + 1 WHERE id = '".$id."'";
				break;
			default:
				$stop = true;
				$err = $send_arr["message"];
				$sql = "UPDATE series SET status = 0 WHERE id = '".$id."'";
		}
		$conn->query($sql);
//		if($send_arr['isSuccess'] === false && ($send_arr['message'] === 'Your instance is offiline,try login first!' || $send_arr['message'] === 'Your instance is not connected,current instance state is : PAIRING!' || $send_arr["message"] === 'Error: Evaluation failed: g'  || $send_arr["message"] === 'Error: Evaluation failed: N' || $send_arr["message"] === "Your instance is not connectd,current instance state is : TIMEOUT!")){
//			$stop = true;
//			$err = $send_arr['message'];
//			$sql = "UPDATE series SET status = 0 WHERE id = '".$id."'";
//			$conn->query($sql);
//		}else if($send_arr['isSuccess'] == true){
//			$sql = "UPDATE series SET send = send + 1 WHERE id = '".$id."'";
//			$conn->query($sql);
//		}else{
//			$sql = "UPDATE series SET faileds = faileds + 1 WHERE id = '".$id."'";
//			$conn->query($sql);
//		}
		$response = array(
           		'err' => $err,
			'stop' => $stop,
			'send_result' => $send_arr
        	 );
	        echo json_encode($response);
	}
	if(isset($_POST['action']) && $_POST['action'] === 'done'){
		$id = clean($_POST["id"]);
		$sql = "UPDATE series SET status = 2 WHERE id = '".$id."'";
		$conn->query($sql);
		$response = array(
           		'err' => $err,
			'stop' => $stop,
'res_Send' => $send_arr
        	 );
	        echo json_encode($response);
	}

    if(isset($_POST['action']) && $_POST['action'] === 'load_data'){
		$selected_serie = clean($_POST['selected_serie']);
		$name = clean($_POST['name']);
		$token = clean($_POST['token']);
		$test = (int)clean($_POST['test']);
		$test = !empty($test) ? $test : 0;
		if(isset($_POST['test_number']))
			$test_number = clean($_POST['test_number']);
		$start = (int)clean($_POST['start']);
		$start = !empty($start) ? $start : 0;
		$text = $_POST['text'];
		$message_file = $_FILES['message_file'];
		$data = [];



			if($selected_serie === "new" && !$stop){

					try {
						//Load the excel(.xls/.xlsx) file
						$objPHPExcel = PHPExcel_IOFactory::load($_FILES["data_file"]['tmp_name']);
					} catch (Exception $e) {
						$stop = true;
						$err = 'Error loading file "' . pathinfo($_FILES["data_file"]['name'], PATHINFO_BASENAME). '": ' . $e->getMessage();
					}
					$data_arr = array();
					$sheet = $objPHPExcel->getSheet(0);
					$total_rows = $sheet->getHighestRow();
					$total_columns = $sheet->getHighestColumn();
					$i = 0;
					for($row =2; $row <= $total_rows; $row++) {
						$single_row = $sheet->rangeToArray('A' . $row . ':A' . $row, NULL, TRUE, FALSE);
						foreach($single_row[0] as $key=>$value) {
							$data_arr[$i] = $value;
						}
						$i++;
					}
					$data_json = json_encode($data_arr , JSON_UNESCAPED_UNICODE );
					$data['total'] = !empty($data_arr) ? count($data_arr) : 0;
					$data['numbers'] = $data_arr;
					$sql = "SELECT * FROM series WHERE name = '".$name."'";
					$result = $conn->query($sql);
					if ($result->num_rows === 0){
						$sql = "INSERT INTO `series` (`id`, `name`, `total`, `send`, `faileds`, `numbers`, `message`) VALUES (NULL, '".$name."', '".count($data_arr)."', '0', '0', '".$data_json."', '".$text."');";
						$data['start'] = 1;
						$data['text'] = $text;
						$result = $conn->query($sql);
						$data['id'] = $conn->insert_id;
					}
					else{
						$row = $result->fetch_assoc();
						$sql = "UPDATE `series` SET name = '".$name."', numbers='".$data_json."', message='".$text."'  WHERE id = ".$row['id'];
						$data['start'] = (((int)$row['send'] + (int)$row['faileds'] + 1) < count($data_arr)) ? ((int)$row['send'] + (int)$row['faileds'] + 1) : 1;
						$data['text'] = $text;
						$result = $conn->query($sql);
						$data['id'] = $row['id'];
					}
					$conn->close();
			}else{
					$sql = "SELECT * FROM series WHERE id = ".$selected_serie;
					$result = $conn->query($sql);
					$row = $result->fetch_assoc();
					$data['numbers'] = json_decode($row['numbers'], true);
					$data['start'] = (int)$row['send'] + (int)$row['faileds'] + 1;
					$data['total'] = count(json_decode($row['numbers']));
					$data['message'] = $row['message'];
					$data['id'] = $row['id'];
			}
		if(isset($_POST['test_number']) && $test === 1){
			$data['total'] = 1;
			$data['numbers'] = [$test_number];
			$data['start'] = 1;
		}


		$response = array(
           		'err' => $err,
			'stop' => $stop,
			'data' => $data
        	 );
	        echo json_encode($response);

    }

?>

