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
	$table->create_page();
	echo "</main>";
?>
