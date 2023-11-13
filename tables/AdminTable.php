<?php
include_once('Table.php');

class AdminTable extends Table{
	public static $user;
	public function __construct(){
		$this->table_name = "admin";
		$this->id_field = "id_admin";
	}

	public function insert($login, $password='', $fio='', $email='', $phone='', $position=''){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $login)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT INTO 
				cosmetic_shop.admin
					   (`login`,  `password`,  `fio`, `email`,  `phone`,  `position`) 
				VALUES ('$login', '$password', '$fio' '$email', '$phone', '$position')");
	}

	public function update($id, $login, $password='', $fio='', $email='', $phone='', $position=''){
		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		}  else if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $login)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("UPDATE 
				`cosmetic_shop`.`admin` 
				SET  
					`login`    = '$login',
					`password` = '$password',
					`fio`      = '$fio',
					`email`    = '$email',
					`phone`    = '$phone',
					`position` = '$position'
				WHERE (`id_admin` = '".$id."')");
	}

	private function have_duplicate($connection, $login){
		return $connection->query("
			select * from `cosmetic_shop`.`admin`
			where login = '$login'
			"
		)->num_rows > 0;
	}

	public function create_page(){
		echo "
			<form method='POST' action=''>
				<h2>Введите логин и пароль</h2>
				<div class='form-group'>
					<input type='text' id='title' class='form-control' placeholder='Логин' name='login'>
				</div>
				<br/>
				<div class='form-group'>
					<input type='password' class='form-control' name='password' placeholder='Пароль' onkeyup='emailFormat(this)'>
				</div>
				<br/>
				<input type='submit' class='btn btn-primary' name='submit' value='Войти'>
			</form>
		";
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
	
	public function login($login, $password){
		if ($login == '' or $password == ''){
			echo "<h3 class='error'>Необходимо заполнить все поля!</h3>";
			return;
		}
		$connection = $this->createConnection();
		$rows = $connection->query("
			select * 
			from cosmetic_shop.admin 
			where login = '$login'
			and password = md5('$password')"
		)->fetch_assoc();

		if ($rows['login'] == null){
			echo "<h3 class='error'>Неверный логин или пароль</h3>";
		}else{
			$_SESSION['auth_admin'] = 'yes_auth';
			$_SESSION['auth_admin_id'] = $rows['id_admin'];
			$_SESSION['auth_admin_login'] = $login;
			$_SESSION['admin_role'] = $rows['position'];

			echo "<h3 class='coplite'>Вы вошли!</h3>";
		}
	}

	public function exit(){
		$_SESSION['auth_admin'] = 'no_auth';
	}
}
?>
