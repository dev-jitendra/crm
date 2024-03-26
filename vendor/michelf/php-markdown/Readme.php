<?php






spl_autoload_register(function($class){
	require str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});




use Michelf\Markdown;


$text = file_get_contents('Readme.md');
$html = Markdown::defaultTransform($text);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>PHP Markdown Lib - Readme</title>
	</head>
	<body>
		<?php
			
			echo $html;
		?>
	</body>
</html>
