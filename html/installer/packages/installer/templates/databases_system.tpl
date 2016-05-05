<script type="text/javascript">
$('document').ready(function(){
  	$("form.deletion").hide();
	$("input.delete").click(function () {
  	  	var dbindex = $(this).attr('id');
  	  	$("form." + dbindex).hide();
  	  	$("form.del" + dbindex).show();
  	});
	$("input.cancel").click(function () {
  	  	var dbindex = $(this).attr('id');
  	  	$("form.del" + dbindex).hide();
  	  	$("form." + dbindex).show();
  	});
});
</script>
<div class="app-head">System Databases</div>
<div class="app-content">
<?php if(!empty($message)){?><h3><?php echo $message ;?></h3><?php } ?>
<?php if(!empty($nodbs)){?>
	<h3 class="alert">No System Databases have been stored!</h3>
<?php }?>
<div style="float:left">
	<form method="post" action="?com=databases&act=add">
		<fieldset><legend>Add a New System Database</legend>
			<table>
				<tr><td class="align-right">
					<label for="dbhost" title="">Database Driver:</label>
				</td><td>
					<!--  <input class="form-textxlong" type="text" name="dbs[dbtype]" id="dbtype" maxlength="64" value="" /> -->
					<select name="dbs[dbtype]" id="dbtype">
						<?php foreach($dbexts as $db => $driver){
						if(!empty($driver)) echo '<option value="'.$driver.'">'.$db.'</option>';
						} ?>
						<option value="" selected>Select a DB Extension</option>
					</select>

				</td></tr>
				<tr><td class="align-right">
					<label for="dbhost" title="">Database Host:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbs[dbhost]" id="dbhost" maxlength="64" value="" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbuser" title="">Database User:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbs[dbuser]" id="dbuser" maxlength="64" value="" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbpass" title="">Database Password:</label>
				</td><td>
					<input class="form-textxlong" type="password" name="dbs[dbpass]" id="dbpass" maxlength="64" value="" />
				</td></tr>
			</table>
			<input type="submit" name="submit" value="Add" id="submit" />
		</fieldset>
	</form>
</div>
<?php
if(!empty($dbs)){
foreach($dbs as $key => $value){ ?>
<div style="float:left">
	<form method="post" action="?com=databases&act=update" class="db<?php echo $key; ?>">
		<fieldset><legend>db<?php echo $key;?></legend>
			<table>
				<tr><td class="align-right">
					<label for="dbhost" title="">Database Driver:</label>
				</td><td>
					<!--  <input class="form-textxlong" type="text" name="dbs[dbtype]" id="dbtype<?php echo $key;?>" maxlength="64" value="<?php echo $value['type'];?>" /> -->
					<select name="dbs[dbtype]" id="dbtype<?php echo $key;?>">
						<?php foreach($dbexts as $db => $driver){
						$status = ($value['type'] == $driver) ? ' selected' : '';
						if(!empty($driver)) echo '<option value="'.$driver.'"'.$status.'>'.$db.'</option>';
						if(!empty($status)) $statuschng = 1;
						}
						if(empty($statuschng)) echo '<option value="" selected>Select a DB Extension</option>'; ?>
					</select>
				</td></tr>
				<tr><td class="align-right">
					<label for="dbhost" title="">Database Host:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbs[dbhost]" id="dbhost<?php echo $key;?>" maxlength="64" value="<?php echo $value['host'];?>" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbuser" title="">Database User:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbs[dbuser]" id="dbuser<?php echo $key;?>" maxlength="64" value="<?php echo $value['usern'];?>" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbpass" title="">Database Password:</label>
				</td><td>
					<input class="form-textxlong" type="password" name="dbs[dbpass]" id="dbpass<?php echo $key;?>" maxlength="64" value="<?php echo $value['passw'];?>" />
				</td></tr>
			</table>
			<input type="hidden" name="dbindex" id="dbindex<?php echo $key; ?>" value="<?php echo $key; ?>" />
			<input type="submit" name="submit" value="Update" id="submit" /> <input type="button" name="delete" value="Delete" id="db<?php echo $key; ?>" class="delete" />
		</fieldset>
	</form>
	<form method="post" action="?com=databases&act=delete" class="deletion deldb<?php echo $key; ?>">
		<fieldset><legend>Delete db<?php echo $key;?>?</legend>
			<p>Are you sure you want to delete this from the system?</p>
			<input type="hidden" name="dbindex" id="dbindex<?php echo $key; ?>" value="<?php echo $key; ?>" /> <input type="hidden" name="dbs" id="dbs<?php echo $key; ?>" value="1" />
			<input type="submit" name="submit" value="Yes, Delete It" id="submit" /> <input type="button" name="cancel" value="Cancel" id="db<?php echo $key; ?>" class="cancel" />
		</fieldset>
	</form>
</div>

<?php }
}
?>
</div>
<br style="clear:both" />