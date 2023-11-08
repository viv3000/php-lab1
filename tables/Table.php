<?php
class Table{
	public $table_name;
	public $id_field;
	public $title_field;

	public function __construct($table_name, $id_field, $title_field){
		$this->table_name = $table_name;
		$this->id_field = $id_field;
		$this->title_field = $title_field;
	}

	public function createConnection(){
		$connection = new mysqli("127.0.0.1:3306", "vi", "dbrnjh", "cosmetic_shop");
		//$connection = new mysqli("127.0.0.1:3306", "root", "root", "cosmetic_shop");
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} 
		return $connection;
	}

	public function insert($title){
		$connection = $this->createConnection();
		if($this->have_duplicate($connection, $title)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT INTO 
				`cosmetic_shop`.`".$this->table_name."` 
				       (`".$this->title_field."`) 
				VALUES ('".$title."')");
	}

	public function update($id, $title){
		$connection = $this->createConnection();
		if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $title)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("UPDATE 
				`cosmetic_shop`.`".$this->table_name."`
				SET   `". $this->title_field."` = '".$title."' 
				WHERE (`".$this->id_field."`   = '".$id."');");
	}
	
	private function have_duplicate($connection, $title){
		return $connection->query(
			"select * from $this->table_name
			where $this->title_field = '$title'"
		)->num_rows > 0;
	}

	public function delete($id){
		$connection = $this->createConnection();
		if($id == null){
			echo '<h3 class="error">Невозможно удалить несуществующую запись!</h3>';
		} else $connection->query("
				DELETE FROM 
				cosmetic_shop.".$this->table_name." 
				WHERE (".$this->id_field." = '".$id."');");
	}
	
	public function create_table(){
		$connection = $this->createConnection();
		$brands = $connection->query("select * from cosmetic_shop.".$this->table_name);

		echo '
			<table class="table">
				  <thead>
				    <tr>
				      <th scope="col">'.$this->id_field.'</th>
				      <th scope="col">'.$this->title_field.'</th>
				    </tr>
				  </thead>
				  <tbody>';
				
		if($brands->num_rows > 0){
			while($row = $brands->fetch_assoc()){
				echo '
					<tr>
				      <td>'.$row[$this->id_field   ].'</td>
				      <td>'.$row[$this->title_field].'</td>
					</tr>';
			}
		}
		echo '
				  </tbody>
				</table>';
	}

}


?>
