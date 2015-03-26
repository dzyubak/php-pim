<!DOCTYPE html PUBLIC "-/W3C/DTD XHTML 1.0 Transitional/EN" "http:/www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<html xmlns="http:/www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow">
<title>Saving category</title>

<?php include_once 'includes/head.php'; ?>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="entry_form">

<h1>Saving category</h1>

<p class="center">
<?php if( $form->getCategoryID() != null ): ?>
<!-- CategoryID: <?php //echo $form->getCategoryID(); ?> | -->
 Actions:
 <a href="<?=BASE_URL.'view_category/'.$form->getCategoryID()?>">View</a>
 | Edit
 | <a href="<?=BASE_URL.'delete_category/'.$form->getCategoryID()?>" onclick="return confirm('Click OK to confirm.\nNote: This can NOT be undone!');">Delete</a><br />
<?php endif ?>
</p>

<form action="<?=BASE_URL?>save_category" method="post" name="" id="">
<input type="hidden" name="category_id" value="<?=$form->getCategoryID()?>" />
<table>
	<tr>
		<th>Name</th>
		<td><input type="text" name="name" value="<?=$form->getName()?>" size="66" maxlength="256" /></td>
	</tr>
	<tr>
		<th>Description</th>
		<td><textarea name="description" cols="50" rows="7"><?=$form->getDescription()?></textarea></td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td><input type="submit" name="save_category" value=" Save Category " /></td>
	</tr>
</table>
</form>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>