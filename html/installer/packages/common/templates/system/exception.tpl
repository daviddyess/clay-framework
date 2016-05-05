<div class="app-content">
<h3>Response: <?php echo $exception->getMessage(); ?></h3>
<p>Exception in File: <?php echo $exception->getFile(); ?> on Line: <?php echo $exception->getLine(); ?></p>
<pre><?php echo $exception->getTraceAsString(); ?></pre>
</div>