<?php
/**
 * @package  sugi example
 * @category view
 */
?>
<!doctype html>
<html lang="<?=$lang;?>">
<head>
	<meta charset="utf-8" />
	<title><?= $title; ?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<div class="form-header">
			<h1><?= $title; ?></h1>
		</div>
		<div class="form-body">
			<?php if (!empty($error)) : ?><div class="error-container"><?= $error; ?></div><?php endif; ?>
			<?php if (!empty($message)) : ?><div class="message-container"><?= $message; ?></div><?php endif; ?>
			<?php if (!empty($redirect)) : ?><a class="button" href="<?= $redirect;?>"><?= __("Continue");?></a><?php endif; ?>
		</div>
	</div>
</div>
</body>
</html>
