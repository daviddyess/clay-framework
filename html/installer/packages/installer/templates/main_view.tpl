<?php if(!empty($output)){ $app->template($output); } else { ?>
<style type="text/css">
	div.inst-group {
		float:left;width:200px;padding:10px;text-align:center;
	}
	img.inst-icon {
		display: block;margin-left: auto;margin-right: auto;
	}
</style>
<div class="app-head">Welcome!</div>
<div class="app-content">
		<p>The Clay Installer is built on the Clay Framework, it allows developers to create standardized installation packages.</p>
		<p>Clay Framework is available under the GPL License. Some portions of code, markup, or other items are released under other licenses (as identified).</p>
	<h1>Getting Started</h1>
	<div class="inst-group">
		<img class="inst-icon" src="<?php echo \clay\application::image('installer','blueprint_tool.png'); ?>" />
		<p><a href="<?php echo \installer\application::url('setup'); ?>">Manage Packages / Web Sites</a></p>
	</div>
	<div class="inst-group">
		<img src="<?php echo \clay\application::image('installer','db.png'); ?>" />
		<p><a href="<?php echo \installer\application::url('databases'); ?>">Manage System Databases</a></p>
	</div>
	<div class="inst-group">
		<img src="<?php echo \clay\application::image('installer','system.png'); ?>" />
		<p><a href="<?php echo \installer\application::url('system'); ?>">View System Information</a></p>
	</div>
	<div class="inst-group">
		<img src="<?php echo \clay\application::image('installer','key.png'); ?>" />
		<p><a href="<?php echo \installer\application::url('admin'); ?>">Change Installer Settings</a></p>
	</div>
	<div class="inst-group">
		<img src="<?php echo \clay\application::image('installer','help.png'); ?>" />
		<p><a href="<?php echo \installer\application::url('help'); ?>">Help</a></p>
	</div>
<br style="clear:both;" />
</div>
<?php } ?>