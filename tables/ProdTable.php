<?php
include_once('Table.php');
include_once('ProductTable.php');
include_once('ConsultantTable.php');

class ProdTable extends Table{
	public $POST = array("submit" => '');
	public $filtration_from_FIO = '';
	public $filtration_from_date_start = '0000-00-00';
	public $filtration_from_date_end = '9999-11-11';

	public $filtration_from_name_select = '';
	public $filtration_from_fio_select = '';

	public function __construct(){
		$this->table_name = "prod";
		$this->id_field = "id_prod";
	}

	public function insert($id_product, $id_consultant = null, $data_prod = null, $time_prod = null, $kol = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $id_product, $id_consultant, $data_prod, $time_prod, $kol)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("
			INSERT INTO `cosmetic_shop`.`prod` 
				   (`id_product`,  `id_consultant`,  `data_prod`,  `time_prod`,  `kol`) 
			VALUES ('$id_product', '$id_consultant', '$data_prod', '$time_prod', '$kol')");
	}

	public function update($id, $id_product, $id_consultant = null, $data_prod = null, $time_prod = null, $kol = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		}  else if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $id_product, $id_consultant, $data_prod, $time_prod, $kol)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("UPDATE 
				`cosmetic_shop`.`prod` 
				SET 
				`id_product` = $id_product, 
				`id_consultant` = $id_consultant, 
				`data_prod` = '$data_prod', 
				`time_prod` = '$time_prod', 
				`kol` = $kol 
				WHERE (`id_prod` = '$id')");
	}

	private function have_duplicate($connection, $id_product, $id_consultant, $data_prod, $time_prod, $kol){
		return $connection->query(
			"SELECT prod.*
				FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
				where prod.id_product = product.id_product
				and   prod.id_consultant = consultant.id_consultant
                and prod.id_product = $id_product
				and prod.id_consultant = $id_consultant
                and prod.data_prod = '$data_prod'
                and prod.time_prod = '$time_prod'
				and prod.kol = $kol
			"
		)->num_rows > 0;
	}


	public function create_page($sort_field = null){
		echo '
			<form method="POST" action="">
				<div class="form-group">
					<label for="data_prod">Дата</label>
					<input type="date" class="form-control" id="data_prod" name="data_prod">
				</div>
				<div class="form-group">
					<label for="time_prod">Время</label>
					<input type="time" class="form-control" id="time_prod" name="time_prod">
				</div>
				<div class="form-group" style="width: 100px;">
					<label for="kol">Количество</label>
					<input value="0" min="0" max="99" type="number" id="kol" class="form-control" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="kol"/>
				</div>
				<br/>
		';
		$consultant = new ConsultantTable("consultant", "id_consultant", "consultant_fio");
		$product    = new ProductTable("prod", "id_prod");
		$consultant->create_select();
		echo "<br/>";
		$product->create_select();
		echo "<br/>";
		$this->create_select();
		echo "<br/>";
		echo '
				<br/>
				<br/>
				<input type="submit" class="btn btn-primary" name="submit" value="Добавить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Изменить" onclick="return chekInput()">
				<input type="submit" class="btn btn-primary" name="submit" value="Удалить", onclick="return confirm(\'Вы действительно хотите удалить запись?\')">
			</form>
		';
		echo "
			</form>
			<form action='' method='POST'>
				<select name='sort-field' class='btn'>
					<option>Название товара</option>
					<option>ФИО консультанта</option>
					<option>Дата продажи</option>
				</select>
				<button type='submit' class='btn btn-primary'>Сортировать</button>
			</form>
		";
		switch ($sort_field){
			case 'Название товара':
				$this->create_table('name_product');
				break;
			case 'ФИО консультанта':
				$this->create_table('consultant_fio');
				break;
			case 'Дата продажи':
				$this->create_table('data_prod');
				break;
			default:
				$this->create_table();
		}
		$this->create_filtrations();
	}
	
	public function create_table($sort_field = 'name_product'){
		$connection = $this->createConnection();
		$brands = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
				product.name_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			where prod.id_product = product.id_product
			and   prod.id_consultant = consultant.id_consultant 
			order by $sort_field ASC");

		echo '
			<table class="table">
					<thead>
					<tr>
						<th scope="col">Дата продажи</th>
						<th scope="col">Время продажи</th>
						<th scope="col">Обьем</th>
						<th scope="col">ФИО консультанта</th>
						<th scope="col">Продукт</th>
					</tr>
					</thead>
					<tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
						<td>'.$row["data_prod"].'</td>
						<td>'.$row["time_prod"].'</td>
						<td>'.$row["kol"].'</td>
						<td>'.$row["consultant_fio"].'</td>
						<td>'.$row["name_product"].'</td>
					</tr>';
			}
		}
		echo '
					</tbody>
				</table>';
	}
	
	public function create_filtrations(){
		echo "<h2>Фильтрация по вводу с клавиатуры</h2>";
		echo "<div class='filtration-keyboard'>";
		echo "<h3>Фильтрация по ФИО консультанта</h3>";
		$this->create_filtration_block_FIO();
		echo "</br>";
		echo "<h3>Фильтрация по промежутку между датами</h3>";
		$this->create_filtration_block_date();
		echo "</div>";

		echo "<h2>Фильтрация по значению из списка</h2>";
		echo "<div class='filtration-keyboard'>";
		echo "<h3>Фильтрация по ФИО консультанта</h3>";
		$this->create_filtration_block_FIO_select();
		echo "<h3>Фильтрация по названию товара</h3>";
		$this->create_filtration_block_name_select();
		echo "</div>";
	}

	public function create_filtration_block_FIO(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for=''>ФИО консультанта</label>
					<input type='text' class='form-control' id='filtration-fio' name='filtration-fio'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по ФИО консультанта'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>ФИО консультанта</b></div>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-2'><b>Дата продажи</b></div>
					<div class='col-2'><b>Количество</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по ФИО консультанта'){
			$prods = $this->get_table_from_FIO($this->filtration_from_FIO);
			if($prods->num_rows > 0){
				while($row = $prods->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["consultant_fio"]."</div>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-2'>".$row["data_prod"]."</div>
							<div class='col-2'>".$row["kol"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function get_table_from_FIO($fio = null){
		if (!$fio){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
			AND   consultant.consultant_fio RLIKE '$fio'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}


	public function create_filtration_block_date(){
		echo "
			<form class='form-filtration' method='POST' action=''>
				<div class='form-group'>
					<label for=''>Дата начала</label>
					<input type='date' class='form-control' id='filtration-date-start' name='filtration-date-start'>
				</div>
				<div class='form-group'>
					<label for='data_prod'>Дата конца</label>
					<input type='date' class='form-control' id='filtration-date-end' name='filtration-date-end'>
				</div>
				</br>
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по дате'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>ФИО консультанта</b></div>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-2'><b>Дата продажи</b></div>
					<div class='col-2'><b>Цена товара</b></div>
					<div class='col-2'><b>Количество</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по дате'){
			$prods = $this->get_table_from_date($this->filtration_from_date_start, $this->filtration_from_date_end);
			if($prods->num_rows > 0){
				while($row = $prods->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["consultant_fio"]."</div>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-2'>".$row["data_prod"]."</div>
							<div class='col-2'>".$row["price_product"]."</div>
							<div class='col-2'>".$row["kol"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function create_name_select($name){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.product order by name_product ASC");

		echo "<select class='btn' name='$name'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row['id_product']."'>"
					.$row['name_product']."
				</option>";
			}
		}
		echo "</select>";
	}

	public function create_FIO_select($name){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.consultant order by consultant_fio ASC");

		echo "<select class='btn' name='$name'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row['id_consultant']."'>"
					.$row['consultant_fio']."
				</option>";
			}
		}
		echo "</select>";
	}

	public function create_filtration_block_FIO_select(){
		echo "
			<form class='form-filtration' method='POST' action=''>
					<label for=''>ФИО консультанта</label>";
			$this->create_FIO_select("filtration-fio-select");
		echo "
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по ФИО консультанта из списка'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>Категория</b></div>
					<div class='col-2'><b>Дата продажи</b></div>
					<div class='col-2'><b>Количество</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по ФИО консультанта из списка'){
			$brands = $this->get_table_from_FIO_select($this->filtration_from_FIO_select);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["name_category"]."</div>
							<div class='col-2'>".$row["data_prod"]."</div>
							<div class='col-2'>".$row["kol"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function create_filtration_block_name_select(){
		echo "
			<form class='form-filtration' method='POST' action=''>
					<label for=''>Название товара</label>";
			$this->create_name_select("filtration-name-select");
		echo "
				<input type='submit' class='btn btn-primary' name='submit' value='Фильтровать по названию товара из списка'>
			</form>";

		echo "
			<div class='container my-table'>
				<div class='row row-table header-table'>
					<div class='col-3'><b>Название товара</b></div>
					<div class='col-3'><b>ФИО консультанта</b></div>
					<div class='col-2'><b>Дата продажи</b></div>
					<div class='col-2'><b>Количество</b></div>
				</div>";
		if ($this->POST['submit'] == 'Фильтровать по названию товара из списка'){
			$brands = $this->get_table_from_name_select($this->filtration_from_name_select);
			if($brands->num_rows > 0){
				while($row = $brands->fetch_assoc()){
					echo "
						<div class='row row-table'>
							<div class='col-3'>".$row["name_product"]."</div>
							<div class='col-3'>".$row["consultant_fio"]."</div>
							<div class='col-2'>".$row["data_prod"]."</div>
							<div class='col-2'>".$row["kol"]."</div>
						</div>";
				}
			}
		}
		echo "</div>";
	}

	public function get_table_from_FIO_select($fio = null){
		if (!$fio){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product, product.price_product,
                category.name_category
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product, cosmetic_shop.category
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
            and   category.id_category = product.id_category
			AND   prod.id_consultant = '$fio'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_name_select($name = null){
		if (!$name){
			echo "<h3 class='error'> Заполните поле для фильтрации!<h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product, product.price_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
			AND   prod.id_product = '$name'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function get_table_from_date($date_start = null, $date_end = null){
		if ($date_start == '' or $date_end == ''){
			echo "<h3 class='error'> Заполните все поля для фильтрации!<h3>";
			return;
		}
		if ($date_start > $date_end){
			echo "<h3 class='error'>Дата начала должна быть меньше чем дата конца!</h3>";
			return;
		}
		$connection = $this->createConnection();
		$query = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product, product.price_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
			AND   prod.data_prod > '$date_start'
			AND   prod.data_prod < '$date_end'");
		if ($query->num_rows == 0){
			echo "<h3 class='error'>Записи не найдены</h3>";
		}
		return $query;
	}

	public function create_select(){
		$connection = $this->createConnection();
		$rows = $connection->query("select * from cosmetic_shop.".$this->table_name." order by data_prod ASC");

		echo "<label>Продажа: </label> <select class='btn' name='id_prod'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "
				<option value='".$row[$this->id_field]."'>"
					.$row['data_prod']." ".$row['time_prod']."
				</option>";
			}
		}
		echo "</select>";
	}
}

?>
