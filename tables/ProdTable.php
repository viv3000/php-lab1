<?php
include_once('Table.php');
include_once('ProductTable.php');
include_once('ConsultantTable.php');

class ProdTable extends Table{
	
	public $filtration_from_FIO = '';
	public $filtration_from_date_start = '0000-00-00';
	public $filtration_from_date_end = '9999-11-11';

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


	public function create_page(){
		echo '
			<form method="get" action="">
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
		$this->create_table();
		$this->create_filtrations();
	}
	
	public function create_table(){
		$connection = $this->createConnection();
		$brands = $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
				product.name_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			where prod.id_product = product.id_product
			and   prod.id_consultant = consultant.id_consultant");

		echo '
			<table class="table">
					<thead>
					<tr>
						<th scope="col">id_prod</th>
						<th scope="col">data_prod</th>
						<th scope="col">time_prod</th>
						<th scope="col">kol</th>
						<th scope="col">consultant_fio</th>
						<th scope="col">name_product</th>
					</tr>
					</thead>
					<tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
						<td>'.$row["id_prod"].'</td>
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
	}

	public function create_filtration_block_FIO(){
		echo "
			<form class='form-filtration' method='get' action=''>
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
		echo "</div>";
	}

	public function get_table_from_FIO($fio){
		$connection = $this->createConnection();
		return $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
			AND   consultant.consultant_fio RLIKE '$fio'");
	}


	public function create_filtration_block_date(){
		echo "
			<form class='form-filtration' method='get' action=''>
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
		$prods = $this->get_table_from_date($this->filtration_from_date_start, $this->filtration_from_date_end);
		if ($this->filtration_from_date_start > $this->filtration_from_date_end){
			echo "<h3 class='error'>Дата начала должна быть меньше чем дата конца!</h3> <div/>";
			return;
		}else if($prods->num_rows > 0){
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
		echo "</div>";
	}

	public function get_table_from_date($date_start, $date_end){
		$connection = $this->createConnection();
		return $connection->query("
			SELECT 
				prod.id_prod, prod.data_prod, prod.time_prod, prod.kol, 
				consultant.consultant_fio, 
			    product.name_product, product.price_product
			FROM cosmetic_shop.prod, cosmetic_shop.consultant, cosmetic_shop.product
			WHERE prod.id_product = product.id_product
			AND   prod.id_consultant = consultant.id_consultant
			AND   prod.data_prod > '$date_start'
			AND   prod.data_prod < '$date_end'");
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
