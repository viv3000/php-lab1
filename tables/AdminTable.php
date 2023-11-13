<?php
include_once('Table.php');

class AdminTable extends Table{
	public static $user;
	public $table;
	public function __construct(){
		$this->table_name = "admin";
		$this->id_field = "id_admin";
	}
	public function get_id(){
		return $_SESSION['auth_admin_id'];
	}

	public function insert($login, $password='', $fio='', $email='', $phone='', $position=''){
		$status = false;
		if (!$login)    {
			echo "<h3 class='error'> Необходимо ввести логин!</h3>"; 
			$status = true;
		}if (!$password) {
			echo "<h3 class='error'> Необходимо ввести пароль!</h3>";
			$status = true;
		}if (!$fio) {
			echo "<h3 class='error'> Необходимо ввести ФИО!</h3>";
			$status = true;
		}if (!$email) {
			echo "<h3 class='error'> Необходимо ввести E-mail!</h3>";
			$status = true;
		}if (!$phone) {
			echo "<h3 class='error'> Необходимо ввести номер телефона!</h3>";
			$status = true;
		}if (!$position) {
			echo "<h3 class='error'> Необходимо ввести должност!</h3>";
			$status = true;
		}if ($status) return;

		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		} else if($this->have_duplicate($connection, $login)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else $connection->query("INSERT INTO 
				cosmetic_shop.admin
					   (`login`,  `password`,  `fio`,  `email`,  `phone`,  `position`) 
				VALUES ('$login', '$password', '$fio', '$email', '$phone', '$position')");
	}

	public function update($id, $login, $password='', $fio='', $email='', $phone='', $position=''){
		$status = false;
		var_dump($login);
		if (!$login)    {
			echo "<h3 class='error'> Необходимо ввести логин!</h3>"; 
			$status = true;
		}if (!$password) {
			echo "<h3 class='error'> Необходимо ввести пароль!</h3>";
			$status = true;
		}if (!$fio) {
			echo "<h3 class='error'> Необходимо ввести ФИО!</h3>";
			$status = true;
		}if (!$email) {
			echo "<h3 class='error'> Необходимо ввести E-mail!</h3>";
			$status = true;
		}if (!$phone) {
			echo "<h3 class='error'> Необходимо ввести номер телефона!</h3>";
			$status = true;
		}if (!$position) {
			echo "<h3 class='error'> Необходимо ввести должность!</h3>";
			$status = true;
		}if ($status) return;

		$connection = $this->createConnection();
		if ($connection->connect_error){
			echo '<h3 class="error">Не удалось подключиться к базе банных</h3>';
		}  else if($id == null){
			echo '<h3 class="error">Невозможно изменить несуществующую запись!</h3>';
		} else if($this->have_duplicate($connection, $login)) {
			echo '<h3 class="error">Невозможно вставить дублирующую запись!</h3>';
		} else {
			$connection->query("UPDATE 
				`cosmetic_shop`.`admin` 
				SET  
					`login`    = '$login',
					`password` = '$password',
					`fio`      = '$fio',
					`email`    = '$email',
					`phone`    = '$phone',
					`position` = '$position'
				WHERE (`id_admin` = '".$id."')");
			unset($_SESSION['auth_admin']);
			header("Location: ../login");
		}
	}

	private function have_duplicate($connection, $login){
		return $connection->query("
			select * from `cosmetic_shop`.`admin`
			where login = '$login'
			and   login != '".$_SESSION['auth_admin_login']."'
			"
		)->num_rows > 0;
	}

	public function check_access(){
		$connection = $this->createConnection();
		return $connection->query("
			select * from `cosmetic_shop`.`admin`
			where id_admin = '".$_SESSION['auth_admin_id']."'
			"
		)->num_rows > 0;
	}

	public function get_admin(){
		$connection = $this->createConnection();
		return $connection->query("
			select * from `cosmetic_shop`.`admin`
			where id_admin = '".$_SESSION['auth_admin_id']."'
			"
		)->fetch_assoc();
	}

	public function create_page_login(){
		echo "
			<form method='POST' action=''>
					<div class='form-group'>
						<label>Логин</label>
						<input type='text' id='title' class='form-control' name='login'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Пароль</label>
						<input type='password' class='form-control' name='password'>
					</div>
					<br/>
					<input type='submit' class='btn btn-primary' name='submit' value='Войти'>
			</form>
		";
	}

	public function create_page_edit(){
		echo "
			<form class='admin-form' method='POST' action=''>
				<div class='admin-form-input'>
					<h2>Введите данные</h2>
					<div class='form-group'>
						<label>Логин</label>
						<input type='text' id='title' class='form-control' name='login' value='".$this->get_admin()['login']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Пароль</label>
						<input type='password' class='form-control' name='password' value='".$this->get_admin()['password']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>ФИО</label>
						<input type='text' class='form-control' name='fio' value='".$this->get_admin()['fio']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Должность</label>
						<input type='text' class='form-control' name='position' value='".$this->get_admin()['position']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>E-mail</label>
						<input type='text' class='form-control' name='email' value='".$this->get_admin()['email']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Номер телефона</label>
						<input type='text' class='form-control' name='phone' value='".$this->get_admin()['phone']."'>
					</div>
				</div>
				<br/>
				<div class='admin-form-buttons'>
					<h2>Выбирите действие</h2>
					<div class='buttons'>
						<input type='submit' class='btn btn-primary' name='submit' value='Изменить'>
						<input type='submit' class='btn btn-primary' name='submit' value='Удалить'>
						<input type='submit' class='btn btn-primary' name='submit' value='Работа с приложением'>
					</div>
				</div>
			</form>
		";
	}



	public function create_page(){
		echo "
			<form class='admin-form' method='POST' action=''>
				<div class='admin-form-input'>
					<h2>Введите данные</h2>
					<div class='form-group'>
						<label>Логин</label>
						<input type='text' id='title' class='form-control' name='login' value='".$_POST['login']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Пароль</label>
						<input type='password' class='form-control' name='password' value='".$_POST['password']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>ФИО</label>
						<input type='text' class='form-control' name='fio' value='".$_POST['fio']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Должность</label>
						<input type='text' class='form-control' name='position' value='".$_POST['position']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>E-mail</label>
						<input type='text' class='form-control' name='email' value='".$_POST['email']."'>
					</div>
					<br/>
					<div class='form-group'>
						<label>Номер телефона</label>
						<input type='text' class='form-control' name='phone' value='".$_POST['phone']."'>
					</div>
				</div>
				<br/>
				<div class='admin-form-buttons'>
					<h2>Выбирите действие</h2>
					<div class='buttons'>
						<input type='submit' class='btn btn-primary' name='submit' value='Просмотр'>
						<input type='submit' class='btn btn-primary' name='submit' value='Добавление'>
						<input type='submit' class='btn btn-primary' name='submit' value='Изменение'>
						<input type='submit' class='btn btn-primary' name='submit' value='Работа с приложением'>
					</div>
				</div>
			</form>
			$this->table
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
			and password = '$password'"
		)->fetch_assoc();

		if ($rows['login'] == null){
			echo "<h3 class='error'>Неверный логин или пароль</h3>";
			unset($_SESSION['auth_admin']);
		}else{
			$_SESSION['auth_admin'] = 'yes_auth';
			$_SESSION['auth_admin_id'] = $rows['id_admin'];
			$_SESSION['auth_admin_login'] = $login;
			$_SESSION['admin_role'] = $rows['position'];

			header("Location: ../administrators");
		}
	}

	public function view_table(){
		$this->table = "
			<table class=\"table\">
					<thead>
					<tr>
						<th scope=\"col\">Логин</th>
						<th scope=\"col\">Пароль</th>
						<th scope=\"col\">ФИО</th>
						<th scope=\"col\">Должность</th>
						<th scope=\"col\">E-mail</th>
						<th scope=\"col\">Номер телефона</th>
					</tr>
					</thead>
					<tbody>";
		$connection = $this->createConnection();
		$admins = $connection->query("select * from cosmetic_shop.admin");
		if($admins->num_rows > 0){
			while($row = $admins->fetch_assoc()){
				$this->table .= '
					<tr>
						<td>'.$row["login"].'</td>
						<td>'.$row["password"].'</td>
						<td>'.$row["fio"].'</td>
						<td>'.$row["position"].'</td>
						<td>'.$row["email"].'</td>
						<td>'.$row["phone"].'</td>
					</tr>';
			}
		}
		$this->table .= '
					</tbody>
				</table>';
	}

	public function exit(){
		$_SESSION['auth_admin'] = 'no_auth';
	}
}
?>
