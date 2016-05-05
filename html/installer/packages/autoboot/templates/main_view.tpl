<div class="app-head">Autoboot - Boot Selector Utility</div>
<div class="app-content">
	<p>This is a simple utitity package to be used as the 'default' site configuration. It allows you to use a Multisite configuration
	 where you are able to host more than one domain, using a single copy of Clay Framework and associated platforms.</p>
	 <p>Note: It is not recommended nor necessary to have 'www.' in the domain name.</p>
	<h3>Configurations</h3>
	<form method="post" action="<?php echo \installer\application::url('main','create'); ?>" class="configs">
		<fieldset>
		<legend>Domain Names</legend>
		
		<?php if(!empty($configs)) { $inid = 0; $index = 0; foreach($configs as $domain => $config){ ?>
		<p>
			<label>Domain Name</label>
			<input type="text" name="conf[<?php echo $index; ?>][name]" id="<?php echo 'id'.$inid++; ?>" value="<?php echo $domain; ?>" />
			<label>Configuration Name</label>
			<input type="text" name="conf[<?php echo $index; ?>][conf]" id="<?php echo 'id'.$inid++; ?>" value="<?php echo $config; ?>" />
		</p>
		<?php $index++; } } else { ?>
		<p>No Domain settings exist!</p>
		<?php } ?>
		</fieldset>
		<fieldset>
		<legend>New Domain (optional)</legend>
		<p>
			<label>New Domain Name</label>
			<input type="text" name="conf[new][name]" id="<?php echo 'id'.$inid++; ?>" value="" />
			<label>Configuration Name</label>
			<input type="text" name="conf[new][conf]" id="<?php echo 'id'.$inid++; ?>" value="" />
		</p>
		</fieldset>
		<p><input type="submit" name="submit" value="Update Configurations" id="submit" /></p>
	</form>
</div>