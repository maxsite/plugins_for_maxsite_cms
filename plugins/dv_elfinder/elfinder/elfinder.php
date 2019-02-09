<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'php/confelfinder.php';
// меняем если переименовываем каталоги
$name_plugins = 'dv_elfinder';
$elf_catalog = 'elfinder';
// Не ТРОГАЕМ
define('ELF_URL', 'http://' . $domen . '/application/maxsite/plugins/' . $name_plugins . '/' . $elf_catalog . '/');
// админ панель
$admin_home_url = 'http://' . $domen . '/admin/home';

?>
<script type="text/javascript">
connector_url = "http://<? echo $domen ?>/application/maxsite/plugins/<? echo $name_plugins . '/' . $elf_catalog ?>/php/connector.php";
</script>
		<!-- jQuery and jQuery UI (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ELF_URL; ?>googleapis/1.8.18_themes_smoothness/jquery-ui.css">
<script type="text/javascript" src="<?php echo ELF_URL; ?>googleapis/jquery_1.7.2/jquery.min.js"></script>
				<script type="text/javascript" src="<?php echo ELF_URL; ?>googleapis/jqueryui_1.8.18/jquery-ui.min.js"></script>

		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ELF_URL; ?>css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ELF_URL; ?>css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="<?php echo ELF_URL; ?>js/elfinder.min.js"></script>

		<!-- elFinder translation (OPTIONAL) -->
		<script type="text/javascript" src="<?php echo ELF_URL; ?>js/i18n/elfinder.ru.js"></script>

<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					 url : connector_url,
					 lang: 'ru',             // language (OPTIONAL)
				}).elfinder('instance');
			});
		</script>
	</head>
	<body>
<div class = "top_button"><a href = <?php echo $admin_home_url?> >Админ Панель</a></div>
		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>