<div class="app-head">Site Databases for <?php echo \clay\data\get('site','string','',\clay\data\get('s','string','','Unknown')); ?></div>
<div class="app-content">
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

<div style="float:left">
	<?php if(!empty($dbs)){ if(empty($cfg)) {?> <h3 class="alert">No Site Databases have been stored!</h3> 
	<p>
		ClayDB requires the primary Database Configuration Name to be 'default'. Please create the 'default' database configuration.
	</p>
	<?php }?>
	<form method="post" action="?com=databases&act=add">
		<fieldset><legend>Choose a Database for this site</legend>
			<table>
				<tr><td class="align-right">
					<label for="dbhost" title="">DB Configuration Name:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbcfg" id="dbcfg" maxlength="64" value="" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbuser" title="">Table Prefix:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="prefix" id="prefix" maxlength="64" value="" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbpass" title="">Database Server:</label>
				</td><td>
					<select class="form-textxlong" name="dbcon" id="dbcon">
<?php foreach($dbs as $key => $value){?>
						<option value="<?php echo $key; ?>"><?php echo $value['type'] .' :: '. $value['usern'] . '@' . $value['host']; ?></option>
<?php }?>
						<option value="" selected>Select a Connection</option>
					</select>
				</td></tr>
				<tr><td class="align-right">
					<label for="dbname" title="">Database Name:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbname" id="dbname" maxlength="64" value="" />
				</td></tr>
			</table>
			<input type="hidden" name="site" id="site" value="<?php echo \clay\data\get('site','string','base',\clay\data\get('s','string','base')); ?>" />
			<input type="submit" name="submit" value="Add" id="submit" />
		</fieldset>
	</form>
</div>
<?php }
if(!empty($cfg)){
foreach($cfg as $key => $value){ ?>
<div style="float:left">
	<form method="post" action="?com=databases&act=update">
		<fieldset><legend><?php echo $key;?></legend>
			<table>
				<tr><td class="align-right">
					<label for="dbhost" title="">DB Configuration Name:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbcfg" id="dbcfg" maxlength="64" value="<?php echo $key; ?>" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbuser" title="">Table Prefix:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="prefix" id="prefix" maxlength="64" value="<?php echo $value['prefix']; ?>" />
				</td></tr>
				<tr><td class="align-right">
					<label for="dbpass" title="">Database Server:</label>
				</td><td>
					<select class="form-textxlong" name="dbcon" id="dbcon">
<?php foreach($dbs as $dbkey => $dbvalue){
if($dbkey == $value['connection']){?>
						<option value="<?php echo $dbkey; ?>" selected="selected"><?php echo $dbvalue['type'] .' :: '. $dbvalue['usern'] . '@' . $dbvalue['host']; ?></option>
<?php } else { ?>
						<option value="<?php echo $dbkey; ?>"><?php echo $dbvalue['type'] .' :: '. $dbvalue['usern'] . '@' . $dbvalue['host']; ?></option>
<?php } }?>
					</select>
				</td></tr>
				<tr><td class="align-right">
					<label for="dbname" title="">Database Name:</label>
				</td><td>
					<input class="form-textxlong" type="text" name="dbname" id="dbname" maxlength="64" value="<?php echo $value['database']; ?>" />
				</td></tr>
			</table>
			<input type="hidden" name="site" id="site" value="<?php echo $site; ?>" />
			<input type="submit" name="submit" value="Update" id="submit" />
		</fieldset>
	</form>
</div>
<?php }
} ?>
</div>
<br style="clear:both" />