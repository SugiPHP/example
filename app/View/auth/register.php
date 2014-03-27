<?php
/**
 * @package  sugi example
 * @category view
 */
?>
<!DOCTYPE html>
<html lang="<?=$lang;?>">
<head>
	<meta charset="utf-8" />
	<title><?=__("Sign Up");?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<form accept-charset="utf-8" action="" method="post">
			<div class="form-header">
				<h1><?=__("Sign Up");?></h1>
			</div>
			<div class="form-body">
				<?php if (!empty($error)) : ?><div class="error-container"><?= __($error); ?></div><?php endif; ?>
				<div>
					<label for="username"><?=__("Username");?>: </label>
					<input type="text" name="username" value="<?= $username; ?>" id="username" autocapitalize="off" autofocus="autofocus" class="input-block" tabindex="1" />
				</div>
				<div>
					<label for="email"><?=__("Email");?>: </label>
					<input type="text" name="email" value="<?= $email; ?>" id="email" class="input-block" tabindex="2" />
				</div>
			<?php if ($showPasswords): ?>
				<div>
					<label for="password"><?=__("Password");?>: </label>
					<input type="password" name="password" id="password" autocomplete="disabled" class="input-block" tabindex="3" />
				</div>
				<div>
					<label for="password2"><?=__("Confirm Password");?>: </label>
					<input type="password" name="password2" id="password2" autocomplete="disabled" class="input-block" tabindex="4" />
				</div>
			<?php endif; ?>
				<input type="submit" value="<?=__("Sign Up");?>" class="button" tabindex="5" />
			</div>
		</form>
	</div>
</div>
</body>
</html>
