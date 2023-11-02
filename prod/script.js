function chekInput() {
	if (    document.getElementById('kol').value       == '' ||
            document.getElementById('data_prod').value == '' ||
            document.getElementById('time_prod').value == ''){    
        alert("Необходимо заполнить все поля");
		return false;
	} else {
        alert("asdf");
		return true;
	}
}

