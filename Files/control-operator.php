<?php

    /*
    *   @author Owen Morgan (OM Solutions)
    *   @copyright OM Solutions 2018
    */
?>
<?php include('./resources/layout/head.php'); ?>

<?php
$permCheck = haveGeneralPerm($UserArray['userid'], 2);

if($permCheck == false){
    echo '<meta http-equiv="refresh" content="0; url=index.php" />';
}
?>

<?php

	$calls = getActiveCalls('ASC');
	$availableUnits = getAvailableUnits();
?>

<title>WMRPC - Control Operator</title>

<div class="container-fluid" style="margin-top: 25px;">
	<div id="panicSection">
		<?php
		$num = $con->query("SELECT * FROM units WHERE status = 0")->num_rows;

			if($num > 0){

				$unit = mysqli_fetch_assoc($con->query("SELECT * FROM units WHERE status = 0"));
		?>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="alert alert-danger state0" style="text-align: center;">
					<b>Panic Button Activation by <?php echo $unit['unit']; ?> (<?php echo $unit['collar']; ?>)</b>
				</div>
			</div>
		</div>
		<?php
			}
		?>
	</div>
	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-7"><div id="refreshDiv">
			<div class="row">
				<?php
					foreach($calls as $call){
				?>
				<div class="col-md-6">
					<div class="card custom-card" style="margin-bottom: 10px;">
						<div class="card-header">
							<?php echo $call['callid'] . "/" . $call['dateline'] . " - " . $call['type']; ?>
						</div>
						<div class="card-body" style="padding-bottom: 0 !important;">
							<div class="row">
								<div class="col-md-6">
									<b>Time Created:</b><br><?php echo $call['created']; ?><br>
								</div>
								<div class="col-md-6">
									<b>Call Status:</b><br><i><?php if($call['status'] == 1){ echo "Received"; }elseif($call['status'] == 2){ echo "Not Dispatched"; }elseif($call['status'] == 3){ echo "Dispatched"; } ?></i><br>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-8">
									<b>Location:</b><br><?php echo $call['location']; ?> <br>
								</div>
								<div class="col-md-4">
									<b>Caller:</b><br><?php if($call['caller'] == false){ echo "Anonymous"; }else{ echo "<a href=\"./pnc-check.php?cid=" . $call['caller']['civid'] . "\">" . $call['caller']['name'] . "</a>"; } ?><br>
								</div>
							</div>
							<Br>
							<div class="row">
								<div class="col-md-12">
									<b>Opening Remarks:</b><br><?php echo $call['description']; ?><br>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-4">
									<b>POLICE Grade:</b><br><?php echo $call['police_grade']; ?><br>
								</div>
								<div class="col-md-4">
									<b>RMU Grade:</b><br><?php echo $call['rmu_grade']; ?><br>
								</div>
								<div class="col-md-4">
									<b>INC Channel:</b><br><?php echo $call['channel']; ?><br>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 text-center">
									<b>Assigned Units</b>
								</div>
							</div>
						</div>
						<table class="table table-responsive-xl" id="availableUnits">
							<tbody>
								<?php
									foreach($call['units'] as $unit){
								?>
								<tr class="state<?php echo preg_replace("/[\s_]/", "-", $unit['status']); ?>">
									<td><b><?php echo $unit['unit'] . " - " . $unit['collar'] . " - State " . $unit['status']; ?></b></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
					}
				?>
			</div>
		</div></div>
		<div class="col-md-3">
			<div class="card custom-card">
				<div class="card-header">
					<a href="#" onclick="toggleDiv('editCAD')"><i class="fa fa-plus"></i></a> 
					Edit CAD
				</div>
				<div class="card-body" id="editCAD" style="display: none;">
					<?php
					if(isset($_POST['updateCall'])) { 
			  	  		updateCall($_POST['cad'],$_POST['police_grade'],$_POST['rmu_grade'],$_POST['channel']);
			  	  		updateCallStatus($_POST['cad'], 2);
					}
					?>
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="form-group col-md-12">
    						<label for="channel">Select CAD</label>
    						<div id="test">
    							<select class="form-control" name="cad">
    								<?php
    								foreach($calls as $call){
    								?>
    								<option value="<?php echo $call['callid']; ?>"><?php echo $call['callid'] . "/" . $call['dateline'] . " - " . $call['type']; ?></option>
    								<?php
    								}
    								?>
    							</select>
    						</div>
  						</div>
  						<div class="form-group col-md-12">
    						<label for="channel">Incident Channel</label>
    						<select class="form-control" name="channel">
    							<option value="INC1">Incident Channel 1</option>
    							<option value="INC2">Incident Channel 2</option>
    							<option value="INC3">Incident Channel 3</option>
    							<option value="INC4">Incident Channel 4</option>
    							<option value="INC5">Incident Channel 5</option>
    							<option value="INC6">Incident Channel 6</option>
    						</select>
  						</div>
  						<div class="form-group col-md-12">
    						<label for="grade">POLICE Grade</label>
    						<select class="form-control" name="police_grade">
    							<option value="N/A">N/A</option>
    							<option value="Grade 1">Grade 1</option>
    							<option value="Grade 2">Grade 2</option>
    						</select>
  						</div>
  						<div class="form-group col-md-12">
    						<label for="grade">RMU Grade</label>
    						<select class="form-control" name="rmu_grade">
    							<option value="N/A">N/A</option>
    							<option value="CAT1">CAT1</option>
    							<option value="CAT2">CAT2</option>
    							<option value="CAT3">CAT3</option>
    							<option value="CAT4">CAT4</option>
    						</select>
  						</div>
  						<div class="form-group col-md-12">
  							<input type="submit" name='updateCall' class="btn btn-success btn-block" value="Update Call">
  						</div>
  					</form>
				</div>
			</div>
			<br>
			<div class="card custom-card">
				<div class="card-header">
					<a href="#" onclick="toggleDiv('refreshMessages')"><i class="fa fa-plus"></i></a> 
					<a data-toggle="modal" data-target="#sendMessage"><i class="fa fa-comment-alt"></i></a> 
					Recent Messages
				</div>
				<table class="table table-responsive-xl" id="refreshMessages">
					<tbody>
						<?php
						$messages = getRecentMessages("CONTROL");

						foreach($messages AS $message){
						?>
						<tr>
							<td><b><?php echo $message['post']; ?></b> to <i><?php echo $message['recive']; ?></i><br><?php echo $message['content']; ?></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="sendMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  		<div class="modal-dialog" role="document">
    		<div class="modal-content" style="margin-top: 100px;">
      			<div class="modal-header">
        			<h5 class="modal-title" id="sendMessage">Send Message</h5>
      			</div>
      			<div class="modal-body">
      				<div class="container">
        				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
							<div class="row">
								<div class="form-group col-md-12">
    								<label for="channel">Select Unit</label>
    								<select name="unit" class="form-control">
    									<?php
    									foreach($availableUnits as $unit){
    									?>
    									<option value="<?php echo strtoupper($unit['unit']); ?>"><?php echo $unit['unit']; ?> (<?php echo $unit['collar']; ?>)</option>
    									<?php
    									}
    									?>
    								</select>
  								</div>
  								<div class="form-group col-md-12">
    								<label for="grade">Enter Message</label>
    								<input type="text" class="form-control" name="message">
  								</div>
								<div class="form-group" style="width: 100%;">
									<input type="submit" name='sendMessage' class="btn btn-success btn-block" value="Send Message">
           						</div>
           					</div>
						</form>
						<?php
							if(isset($_POST['sendMessage'])) { 
			  	  				sendMessage($_POST['unit'], 'CONTROL', $_POST['message']);
							}
						?> 
					</div>
      			</div>
    		</div>
  		</div>
	</div>


<script> 
function refreshDiv() { 
    $('#refreshDiv').load(document.URL +  ' #refreshDiv');
} 

function refreshMessages(){
	$('#refreshMessages').load(document.URL +  ' #refreshMessages');
}

function panicSection(){
	$('#panicSection').load(document.URL +  ' #panicSection');
}

function refreshModals(){
	$('#test').load(document.URL +  ' #test');
}

function toggleDiv(div){
	$(`#${div}`).slideToggle();
}

function playAudio(){
	document.getElementById('player').play();
}

function stopAudio(){
	document.getElementById('player').stop();
}

window.setInterval(refreshDiv, 3000);
window.setInterval(refreshMessages, 3000);
window.setInterval(panicSection, 3000);
window.setInterval(refreshModals, 10000);
</script>