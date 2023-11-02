<?php
include_once('ColorTable.php');
include_once('BrandTable.php');
include_once('CategoryTable.php');
include_once('Table.php');
class ProductTable extends Table{

	public function __construct(){
		$this->table_name = "product";
		$this->id_field = "id_product";
	}

	public function insert($title, $id_brand = null, $id_category = null, $id_color = null, $weight = null, $price_product = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $title, $id_brand, $id_category, $id_color, $weight, $price_product)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT 
			INTO `cosmetic_shop`.`product` 
				   (`name_product`,  `id_brand`,  `id_category`,  `id_color`,  `weight`,  `price_product`) 
			VALUES ('$title', '$id_brand', '$id_category', '$id_color', '$weight', '$price_product')");
	}

	public function update($id, $title, $id_brand = null, $id_category = null, $id_color = null, $weight = null, $price_product = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		}  else if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $title, $id_brand, $id_category, $id_color, $weight, $price_product)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("UPDATE `cosmetic_shop`.`product` 
				SET 
				`name_product` = '$title', 
				`id_brand`     = '$id_brand', 
				`id_category`  = '$id_category', 
				`id_color` = '$id_color',
				`weight` = '$weight',
				`price_product` = '$price_product' 
				WHERE (`id_product` = '$id')");
	}

	private function have_duplicate($connection, $title, $id_brand, $id_category, $id_color, $weight, $price_product){
		return $connection->query("
			SELECT * FROM cosmetic_shop.product
			where name_product = '$title'
			and id_brand = $id_brand
			and id_category = $id_category
			and id_color = $id_color
			and weight = $weight
			and price_product = $price_product
			"
		)->num_rows > 0;
	}

	public function create_page(){
		echo '
			<form method="get" action="">
				<div class="form-group">
					<label for="data_prod">Название</label>
					<input type="text" class="form-control" id="name_product" name="name_product">
				</div>
				<div class="form-group" style="width: 100px;">
					<label for="price_product">Цена</label>
					<input value="0" min="0" max="999999" onkeypress="return event.charCode >= 48 && event.charCode <= 57" type="number" class="form-control" id="price_product" name="price_product"/>
				</div>
				<div class="form-group" style="width: 100px;">
					<label for="weight">Вес</label>
					<input value="0" min="0" max="999" onkeypress="return event.charCode >= 48 && event.charCode <= 57" type="number" class="form-control" id="weight" name="weight"/>
				</div>
				<br/>
		';
		$color    = new ColorTable("color", "id_color", "name_color");
		$brand    = new BrandTable("brand", "id_brand", "name_brand");
		$category = new CategoryTable("category", "id_category", "name_category");
		$color->create_select();
		echo "<br/>";
		$brand->create_select();
		echo "<br/>";
		$category->create_select();
		echo "<br/>";
		$this->create_select();
		echo '
				<br/>
				<br/>
				<input type="submit" class="btn btn-primary" name="submit" value="Добавить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Изменить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Удалить", onclick="return confirm(\'Вы действительно хотите удалить запись?\')">			
			</form>
		';
		$this->create_table();
	}

	
	public function create_table(){
		$connection = $this->createConnection();
		$brands = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color");

		echo '
			<table class="table">
					<thead>
					<tr>
						<th scope="col">id_product</th>
						<th scope="col">name_product</th>
						<th scope="col">weight</th>
						<th scope="col">price_product</th>
						<th scope="col">name_brand</th>
						<th scope="col">name_color</th>
						<th scope="col">name_category</th>
					</tr>
					</thead>
					<tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
						<td>'.$row["id_product"].'</td>
						<td>'.$row["name_product"].'</td>
						<td>'.$row["weight"].'</td>
						<td>'.$row["price_product"].'</td>
						<td>'.$row["name_brand"].'</td>
						<td>'.$row["name_color"].'</td>
						<td>'.$row["name_category"].'</td>
					</tr>';
			}
		}
		echo '
					</tbody>
				</table>';
	}

	public function create_select(){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.".$this->table_name." order by name_product ASC");

		echo "<label>Продукт: </label><select class='btn' name='".$this->id_field."'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row[$this->id_field]."'>"
					.$row['name_product']."
				</option>";
			}
		}
		echo "</select>";
	}
}
?>
