<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
 * Copyright (C) 2014, 2015 Dmytro Dzyubak
 * 
 * This file is part of php-pim.
 * 
 * php-pim is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php-pim is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php-pim. If not, see <http://www.gnu.org/licenses/>.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Sign In</title>

<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>views/styles/main.css" />

</head>

<body>

<form id="authorization_form" name="authorization_form" action="<?=BASE_URL?>authorize" method="post">
<h1>Sign in</h1>
<img class="center" src="<?=BASE_URL?>views/images/sing_in.png" alt="Personal Information Manager sing in" height="135" width="135" /><br /><br />
<?php
if(isset($_SESSION['error'])) {
	echo '<p class="center"><strong><font color="red">'.$_SESSION['error'].'</font></strong></p>';
}
unset($_SESSION['error']);
?>
<p class="center">Username: <input type="text" name="username" style="width:120px;" /><br />
Password: <input type="password" name="password" style="width:120px;" /><br /><br />
<input type="submit" value="Sign in" /></p>
</form>

</body>
</html>