<?php
	function create_head($contentPath, $pathToContext, $title){
		echo '
			<head>
				<meta charset="UTF-8">
				<link rel="stylesheet" href="'.$pathToContext.'css/bootstrap/dist/css/bootstrap.css"/>
				<link rel="stylesheet" href="'.$pathToContext.'css/master.css"/>
				<script type="text/javascript" src="script.js"></script>
				<title>'.$title.'</title>
			</head>';
	}
?>
