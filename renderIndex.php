<?php
function renderIndex($contentPath, $pathToContext, $title = "Главная"){
	echo "<!DOCTYPE html>";
	echo "<html>";
//	include $pathToContext."templates/head.php";
	include $pathToContext."create_head.php";
	create_head($contentPath, $pathToContext, $title);

	echo "<body>";

	include $pathToContext."templates/header.php";
	include $pathToContext."templates/nav.php";
	include $contentPath;
	include $pathToContext."templates/footer.php";

	echo "</body></html>";
}
?>
