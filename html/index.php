<?PHP
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

	/* Clay Framework - Copyright Invenotech LLC 2008
	 * Author: David Dyess II
	 * All Rights Reserved
	*/

	$starttimer = time()+microtime();
	//ini_set('zlib.output_compression', 'on');
	//ini_set('zlib.output_compression_level',-1);
	try{
		set_include_path(dirname(dirname(__FILE__)) . PATH_SEPARATOR . get_include_path());
		include 'library/clay.php';
		\clay::bootstrap('default');
		//You expected more? :)
	} catch (\Exception $exception) {
    	 ?>	<div class="app-content">
		<h3>Response: <?php echo $exception->getMessage(); ?></h3>
		<?php # Need to find a better way to do this...
			  # Maybe a fall back app (such as System)?
		/*
		<p>Exception in File: <?php echo $exception->getFile(); ?> on Line: <?php echo $exception->getLine(); ?></p>
		<pre><?php echo $exception->getTraceAsString(); ?></pre>
		*/
		?>
		</div>
	<?php
	}
	$stoptimer = time()+microtime();
	$timer = round($stoptimer-$starttimer,4);
	echo "<!-- Page created in $timer seconds. -->";
	echo "<!-- Peak PHP Memory Usage ".\memory_get_peak_usage()." bytes -->";
?>
