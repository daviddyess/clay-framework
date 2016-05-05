<div class="app-head">Restore a Site Installation</div>
<div class="app-content">
<?php if(!empty($message)){?><h3><?php echo $message ;?></h3><?php } ?>
<form method="post" action="?com=setup&act=restore">
	<div style="float:left">
	<fieldset><legend>Restore <?php echo $site; ?>?</legend>
		<p>If an installation already exists named <?php echo $site; ?>, it will be replaced by the backup.</p>
		<p>Are you sure you want to restore this package installation backup?</p>
		<input type="hidden" name="site" id="site" value="<?php echo $site; ?>" /> <input type="hidden" name="confirm" id="confirm" value="1" />
		<input type="submit" name="submit" value="Yes, Restore <?php echo $site; ?>" id="submit" />
	</fieldset>
	</div>
</form>
</div>
<br style="clear:both" />