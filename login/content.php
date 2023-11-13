<?php
	include_once('../tables/AdminTable.php');
	$table = new AdminTable();
	if (isset($_POST['submit'])){
		$table->POST = $_POST;
		switch($_POST['submit']){
			case 'Войти':
				$table->login($_POST['login'], $_POST['password']);
				break;
		}
	}
	echo "<main> ";
	if ($_SESSION['auth_admin'] != 'yes_auth'){
		echo "<h3 class='error'>У вас нет прав на просмотр этой страницы!</h3>";
	}else if (!$table->check_access()){
		echo "<h3 class='error'>У вас нет прав на просмотр этой страницы!</h3>";
	}
	else {
		$table->create_page_login();
	}
	echo "</main>";
?>
