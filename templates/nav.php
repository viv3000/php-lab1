<?php
include_once("../tables/AdminTable.php");

$admin = $_SESSION['auth_admin'] == 'yes_auth' ? "<a href=\"/lab1/administrators\">Администраторы</a>" : "";
echo "
	<nav>
		<a href=\"/lab1/directorys\">Справочники</a>
		<a href=\"/lab1/prod\">Продажи</a>
		<a href=\"/lab1/product\">Продукты</a>
		<a href=\"/lab1/consultant\">Консультанты</a>
		$admin
	</nav>";
?>
