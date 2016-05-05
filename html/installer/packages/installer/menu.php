<?php
$data = array();
$data['links'] = array(array('name' => 'Overview', 'title' => 'Setup Editor','url' => static::url('main','view')),
    		array('name' => 'Installations', 'title' => 'Configuration Editor','url' => static::url('setup','view')),
    		array('name' => 'Databases', 'title' => 'Database Manager','url' => static::url('databases','view')),
			//array('name' => 'Clay News', 'title' => 'Clay News','url' => self::url('installer','stream','view')),
			array('name' => 'Installer Settings', 'title' => 'Change options, such as your authentication passcode.','url' => static::url('admin')),
			array('name' => 'System Information', 'title' => 'System Information','url' => static::url('system','view')),
			array('name' => 'Help', 'title' => 'Help','url' => static::url('help','view')),
			array('name' => 'Logout', 'title' => 'End your session','url' => static::url('admin','logout')),
    	);
?>