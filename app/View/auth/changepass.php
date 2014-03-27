<?php
/**
 * @package  sugi example
 * @category view
 */
?><!doctype html>
<html lang="<?=$lang;?>">
<head>
	<meta charset="utf-8" />
	<title><?=__("Change Password");?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<form accept-charset="utf-8" action="" method="POST">
			<div class="form-header">
				<h1><?=__("Change Password");?></h1>
			</div>
			<div class="form-body">
				<?php if (!empty($error)) : ?><div class="error-container"><?= $error; ?></div><?php endif; ?>
				<div>
					<label for="oldpassword"><?=__("Old Password");?>: </label>
					<input type="password" name="oldpassword" id="oldpassword" autocomplete="disabled" class="input-block" tabindex="1" />
				</div>
				<div>
					<label for="password"><?=__("Password");?>: </label>
					<input type="password" name="password" id="password" autocomplete="disabled" class="input-block" tabindex="2" />
				</div>
				<div>
					<label for="password2"><?=__("Confirm Password");?>: </label>
					<input type="password" name="password2" id="password2" autocomplete="disabled" class="input-block" tabindex="3" />
				</div>
				<input type="submit" name="reset" value="<?=__("Save Password");?>" class="button" tabindex="4" />
			</div>
		</form>
	</div>
</div>
</body>
</html>
