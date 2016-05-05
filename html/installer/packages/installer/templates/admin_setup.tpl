<style type="text/css">
/* TODO: add this to a package css */
.inst-accent { color:blue }
.inst-error { color:red }
</style>
<div class="app-head">Installer Setup</div>
<div class="app-content">
<h2>Welcome</h2>
<p>Welcome to the Clay Unified Installer. This installer is used to install packages, built using the Clay Framework and associated API. 
Here you will setup the installer and secure it for future access. You will then be able to install packages provided to the installer.
</p>
<p>
Most packages will use this installer for minimal installation, providing more in-depth setup options within the packages' application 
platform. Some packages are utility packages, those will require configuration changes directly from the Installer.
</p>
<?php if(!empty($error)) {
if(!empty($error['data'])) { ?>
<h3>Setup Assistance:</h3>
<p class="inst-error">The Installer can not write to <?php echo \clay\DATA_PATH; ?>.</p>
<p class="inst-accent">You must give the server permission to write to <?php echo \clay\DATA_PATH; ?> before the Installer can be initiated.</p>
<?php } if(!empty($error['config'])) { ?>
<h3>Setup Assistance:</h3>
<p class="inst-error">The Installer can not write to <?php echo \clay\CFG_PATH; ?>.</p>
<p class="inst-accent">You must give the server permission to write to <?php echo \clay\CFG_PATH; ?> before the Installer can be initiated.</p>
<p class="inst-accent">Note: You can also delete this directory and allow the server to create its own.</p>
<?php } } else { ?>
<h2>Step 1:</h2>
<p>Please verify the paths below are accurate. Also, please physically ensure the Data path and any subfolders within it are writable or owned by the web server.</p>
	<h3>File Structure</h3>
	<p>Clay path is <?php echo \clay\PATH; ?></p>
	<p>Data path is <?php echo \clay\DATA_PATH; ?> and <?php echo $data_dir_status; ?></p>
	<p>Data Configuration path is <?php echo \clay\CFG_PATH; ?> and <?php echo $cfg_dir_status; ?></p>
	<p>Web path is <?php echo \clay\WEB_PATH; ?></p>
<h2>Step 2:</h2>
<p>
During this step, please provide a Password (which you will need to access the Installer) and a Passkey (used to encrypt your Password).
</p>
<form method="post" action="<?php echo \installer\application::url('admin','setup'); ?>">
<fieldset><legend>Installer Security</legend>
<p>
  Please enter and confirm a Passsword to secure the Installer. This is a security measure to prevent unauthorized individuals from accessing the Unified Installer.
</p>
<?php if(!empty($msg)){ echo "<p><h3>$msg</h3></p>"; } ?>
<p>
  <label for="pass1" title="Password">Password:</label>
  <input class="form-textxlong" type="password" name="pass" id="pass1" maxlength="128" />
</p>
<p>
  <label for="pass2" title="Passcode">Confirm Password:</label>
  <input class="form-textxlong" type="password" name="passconf" id="pass2" maxlength="128" />
</p>
<p>Please enter a Passkey to secure the Installer. The Passkey is used for encryption only and will not be required the next time you access the Unified Installer.</p>
<p>
  <label for="key1" title="Encryption Key">Passkey:</label>
  <input class="form-textxlong" type="password" name="key" id="key1" maxlength="128" />
</p>
</fieldset>
<fieldset><legend>Password Recovery</legend>
<p>
  Please enter a question and answer set. Your question will be displayed during password recovery. Consider this Question and Answer set as being as sensitive as your actual Password.
</p>
<p>
  <label for="pass1" title="Recovery Question">Question:</label>
  <input class="form-textxlong" type="text" name="question" id="quest1" maxlength="200" />
</p>
<p>
  <label for="pass2" title="Recovery Answer">Answer:</label>
  <input class="form-textxlong" type="text" name="answer" id="answ2" maxlength="200" />
</p>
<input type="hidden" name="initiate" value="true" id="iniedit" />
<input type="submit" name="submit" value="Submit" id="submit" />
</fieldset>
</form>
<h2>Step 3:</h2>
<p>1..2..3... GO!</p>
<?php } ?>
</div>