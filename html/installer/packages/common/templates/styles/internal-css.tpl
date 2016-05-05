<?php foreach(self::$map['styles'] as $file){ if(is_array($file)){ ?>
<link rel="stylesheet" type="text/css" href="<?php echo $file['src']; ?>" /><?php } else {?>
<style type="text/css">
<?php include $file; ?>
</style>
<?php } } ?>