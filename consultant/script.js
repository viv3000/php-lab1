var isPhoneValid = (phone) =>{
	var phoneReg = /^(1\s|8|)?((\(\d{3}\))|\d{3})(\-|\s)?(\d{3})(\-|\s)?(\d{4})$/;
	// 1234567890
	// 123-456-7890
	// (123)456-7890
	// 123.456.7890
	// 81234567890
	// 8123-456-7890
	// 8(123)456-7890
	// 8123.456.7890
	// и т.д.
	return phoneReg.test(
		phone.replace(/\D/g, "")
	);
}

var isTryFon = () => {
	var phone = document.getElementById('fon').value;
	return isPhoneValid(phone);
}
function chekInput() {
	if (document.getElementById('title').value == '' ||
		document.getElementById('fon'  ).value == ''){
		alert("Необходимо заполнить все поля");
		return false;
	} else if (!isTryFon()) {
		alert("Неверный формат номера телефона!");
		return false;
	} else {
		return true;
	}
}
