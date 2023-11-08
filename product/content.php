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
			case "Фильтровать по названию":	
				$table->filtration_from_name = $_GET['filtration-title'];
				break;
			case "Фильтровать по категории":
				$table->filtration_from_category = $_GET['filtration-category'];
				break;
			case "Фильтровать по цене":
				$table->filtration_from_price = $_GET['filtration-price'];
				break;
			case "Фильтровать по бренду и категории":
				$table->filtration_from_brand_and_category = array(
					$_GET['filtration-brand-and-category-brand'], 
					$_GET['filtration-brand-and-category-category']);
				break;
		}
	}
	echo "<main> ";
	$table->create_page();
	echo "</main>";
?>
