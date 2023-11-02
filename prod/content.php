<?php
	include_once('../tables/ProdTable.php');
	$table = new ProdTable("prod", "id_prod");
	$name  = $table->title_field;
	$id    = $table->id_field;
	if (isset($_GET['submit'])){
		switch($_GET['submit']){
			case "Добавить":
				$table->insert($_GET["id_product"], $_GET["id_consultant"], $_GET["data_prod"], $_GET["time_prod"], $_GET["kol"]);
				break;
			case "Изменить":
				$table->update($_GET["id_prod"], $_GET["id_product"], $_GET["id_consultant"], $_GET["data_prod"], $_GET["time_prod"], $_GET["kol"]);
				break;
			case "Удалить":
				$table->delete($_GET["id_prod"]);
				break;
		}
	}
	echo "<main> ";
	$table->create_page();
	echo "</main>";
?>






