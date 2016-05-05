<?php foreach(self::$map['scripts.'.$position] as $file){ if(is_array($file)){ ?>
<script type="text/javascript" src="<?php echo $file['src']; ?>"></script>
<?php } else {?>
<script type="text/javascript">
<?php include $file; ?>
</script>
<?php } }?>