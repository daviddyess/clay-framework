<style type="text/css">
/* TODO: add this to a package css */
.inst-accent { color:blue }
.inst-error { color:red }
</style>
<div class="app-head">Installer Setup</div>
<div class="app-content">
<?php if(!empty($msg)){ echo "<p><h3>$msg</h3></p>"; } ?>
	<h3>File Structure</h3>
	<p>Clay path is <?php echo \clay\PATH; ?></p>
	<p>Data path is <?php echo \clay\DATA_PATH; ?> and <?php echo $data_dir_status; ?></p>
	<p>Data Configuration path is <?php echo \clay\CFG_PATH; ?></p>
	<p>Web path is <?php echo \clay\WEB_PATH; ?></p>
<?php if(!empty($initial)) $this->template(array('application' => 'installer', 'template' => 'includes/admin_view_initial')); ?>
<?php if(!empty($upgrade)) $this->template(array('application' => 'installer', 'template' => 'includes/admin_view_upgrade')); ?>
<?php if(!empty($settings)) $this->template(array('application' => 'installer', 'template' => 'includes/admin_view_settings')); ?>
	<h3>Security</h3>
<form method="post" action="<?php echo \installer\application::url('admin','edit'); ?>">
<p>This is a security measure to prevent unauthorized individuals from accessing the Installer. If you change your passcode your session will be invalidated..</p>
<fieldset><legend>Set Passcode</legend>
<p>
  Please enter and confirm a passcode to secure the Installer.
</p>
<p>
  <label for="pass1" title="Passcode">Passcode:</label>
  <input class="form-textxlong" type="password" name="pass" id="pass1" maxlength="128" />
</p>
<p>
  <label for="pass2" title="Passcode">Confirm Passcode:</label>
  <input class="form-textxlong" type="password" name="passconf" id="pass2" maxlength="128" />
</p>
</fieldset>
<fieldset><legend>Passkey</legend>
<p>Please enter a Passkey to secure the Installer. The Passkey is used for encryption only and will not be required the next time you access the Installer.</p>
<p>
  <label for="key1" title="Encryption Key">Passkey:</label>
  <input class="form-textxlong" type="password" name="key" id="key1" maxlength="128" />
</p>
</fieldset>
<input type="hidden" name="edit" value="Passcode" id="pcedit" />
<input type="submit" name="submit" value="Submit" id="submit" />
</form>

<h3>Passcode Recovery</h3>
<form method="post" action="<?php echo \installer\application::url('admin','edit'); ?>">
<fieldset><legend>Q&amp;A</legend>
<p>
  Please enter a question and answer set. Your question will be displayed during password recovery. Consider this Question and Answer set as being as sensitive as your actual Password.
</p>
<p>
  <label for="pass1" title="Recovery Question - Current Question is: <?php echo $question; ?>">Question:</label>
  <input class="form-textxlong" type="text" name="question" id="quest1" maxlength="200" />
</p>
<p>
  <label for="pass2" title="Recovery Answer">Answer:</label>
  <input class="form-textxlong" type="text" name="answer" id="answ2" maxlength="200" />
</p>
</fieldset>
<input type="hidden" name="edit" value="PasscodeRecovery" id="pcredit" />
<input type="submit" name="submit" value="Submit" id="submit" />
</form>
</div>