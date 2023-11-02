function chekInput() {
	if (document.getElementById('title').value != ''){
		return true;
	} else {
		alert("Необходимо заполнить все поля");
		return false;
	}
}
