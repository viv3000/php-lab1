<?php
include_once('ColorTable.php');
include_once('BrandTable.php');
include_once('CategoryTable.php');
include_once('Table.php');

class ProductTable extends Table{
	public $POST = array('submit' => '');
	public $filtration_from_name = '';
	public $filtration_from_category = '';
	public $filtration_from_brand_and_category = array('','');
	public $filtration_from_price = 0;

	public $filtration_from_brand_and_category_select = array('','');
	public $filtration_from_brand_select = '';

	public function __construct(){
		$this->table_name = "product";
		$this->id_field = "id_product";
	}

	public function insert($title, $id_brand = null, $id_category = null, $id_color = null, $weight = null, $price_product = null, $image = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $title, $id_brand, $id_category, $id_color, $weight, $price_product)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT 
			INTO `cosmetic_shop`.`product` 
				   (`name_product`,  `id_brand`,  `id_category`,  `id_color`,  `weight`,  `price_product`, `image`) 
			VALUES ('$title', '$id_brand', '$id_category', '$id_color', '$weight', '$price_product', '$image')");
	}

	public function update($id, $title, $id_brand = null, $id_category = null, $id_color = null, $weight = null, $price_product = null, $image = null){
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
				`price_product` = '$price_product',
				`image` = '$image'
				WHERE (`id_product` = '$id')");
	}

	public function delete($id){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($id == null){
			echo '<h3 class="error">Невозможно удалить несуществующую запись!</h3>';
		} else if($this->have_relations($connection, $id)){
			echo '<h3 class="error">Невозможно удалить запись, связанную с другой! (Связи в таблице продажи)</h3>';
		} else $connection->query("
				DELETE FROM 
				cosmetic_shop.".$this->table_name." 
				WHERE (".$this->id_field." = '".$id."');");
	}

	private function have_relations($connection, $id){
		return $connection->query("
			SELECT * FROM cosmetic_shop.prod
			where id_product = '$id'"
		)->num_rows > 0;
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

	public function create_page($sort_field = null){
		echo '
			<form method="post" action="" enctype="multipart/form-data">
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
				<div class="form-group">
					<label for="">Изображение</label>
					<input type="file" class="form-control" accept="image/jpeg, image/png" id="image" name="image">
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
				<br/>';
		$this->create_buttons();
		echo "
			</form>
			<form action='' method='POST'>
				<select name='sort-field' class='btn'>
					<option>Название товара</option>
					<option>Название бренда</option>
					<option>Название категории</option>
				</select>
				<button type='submit' class='btn btn-primary'>Сортировать</button>
			</form>
		";
		switch ($sort_field){
			case 'Название товара':
				$this->create_table($this->get_table('name_product'));
				break;
			case 'Название бренда':
				$this->create_table($this->get_table('name_brand'));
				break;
			case 'Название категории':
				$this->create_table($this->get_table('name_category'));
				break;
			default:
				$this->create_table($this->get_table());
		}
		$this->create_filtrations();
	}

	public function create_buttons(){
		echo '
				<input type="submit" class="btn btn-primary" name="submit" value="Добавить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Изменить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Удалить", onclick="return confirm(\'Вы действительно хотите удалить запись?\')">';
	}

	public function get_table($sort_field = 'name_product'): mysqli_result{
		$connection = $this->createConnection();
		return $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, product.image,
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color order by $sort_field ASC");
	}
	
	public function create_filtrations(){
		echo "<h2>Фильтрация по вводу с клавиатуры</h2>";
		echo "<div class='filtration-keyboard'>";
		echo "<h3>Фильтрация по названию товара</h3>";
		$this->create_filtration_block_name();
		echo "</br>";
		echo "<h3>Фильтрация по названию категории</h3>";
		$this->create_filtration_block_category();
		echo "</br>";
		echo "<h3>Фильтрация по названию категории и бренда</h3>";
		$this->create_filtration_block_brand_and_category();
		echo "</br>";
		echo "<h3>Фильтрация по цене (получение значений меньше введенного)</h3>";
		$this->create_filtration_block_price();
		echo "</div>";
		echo "<h2>Фильтрация по значению из списка</h2>";
		echo "<div class='filtration-keyboard'>";
		echo "<h3>Фильтрация по названию категории и бренда</h3>";
		$this->create_filtration_block_brand_and_category_select();
		echo "<h3>Фильтрация по названию бренда</h3>";
		$this->create_filtration_block_brand_select();
		echo "</div>";
	}

	public function create_filtration_block_name(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for='data_prod'>Название</label>
					<input type='text' class='form-control' id='filtration-title' name='filtration-title'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по названию'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Название бренда</b></div>
					<div class='col-2'><b>Цвет</b></div>
					<div class='col-2'><b>Вес</b></div>
					<div class='col-2'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по названию'){
			$brands = $this->get_table_from_name($this->filtration_from_name);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_brand"]."</div>
							<div class='col-2'>".$row["name_color"]."</div>
							<div class='col-2'>".$row["weight"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function create_filtration_block_category(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for='data_prod'>Категория</label>
					<input type='text' class='form-control' id='filtration-category' name='filtration-category'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по категории'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Название бренда</b></div>
					<div class='col-2'><b>Цвет</b></div>
					<div class='col-2'><b>Вес</b></div>
					<div class='col-2'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по категории'){
			$brands = $this->get_table_from_category($this->filtration_from_category);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_brand"]."</div>
							<div class='col-2'>".$row["name_color"]."</div>
							<div class='col-2'>".$row["weight"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function create_filtration_block_brand_and_category(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for=''>Бренд</label>
					<input type='text' class='form-control' id='filtration-brand-and-category-brand' name='filtration-brand-and-category-brand'>
				</div>
				<div class='form-group'>
					<label for='data_prod'>Категория</label>
					<input type='text' class='form-control' id='filtration-brand-and-category-category' name='filtration-brand-and-category-category'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по бренду и категории'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-2'><b>Цвет</b></div>
					<div class='col-2'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по бренду и категории'){
			$brands = $this->get_table_from_brand_and_category(
				$this->filtration_from_brand_and_category[0],
				$this->filtration_from_brand_and_category[1]);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-2'>".$row["name_color"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}
	public function create_filtration_block_price(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for='data_prod'>Цена</label>
					<input type='number' value='0' min='0' max='999999' onkeypress='return event.charCode >= 48 && event.charCode <= 57' class='form-control' id='filtration-price' name='filtration-price'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по цене'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Название бренда</b></div>
					<div class='col-3'><b>Цвет</b></div>
					<div class='col-3'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по цене'){
			$brands = $this->get_table_from_price($this->filtration_from_price);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_brand"]."</div>
							<div class='col-3'>".$row["name_color"]."</div>
							<div class='col-3'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}



	public function create_category_select($name){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.category order by name_category ASC");

		echo "<select class='btn' name='$name'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row['id_category']."'>"
					.$row['name_category']."
				</option>";
			}
		}
		echo "</select>";
	}

	public function create_brand_select($name){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.brand order by name_brand ASC");

		echo "<select class='btn' name='$name'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row['id_brand']."'>"
					.$row['name_brand']."
				</option>";
			}
		}
		echo "</select>";
	}

	public function create_filtration_block_brand_select(){
		echo "
			<form class='form-filtration' method='POST' action=''>
					<label for=''>Бренд</label>";
			$this->create_brand_select("filtration-brand-select");
		echo "
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по бренду из списка'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Цвет</b></div>
					<div class='col-2'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по бренду из списка'){
			$brands = $this->get_table_from_brand_select($this->filtration_from_brand_select);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_color"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}



	public function create_filtration_block_brand_and_category_select(){
		echo "
			<form class='form-filtration' method='POST' action=''>
					<label for=''>Бренд</label>";
			$this->create_brand_select("filtration-brand-and-category-brand-select");
			echo "
					<label for='data_prod'>Категория</label>";
			$this->create_category_select("filtration-brand-and-category-category-select");
		echo "
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по бренду и категории из списка'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Цвет</b></div>
					<div class='col-2'><b>Категория</b></div>
					<div class='col-2'><b>Вес</b></div>
					<div class='col-2'><b>Цена</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по бренду и категории из списка'){
			$brands = $this->get_table_from_brand_and_category_select(
				$this->filtration_from_brand_and_category_select[0],
				$this->filtration_from_brand_and_category_select[1]);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_color"]."</div>
							<div class='col-2'>".$row["name_category"]."</div>
							<div class='col-2'>".$row["weight"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function get_table_from_price($price = null){
		if (!$price and $price!=0){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query =  $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color
			and product.price_product <= $price");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_brand_and_category($brand = null, $category = null){
		if (!$brand and !$category){
			echo "<h3 class='error'> Заполните хотябы одно поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$brand_condition_string    = " and brand.name_brand RLIKE '$brand'" or "";
		$category_condition_string = " and category.name_category RLIKE '$category'" or "";
		$condition_stirng = $brand_condition_string.$category_condition_string;
		$query = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color".$condition_stirng);
		if ($query->num_rows == 0) {
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return 	$query;
	}

	public function get_table_from_brand_and_category_select($brand, $category){
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color
			and product.id_brand = '$brand'
			and category.id_category = '$category'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_brand_select($brand){
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color
			and product.id_brand = '$brand'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_category($category = null){
		if (!$category){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color
			and category.name_category RLIKE '$category'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_name($name = null){
		if (!$name){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				product.id_product, product.name_product, product.weight, product.price_product, 
				brand.name_brand, color.name_color, category.name_category 
			FROM cosmetic_shop.product, cosmetic_shop.brand, cosmetic_shop.color, cosmetic_shop.category
			where product.id_brand = brand.id_brand
			and product.id_category = category.id_category
			and product.id_color = color.id_color
			and product.name_product RLIKE '$name'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	
	public function create_table(mysqli_result $brands = null){
		echo '
			<table class="table">
					<thead>
					<tr>
						<th scope="col">Название</th>
						<th scope="col">Вес</th>
						<th scope="col">Цена</th>
						<th scope="col">Бренд</th>
						<th scope="col">Цвет</th>
						<th scope="col">Категория</th>
						<th scope="col">Изображение</th>
					</tr>
					</thead>
					<tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
						<td>'.$row["name_product"].'</td>
						<td>'.$row["weight"].'</td>
						<td>'.$row["price_product"].'</td>
						<td>'.$row["name_brand"].'</td>
						<td>'.$row["name_color"].'</td>
						<td>'.$row["name_category"].'</td>
						<td><img src="data:image/jpg;charset=utf8;base64, '.$row['image'].'" /></td>
					</tr>';
			}
		}
		echo '
					</tbody>
				</table>';
	}

	public function get_image_by_id($id){
		return "../images/$id.jpg";
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
