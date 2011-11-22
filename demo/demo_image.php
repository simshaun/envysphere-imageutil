<?php
require dirname(__FILE__) . '/../src/envimage.php';
require dirname(__FILE__) . '/../src/driver/gd.php';

$valid_crop_directions = array(
	't',
	'b',
	'l',
	'r',
	'c'
);

$image = empty($_GET['img']) ? 'wide.jpg' : $_GET['img'];
$width = empty($_GET['w']) || ! is_numeric($_GET['w']) ? 0 : $_GET['w'];
$height = empty($_GET['h']) || ! is_numeric($_GET['h']) ? 0 : $_GET['h'];
$crop = ! empty($_GET['c']);
$crop_direction = ! empty($_GET['cd']) && in_array($_GET['cd'], $valid_crop_directions) ? $_GET['cd'] : 'c';

if ($image != 'wide.jpg' && $image != 'tall.jpg')
	die('Invalid test image name specified.');

$image = EnvImage::factory( new EnvImage_Driver_Gd() )
	->loadFromFile('./../tests/resources/'. $image)
	->resize($width, $height, $crop, $crop_direction);

header('Content-Type: '. $image->getMimeType());
echo $image->output();