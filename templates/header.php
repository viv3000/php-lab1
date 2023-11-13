<?php
session_start();

if (isset($_POST['global-submit'])){
	include_once('../tables/AdminTable.php');
	$table = new AdminTable();
	switch($_POST['global-submit']){
		case 'Войти':
			$table->login($_POST['login'], $_POST['password']);
			break;
		case 'Выйти':
			$table->exit();
			break;
	}
}

$user = $_SESSION['auth_admin_login'];

$form_login = "
		<form method='POST' class='header-login' action=''>
			<div class='form-group'>
				<input type='text' id='title' placeholder='Логин' name='login'>
			</div>
			<div class='form-group'>
				<input type='password' name='password' placeholder='Пароль'>
			</div>
			<input type='submit' class='' name='global-submit' value='Войти'>
		</form>
";
$form_exit = "
		<form method='POST' class='header-login' action=''>
			<h3>Добро пожаловать, $user</h3>
			<input type='submit' class='' name='global-submit' value='Выйти'>
		</form>
";

$form = $_SESSION['auth_admin'] == 'yes_auth' ? $form_exit : $form_login;

echo "
	<header>
		<h1><a href='/lab1/'>Магазин косметики: 'Магазин косметики'</a></h1>
		$form
</header>";
?>
