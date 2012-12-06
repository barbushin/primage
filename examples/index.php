<?php

$apacheModules = apache_get_modules();
if(!$apacheModules || !in_array('mod_rewrite', $apacheModules)) {
	throw new Exception('Apache module "mod_rewrite" is not installed');
}

$sizes = array('big', 'medium', 'small');
$types['avatars'] = 'jpg';
$types['clipart'] = 'jpg';
$images['avatars'] = array('sharapova', 'safin', 'federer');
$images['clipart'] = array('bird', 'girl');
$clipartDynamicFormats = array(300, 150, 100);

foreach($images as $dir => $names) {
	foreach($names as $name) {
		$uris = array();
		foreach($sizes as $size) {
			$uri = 'images/' . $dir . '/' . $name . '_' . $size . '.' . $types[$dir];
			$uris[] = $uri;
			echo '<img src="' . $uri . '" /> ';
		}
		echo '<br />';
		foreach($uris as $uri) {
			echo '<a href="' . $uri . '" target="_blank">' . $uri . '</a><br />';
		}
		echo '<br />';
	}
}

$dir = 'clipart';
$name = $images['clipart'];
	
foreach($names as $name) {
	$uris = array();
	foreach($clipartDynamicFormats as $size) {
		$uri = 'images/' . $dir . '/' . $name . '_' . $size . 'x'. $size . '.' . $types[$dir];
		$uris[] = $uri;
		echo '<img src="' . $uri . '" /> ';
	}
	echo '<br />';
	foreach($uris as $uri) {
		echo '<a href="' . $uri . '" target="_blank">' . $uri . '</a><br />';
	}
	echo '<br />';
}