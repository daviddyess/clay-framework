<style type="text/css">
#phpinfo {}
#phpinfo pre {}
#phpinfo a:link {}
#phpinfo a:hover {}
#phpinfo table {width:100%;text-align:left;}
#phpinfo .center {}
#phpinfo .center table {}
#phpinfo .center th {}
#phpinfo td, th {}
#phpinfo h1 {}
#phpinfo h2 {border-top:2px solid #000000; border-bottom:3px solid #cccccc;}
#phpinfo .p {}
#phpinfo .e {border:1px solid #cccccc;}
#phpinfo .h {}
#phpinfo .v {border:1px solid #cccccc;}
#phpinfo .vr {}
#phpinfo img {}
#phpinfo hr {}
</style>
<div class="app-head">System Information</div>
<div class="app-content">
	<h1>File System</h1>
		<p>The path to your Clay file system is <span style="color:blue;"><?php echo \clay\PATH; ?></span>.</p>
		<p>The <strong>data</strong> directory (<?php echo \clay\DATA_PATH ?>) <?php echo $data_priv; ?></p>

	<h1>Database Extensions</h1>
		<p>It is not a requirement of Clay to have access to a database, but it is common for Applications and APIs to have this requirement.</p>
		<?php foreach($dbexts as $db => $status):?>
			<?php $_status = (!empty($status)) ? " <span style=\"color:green\">is available</span>." : " <span style=\"color:red\">is not available</span>."; ?>
		<p><?php echo $db . $_status;?></p>
		<?php  endforeach; ?>
		<p>Note: You may have other database extensions available, the ones listed are currently supported by ClayDB.</p>
	<h1>PHP Info</h1>
	<div id="phpinfo">
	<?php

	ob_start () ;
	phpinfo () ;
	$pinfo = ob_get_contents () ;
	ob_end_clean () ;

	// the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
	echo ( str_replace ( "module_Zend Optimizer", "module_Zend_Optimizer", preg_replace ( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo ) ) ) ;

	?>
	</div>
</div>