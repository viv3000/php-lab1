<?php
	include_once('../../tables/ColorTable.php');
	$table = new ColorTable("color", "id_color", "name_color");
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
