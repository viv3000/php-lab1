function chekInput() {
	if (document.getElementById('name_product').value  == '' ||
		document.getElementById('price_product').value == '' ||
		document.getElementById('weight').value        == '' ){
		alert("Необходимо заполнить все поля");
		return false;
	} else {
		return true;
	}
}

