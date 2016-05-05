<style type="text/css">
.package {float:left;width:200px;padding:10px;margin:2px;border: 3px solid white;cursor:pointer;}
.package:hover{border:3px solid #9ED0D0;}
</style>
<script type="text/javascript">
$('document').ready(function(){
  	$("div.package").click(function () {
  	  	var mypkgchoice = $(this).attr('id');
  	  	var mypkgver = $(this).attr('title');
  	  	$("div.package").css({"border":"3px solid white",'background-color':''});
  	  	$(this).css({'border':'3px solid #9ED0D0','background-color':'#D6EBEB'});
  	    $("#pkgdisplay").html(mypkgchoice);
		$("#pkgchoice").attr('value', mypkgchoice);
		$("#pkgver").attr('value', mypkgver);
  	});
  	$("div.rename").hide();
  	$("a.rename").click(function () {
  	  	var oldconf = $(this).attr('rel');
  	  	$("div.rename-" + oldconf).show();
  	});
});
</script>
<div class="app-head">Site Installations</div>
<div class="app-content">
<?php if(!empty($message)){?><h3><?php echo $message ;?></h3><?php } ?>

	<div style="float:left">
	<form method="post" action="?com=setup&act=add">
	<fieldset><legend>New Installation</legend>
		<p>Clay utilizes a packages system, which allows developers to shape the Clay Framework to their purpose. </p>
		<?php if(!empty($pkgs)){ ?>
		<p>
		  <label for="new_conf" title="Create a new website configuration.">Name:</label>
		  <input class="form-textmedium" type="text" name="new_conf" id="new_conf" maxlength="64" value="" />
		</p>
		<p>Select a package to install:</p>
		<?php foreach($pkgs as $pkg){?>
		<div class="package" id="<?php echo $pkg['name']; ?>" title="<?php echo $pkg['version']; ?>">
		<?php echo $pkg['title'].' '.$pkg['version'];?>
		<p><?php echo $pkg['description']; ?></p>
		</div>
		<?php } ?>
		<p style="clear:both;">
		<label for="pkgdisplay" title="Package to install.">Package:</label>
		<span id="pkgdisplay"> </span>
		</p>
		<input type="hidden" name="pkgver" id="pkgver" value="" /> <input type="hidden" name="pkgchoice" id="pkgchoice" value="" />
		<input type="submit" name="submit" value="Create" id="submit" />
		<?php } else { ?>
		<div><h2>Oops!</h2>No Installation packages were found. Please add a package to the [web]/installer/packages/ folder.</div>
		<?php } ?>
	</fieldset>
	</form>
	</div>
	<div style="float:left">
	<fieldset><legend>Installations</legend>
<?php if(!empty($confs)) { foreach($confs as $conf => $pkg){?>
<p><?php echo $conf; ?> (<?php echo $pkg['package'].' '.$pkg['version'];?>) - 
<a href="<?php echo \installer\application::url(array('s' => $conf)); ?>"><?php echo !empty($pkg['update']) ? 'Upgrade to '.$pkg['update'] : 'Setup'; ?></a> | 
<a href="<?php echo \installer\application::url('setup','delete',array('site' => $conf)); ?>">Delete</a> | 
<a href="#rename" class="rename" rel="<?php echo $conf; ?>">Rename</a>
</p>
<div class="rename rename-<?php echo $conf; ?>">
<form method="post" action="?com=setup&act=rename" id="rename-<?php echo $conf; ?>" class="rename">
<fieldset><legend>Rename <?php echo $conf; ?></legend>
	<p>
		<label for="new_conf" title="Rename a website configuration.">Name:</label>
		<input class="form-textmedium" type="text" name="new_conf" id="new_conf" maxlength="64" value="" />
	</p>
	<input type="hidden" name="old_conf" id="old_conf" value="<?php echo $conf; ?>" />
	<input type="submit" name="submit" value="Rename" id="submit-rename" />
</fieldset>
</form>
</div>
<?php } } else { echo "Please create an installation."; }?>
	</fieldset>
	</div>
	<div style="float:left">
	<fieldset><legend>Restore an Installation</legend>
<?php if(!empty($backups)) { foreach($backups as $backup){?>
<p><?php echo $backup['name']; ?> (<?php echo $backup['package'].' '.$backup['version'];?>) - <a href="<?php echo \installer\application::url('setup','restore',array('site' => $backup['name'])); ?>">Restore</a> | <a href="<?php echo \installer\application::url('setup','delbackup',array('site' => $backup['name'])); ?>">Delete</a></p>
<?php } } else { ?><p>No restorable backups could be found.</p> <?php }?>
	</fieldset>
	</div>
</div>
<br style="clear:both" />