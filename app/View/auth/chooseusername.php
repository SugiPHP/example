<?php
/**
 * @package  sugi example
 * @category view
 */
?><!doctype html>
<html lang="<?=$lang;?>">
<head>
	<meta charset="utf-8" />
	<title><?=__("Choose Username");?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<form accept-charset="utf-8" action="" method="POST">
			<div class="form-header">
				<h1><?=__("Choose Username");?></h1>
			</div>
			<div class="form-body">
				<?php if (!empty($error)) : ?><div class="error-container"><?= $error; ?></div><?php endif; ?>
				<div>
					<label for="username"><?=__("Username");?>: </label>
					<input type="text" name="username" id="username" autocomplete="disabled" class="input-block" tabindex="1" />
				</div>
				<input type="submit" name="reset" value="<?=__("Save username");?>" class="button" tabindex="2" />
			</div>
		</form>
	</div>
</div>
</body>
</html>
