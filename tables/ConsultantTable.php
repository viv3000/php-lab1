<?php
include_once('Table.php');

class ConsultantTable extends Table{

	public function insert($title, $fon = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $title, $fon)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT INTO 
				cosmetic_shop.consultant
					   (`consultant_fio`, `consultant_fon`) 
				VALUES ('$title',         '$fon')");
	}

	public function update($id, $title, $fon = null){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		}  else if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $title, $fon)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("UPDATE 
				`cosmetic_shop`.`consultant` 
				SET `consultant_fio` = '".$title."', 
					`consultant_fon` = '".$fon."' 
				WHERE (`id_consultant` = '".$id."')");
	}

	private function have_duplicate($connection, $title, $fon){
		return $connection->query(
			"select * from `cosmetic_shop`.`consultant`
			where consultant_fio = '".$title."'
			and   consultant_fon = '".$fon."'
			"
		)->num_rows > 0;
	}

	public function create_page(){
		echo '
			<form method="get" action="">
				<div class="form-group">
					<label for="'.$this->title_field.'"">ФИО консультанта</label>
					<input type="text" id="title" class="form-control" name="'.$this->title_field.'"">
				</div>
				<div class="form-group">
					<label for="consultant_fon">Номер телефона</label>
					<input type="tel" id="fon" class="form-control" name="consultant_fon" placeholder="8(111)-111-1111" onkeyup="phoneFormat(this)">
				</div>
				<br/>
		';
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

	public function create_select(){
		$connection = $this->createConnection();
		$rows = $connection->query("
			select * 
			from cosmetic_shop.".$this->table_name." 
			order by ".$this->title_field." ASC");

		echo "<label>Консультант:</label>  <select class='btn' name='".$this->id_field."'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "<option value='".$row[$this->id_field]."'>".$row[$this->title_field]."</option>";
			}
		}
		echo "</select>";
	}

	
	public function create_table(){
		$connection = $this->createConnection();
		$brands = $connection->query("select * from cosmetic_shop.".$this->table_name);

		echo '
			<table class="table">
				  <thead>
				    <tr>
				      <th scope="col">ФИО</th>
				      <th scope="col">Номер телефона</th>
				    </tr>
				  </thead>
				  <tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
				      <td>'.$row[$this->title_field].'</td>
				      <td>'.$row["consultant_fon"].'</td>
					</tr>';
			}
		}
		echo '
				  </tbody>
				</table>';
	}


}
?>
