<?php
	include_once('../tables/ProdTable.php');
	$table = new ProdTable("prod", "id_prod");
	$name  = $table->title_field;
	$id    = $table->id_field;
	if (isset($_POST['submit'])){
		$table->POST = $_POST;
		switch($_POST['submit']){
			case "Добавить":
				$table->insert($_POST["id_product"], $_POST["id_consultant"], $_POST["data_prod"], $_POST["time_prod"], $_POST["kol"]);
				break;
			case "Изменить":
				$table->update($_POST["id_prod"], $_POST["id_product"], $_POST["id_consultant"], $_POST["data_prod"], $_POST["time_prod"], $_POST["kol"]);
				break;
			case "Удалить":
				$table->delete($_POST["id_prod"]);
				break;
			case "Фильтровать по ФИО консультанта":
				$table->filtration_from_FIO = $_POST['filtration-fio'];
				break;
			case "Фильтровать по дате":
				$table->filtration_from_date_start = $_POST['filtration-date-start'];
				$table->filtration_from_date_end   = $_POST['filtration-date-end'];
				break;
			case "Фильтровать по ФИО консультанта из списка":
				$table->filtration_from_FIO_select = $_POST['filtration-fio-select'];
				break;
			case "Фильтровать по названию товара из списка":
				$table->filtration_from_name_select = $_POST['filtration-name-select'];
				break;
		}
	}
	echo "<main> ";
	$table->create_page($_POST['sort-field']);
	echo "</main>";
?>






