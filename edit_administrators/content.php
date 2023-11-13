<?php
	include_once('../tables/AdminTable.php');
	$table = new AdminTable();
	if (isset($_POST['submit'])){
		$table->POST = $_POST;
		switch($_POST['submit']){
			case 'Удалить':
				$table->delete($table->get_id());
				header("Location: ../login");
				unset($_SESSION['auth_admin']);
				break;
			case 'Изменить':
				$table->update($table->get_id(), $_POST['login'],$_POST['password'], $_POST['fio'], $_POST['email'], $_POST['phone'], $_POST['position']);
				break;
			case 'Работа с приложением':
				header("Location: ../");
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
		$table->create_page_edit();
	}
	echo "</main>";
?>
