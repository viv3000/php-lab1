<?php
	include_once('../../tables/CategoryTable.php');
	$table = new CategoryTable("category", "id_category", "name_category");
	$name  = $table->title_field;
	$id    = $table->id_field;
	if (isset($_GET['submit'])){
		switch($_GET['submit']){
			case "Добавить":
				$table->insert($_GET[$name]);
				break;
			case "Изменить":
				$table->update($_GET[$id], $_GET[$name]);
				break;
			case "Удалить":
				$table->delete($_GET[$id]);
				break;
		}
	}
	echo '<main> 	
		<nav class="directorys-bar">
			<a href="../brands">Брэнды</a>
			<a href="../categorys">Категории</a>
			<a href="../colors">Цвета</a>
		</nav>';
	$table->create_page();
	echo "</main>";
?>
