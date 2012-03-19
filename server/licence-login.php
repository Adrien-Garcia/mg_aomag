<?php 
session_start();

if(($_POST['login'] == 'aoadmin') && ($_POST['password'] == 'N3SSpr3S0/lung0')):
	$_SESSION['password'] = 'N3SSpr3S0/lung0';	
	header('Location: licence-generator.php');	
endif;

if($_SESSION['password'] != 'N3SSpr3S0/lung0'):
	$_SESSION['password'] = null;	
	header('Location: licence-generator.php');	
endif;

?>