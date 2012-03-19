<?php
session_start();
if($_SESSION['password'] != 'N3SSpr3S0/lung0'):
	$_SESSION['password'] = null;
	header('Location: licence-generator.php');
endif;


$key = 'e983cfc54f88c7114e99da95f5757df6';
$licence = md5($_POST['hostname'].$key.$_POST['module']);
echo $licence.'<br>';

?>