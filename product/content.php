<?php
	include_once('../tables/ProductTable.php');
	$table = new ProductTable("prod", "id_prod");
	if (isset($_GET['submit'])){
		switch($_GET['submit']){
			case "Добавить":
				$table->insert($_GET["name_product"], $_GET["id_brand"], $_GET["id_category"], $_GET["id_color"], $_GET["weight"], $_GET["price_product"]);
				break;
			case "Изменить":
				$table->update($_GET["id_product"], $_GET["name_product"], $_GET["id_brand"], $_GET["id_category"], $_GET["id_color"], $_GET["weight"], $_GET["price_product"]);
				break;
			case "Удалить":
				$table->delete($_GET["id_product"]);
				break;
		}
	}
	echo "<main> ";
	$table->create_page();
	echo "</main>";
?>






