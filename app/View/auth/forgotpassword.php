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
	<title><?=__("Forgot Password");?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<form accept-charset="utf-8" action="" method="post">
			<div class="form-header">
				<h1><?=__("Forgot Password");?></h1>
			</div>
			<div class="form-body">
				<?php if (!empty($error)) : ?><div class="error-container"><?= $error; ?></div><?php endif; ?>
				<div>
					<label for="email"><?=__("Email");?>: </label>
					<input type="text" name="email" value="<?= $email; ?>" id="email" autocapitalize="off" autofocus="autofocus" class="input-block" tabindex="1" />
				</div>
				<input type="submit" value="<?=__("Submit");?>" class="button" tabindex="2" />
			</div>
		</form>
	</div>
</div>
</body>
</html>
