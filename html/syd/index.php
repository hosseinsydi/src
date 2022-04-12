<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
session_start();


	$servername = "localhost";
	$username = "root";
	$password = "10020808";
	$dbname = "wa";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	$names = "<option value='new'>---- NEW DATA ----</option>";
	$sql = "SELECT * FROM series";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$counter = 1;
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
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
        $(document).ready(function () {
			$("#textinput").attr('disabled', true);
			$('input[type=radio][name=test]').change(function () {
				$("input[name=test_number]").prop("disabled",function(i,v){return !v;});
			});



$("#selected_serie").on("change",function(){
	var selected = $(this).find(":selected").attr("value");
	if (selected === "new") {
		 $("input[name=name]").val("").attr("disabled",false);
		 $("textarea").val("").attr("disabled",false);
		$("form")[0].reset();
		 $("input[name=test_number]").val("").attr("disabled",true);
		$("input[type=file]").attr("disabled",false)
	}
	else {
		 $("input[name=name]").val("").attr("disabled",true);
		 $("input[type=file]").val("").attr("disabled",true);
		$("select").not("#selected_serie").not("select[name=token]").attr("disabled",true);
	}
});


	var numbers = [];
	var numbers_count = 0;
        $("#loadBtn").click(function (e) {
                e.preventDefault();
		var sendForm = false;
		if ($("#selected_serie").find(":selected").attr("value") === "new")
			if ($("input[name=name]").val() === "" || $("input[name=start]").val() === "" || $("textarea").val() === "")
				alert("PLEASE FILL REQUIRED FIELD WITH RED START");
			else if ($("#selectfile").prop('files').length == 0  ||  $("#selectfile").prop('files')[0].name.split('.').pop() !== "xlsx")
				alert("PLEASE SELECT DATA FILE (Excel)");
			else
				sendForm = true;
		else
			sendForm = true;
		if(sendForm){
	                var form = $("form")[0];
	                var inputData = new FormData(form);
			if ($("#selected_serie").find(":selected").attr("value") !== "new")
				inputData.append('action' , 'load_data');

//for(var val of inputData.values()){console.log(val)}

			$.ajax('ajax.php',
	                    {
	                        type: 'POST',
	                        enctype: 'multipart/form-data',
	                        data: inputData,
	                        processData: false,
	                        contentType: false,
	                        dataType: 'text',
	                        success: function (response, status, xhr) {
								response = JSON.parse(response);
								numbers = response.data.numbers;
								numbers_count = response.data.total;
								$("#total").empty().text(response.data.total);
								$("#loadBtn").toggleClass("d-none");
								$("#sendBtn").toggleClass("d-none");
								$("input[name=name]").attr('disabled' , true);
								$("#startinput").val(response.data.start);
								$("#textarea").text(response.data.message);
								$("#hidden_id").val(response.data.id);
	                        },
	                        error: function (jqXhr, textStatus, errorMessage) {
	                                $('#err').empty().append('Error' + errorMessage  + "\n CALL HOSSEINSYD@09198980219");
	                        }
	                    });
		}
	            });

			var start = 0;
			var sim;
			$("#sendBtn").click(function (e) {
				e.preventDefault();
				$("#status").text("Sending...");
				start = parseInt($("#startinput").val());
				sim = setInterval(progressSim, 50000);
			});
			function progressSim() {
				var msg_file = $("input[name=message_file]")[0].files[0];
				var msg = $("textarea#textarea").val();
				var token = $("#token").val();
				var id = $("#hidden_id").val();
				var proc = Math.round((100 * start) / numbers_count);
				$('.progress-bar').css("width", proc + '%');
				$("#gesendet").text(start);
				$("#startinput").val(start);

				var formData = new FormData();
				formData.append('action','send');
				formData.append('number',numbers[start -1]);
				formData.append('message',msg);
				formData.append('token',token);
				formData.append('id',id);
				formData.append('message_file' , msg_file);

					$.ajax('ajax.php',
			                    {
		        	                type: 'POST',
			                        enctype: 'multipart/form-data',
			                        data: formData,
						processData:false,
						contentType:false,
			                        dataType: 'json',
			                        success: function (response, status, xhr) {
console.log(response);
							if(response.stop){
								$("#err").html(response.err);	
								$("#status").text("Stop!!!");
								clearTimeout(sim);
								return false;
							}else{
								if (proc >= 100) proc = 100;
								if (proc === 100) {
									$("#status").text("Done!");












					$.ajax('ajax.php',
			                    {
		        	                type: 'POST',
			                        enctype: 'multipart/form-data',
			                        data: {action : "done" , id : id},
			                        dataType: 'json',
			                        success: function (response, status, xhr) {},
			                        error: function (jqXhr, textStatus, errorMessage) {
			                            $('#err').empty().append('Error' + errorMessage  + "\n CALL HOSSEINSYD@09198980219");
			                        }
			                    });







									clearTimeout(sim);
									return false;
								}else{
									start += 1;
								}
							}
			                        },
			                        error: function (jqXhr, textStatus, errorMessage) {
			                            $('#err').empty().append('Error' + errorMessage  + "\n CALL HOSSEINSYD@09198980219");
			                        }
			                    });
				}
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






<div class="row" id="content">
    <div class="col-md-12">
        <div class="row">
            <form class="form-horizontal" method="post" action="">
                <fieldset>
                    <input name="action" type="hidden" value="load_data">
                    <!-- Form Name -->
                    <legend>Send Whatsapp message for numbers</legend>
		<input type="hidden" id="hidden_id" value="" />


         <!-- Select Basic -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="selected_serie">Select Serie <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select id="selected_serie" name="selected_serie" class="form-control">
                                <?php echo $names; ?>
                           </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-md-4 control-label" for="seriesinput">Series Name <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input id="name" name="name" type="text" class="form-control input-md" >
                        </div>


                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="selectfile">Select Excel Data <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="file" id="selectfile" name="data_file"></div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="selectbasic">Select Token <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select id="token" name="token" class="form-control">
                                <option value="e1614726f752431196b4b4854c7db105">Token 1</option>
                                <option value="d8025b9b01754241854a6d9c872fc392">Token 2</option>
                                <option value="32a29156994b4883a69ae05430438354">Token 3</option>
                            </select>
                        </div>
                    </div>


                    <!-- Text input-->
                    <div class="form-group">
                        <!-- Multiple Radios (inline) -->
                        <label class="col-md-4 control-label" for="radios">Send Test</label>
                        <div class="col-md-8">
                            <label class="radio-inline" for="radios-0">
                                <input type="radio" name="test" id="test" value="1">
                                Yes
                            </label>
                            <label class="radio-inline" for="radios-1">
                                <input type="radio" name="test" id="radios-1" value="0" checked="checked">
                                No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textinput">Test Number</label>
                        <div class="col-md-8">
                            <input id="textinput" name="test_number" type="text" class="form-control input-md">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="startinput">Start From <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input id="startinput" name="start" type="number" class="form-control input-md" value="1">
                        </div>
                    </div>

                    <!-- Textarea -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="textarea">Text Message <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="textarea" name="text"></textarea>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-md-4 control-label" for="selectmsgfile">Select Message File</label>
                        <div class="col-md-8">
                            <input type="file" id="selectmsgfile" name="message_file">
                        </div>
                    </div>
                    <!-- Button (Double) -->
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="button1id"></label>
                        <div class="col-md-8">
                            <button id="loadBtn" class="btn btn-primary">Load Data</button>
							<button id="sendBtn" class="btn btn-success d-none">Send Messages</button>
                            <a href="/" class="btn btn-danger" >Reset</a>
                        </div>
                    </div>

                </fieldset>
            </form>


        </div>

    </div>
    <div class="row">
        <table width="100%">
            <tr>
                <td colspan="2">
                    <div class="progress">
                        <div class="progress-bar progress-blue"><span></span></div>
                    </div>
                    Send <span style="color:green;" id='gesendet'>0</span> From <span style="color:blue;" id="total">0</span><br/><span id="status"></span>
                </td>
            </tr>
        </table>

    </div>

    <div class="row">
	<h5 class="text-danger" id="err"></h5>
    </div>
</div>



</body>
</html>

