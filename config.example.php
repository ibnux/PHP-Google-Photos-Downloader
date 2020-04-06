<?php
use Medoo\Medoo;

//get from https://console.cloud.google.com/apis/dashboard
$client_id = ".apps.googleusercontent.com";
$client_sc = "F0XmTh";

//Permission scope, only google photos
$scopes = 'https://www.googleapis.com/auth/photoslibrary';

//after user login, will be redirect to this
//need to register on oauth redirect
$redirect = "http://photos.ibnux.org/login.php";

$proxy = '127.0.0.1:3128';

//how many image to download every request
$pageSize = 50;

$db = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'google_photo',
	'server' => 'localhost',
	'username' => 'root',
	'password' => 'root'
]);
