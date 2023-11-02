<?php

include_once('Table.php');
class ColorTable extends Table{
	public function create_page(){
		echo '
			<form method="get" action="">
				<div class="form-group">
					<label for="'.$this->title_field.'"">Название цвета</label>
					<input type="text"  id="title" class="title form-control" name="'.$this->title_field.'"">
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

		echo "<label>Цвет:</label>  <select class='btn' name='".$this->id_field."'>";
		if($rows->num_rows > 0){
			while($row = $rows->fetch_assoc()){
				echo "<option value='".$row[$this->id_field]."'>".$row[$this->title_field]."</option>";
			}
		}
		echo "</select>";
	}
}
?>
