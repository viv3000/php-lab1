<?php
	include_once('../tables/ConsultantTable.php');
	$table = new ConsultantTable("consultant", "id_consultant", "consultant_fio");
	$fio  = $table->title_field;
	$id   = $table->id_field;
	if (isset($_GET['submit'])){
		switch($_GET['submit']){
			case "Добавить":
				$table->insert($_GET[$fio], $_GET['consultant_fon']);
				break;
			case "Изменить":
				$table->update($_GET[$id], $_GET[$fio], $_GET['consultant_fon']);
				break;
			case "Удалить":
				$table->delete($_GET[$id]);
				break;
		}
	}
	echo "<main> ";
	$table->create_page();
	echo "</main>";
?>
