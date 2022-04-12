<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
session_start();
	if(!$_SESSION["auth"])
		header("Location: /");
	
	$servername = "localhost";
	$username = "root";
	$password = "10020808";
	$dbname = "wa";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	mysqli_set_charset($conn,"utf8");
	$template = "";
	$names = "<option value='new'>---- NEW DATA ----</option>";
	$sql = "SELECT * FROM series";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$counter = 1;
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
		if( $row["status"] == 0 ){ $status = "Stop!!!"; }
		else if($row["status"] == 1){ $status = "Sending";}
		else {$status = "Done";}
		$template .= "<tr id='".$row["id"]."'>";
		$template .= "<th>".$counter."</th>";
		$template .= "<th id='name_".$row["id"]."'>".$row['name']."</th>";
		$template .= "<th id='total_".$row["id"]."'>".$row['total']."</th>";
		$template .= "<th id='send_".$row["id"]."'>".$row['send']."</th>";
		$template .= "<th id='faileds_".$row["id"]."'>".$row['faileds']."</th>";
		$template .= "<th id='status_".$row["id"]."'>".$status."</th>";
		$template .= "</tr>";
		$names  .= "<option value='".$row["id"]."'>".$row["name"]."</option>";
		$counter++;
		}
	}
?>
<!doctype html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>syd whatsapp sender</title>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Bootstrap multicolumn form</title>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
		<link rel="shortcut icon" href="#">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});

    </script>

    <style>
        #content {
            padding: 50px;
        }

		.d-none{display : none}
        .progress {

            overflow: hidden;
            margin-top: 10px;
            margin-bottom: 10px;
            background-color: whitesmoke;
            height: 10px;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            -ms-border-radius: 8px;
            -o-border-radius: 8px;
            border-radius: 8px;
            background: #eee;
            -webkit-box-shadow: 0 1px 0 white, 0 0px 0 1px rgba(0, 0, 0, 0.1) inset, 0 1px 4px rgba(0, 0, 0, 0.2) inset;
            box-shadow: 0 1px 0 white, 0 0px 0 1px rgba(0, 0, 0, 0.1) inset, 0 1px 4px rgba(0, 0, 0, 0.2) inset;
        }
    </style>
</head>

<body>





<div class="row" style="margin:50px 0 0 50px">
<div class="col-md-2">
<a target="_blank" class="btn btn-success" href="send.php">New</a>
</div>
</div>
<div class="row" id="content">
    <div class="col-md-12">
        <table class="table table-striped custab">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Total</th>
					<th>Success</th>
					<th>Failed</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody id="table_body">
			<?php echo $template; ?>
			</tbody>
		</table>
    </div>

</div>



</body>
</html>

