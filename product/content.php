<?php
	include_once('../tables/ProductTable.php');
	$table = new ProductTable("prod", "id_prod");
	if (isset($_POST['submit'])){
		$table->POST = $_POST;
		switch($_POST['submit']){
			case "Добавить":
				$file = base64_encode(file_get_contents(($_FILES['image']['tmp_name'])));
				$table->insert($_POST["name_product"], $_POST["id_brand"], $_POST["id_category"], $_POST["id_color"], $_POST["weight"], $_POST["price_product"], $file);
				break;
			case "Изменить":
				$file = base64_encode(file_get_contents(($_FILES['image']['tmp_name'])));
				$table->update($_POST["id_product"], $_POST["name_product"], $_POST["id_brand"], $_POST["id_category"], $_POST["id_color"], $_POST["weight"], $_POST["price_product"], $file);
				break;
			case "Удалить":
				$table->delete($_POST["id_product"]);
				break;
			case "Фильтровать по названию":	
				$table->filtration_from_name = $_POST['filtration-title'];
				break;
			case "Фильтровать по категории":
				$table->filtration_from_category = $_POST['filtration-category'];
				break;
			case "Фильтровать по цене":
				$table->filtration_from_price = $_POST['filtration-price'];
				break;
			case "Фильтровать по бренду и категории":
				$table->filtration_from_brand_and_category = array(
					$_POST['filtration-brand-and-category-brand'], 
					$_POST['filtration-brand-and-category-category']);
				break;
			case "Фильтровать по бренду и категории из списка":
				$table->filtration_from_brand_and_category_select = array(
					$_POST['filtration-brand-and-category-brand-select'], 
					$_POST['filtration-brand-and-category-category-select']);
				break;
			case "Фильтровать по бренду из списка":
				$table->filtration_from_brand_select = $_POST['filtration-brand-select'];
				break;
		}
	}
	echo "<main> ";
	$table->create_page($_POST['sort-field']);
	echo "</main>";
?>
