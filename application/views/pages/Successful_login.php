<h3>------------- You have successfully logged into the system ----------------</h3>


<?php echo form_open('logout'); ?>

<?php 
	$logged_in = $this->session->logged_in;
	if($logged_in == true){
		echo 'Welcome user: '.$this->session->username;
		}else {
			echo 'session DOES NOT exist!';
			}
?>

	<fieldset>
	<!-- Form Name -->


	<legend>LOGGED IN<legend>

<!-- Button -->
	<div class="form-group">
	  <div class="col-md-9 control-label">
		<button id="submit" name="submit" type="submit" class="btn btn-primary btn-lg">Logout</button>
	  </div>
	</div>
	
	</fieldset>
	</form>