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
	<title><?=__("Sign In");?></title>
	<style><?php include APPPATH."css/auth.css" ?></style>
</head>
<body>
<div class="container">
	<div class="form">
		<form accept-charset="utf-8" action="" method="post" class="">
			<div class="form-header">
				<h1><?=__("Sign In");?></h1>
			</div>
			<div class="form-body">
				<?php if (!empty($error)) : ?><div class="error-container"><?= __($error); ?></div><?php endif; ?>
				<div>
					<label for="username"><?=__("Username or Email");?>: </label> (<a href="/auth/register"><?=__("Sign Up");?></a>)
					<input type="text" name="username" value="<?= $username; ?>" id="username" autocapitalize="off" autofocus="autofocus" class="input-block" tabindex="1" />
				</div>
				<div>
					<label for="password"><?=__("Password");?>: </label> (<a href="/auth/forgotpassword"><?=__("Forgot Password");?></a>)
					<input type="password" name="password" id="password" autocomplete="disabled" class="input-block" tabindex="2" />
				</div>
				<input type="submit" value="<?=__("Sign In");?>" class="button" tabindex="3" />
				<?php if (!empty($showRememberMe)) : ?>
				<label for="rememberme" class="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="1" class="checkbox" tabindex="4"<?= (empty($remember)) ? "" : ' checked="checked"' ;?>/> <?=__("Remember me");?></label>
				<?php endif; ?>
			</div>
		</form>
	</div>
</div>
</body>
</html>
