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
<title>Viewing Category</title>

<?php include_once 'includes/head.php'; ?>
<?php //include_once 'head_syntaxhighlighter.php'; ?>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="entry">

<h1>Viewing Category</h1>

<p class="center">
Actions:
 View
 | <a href="<?=BASE_URL.'edit_category/'.$category->getCategoryID()?>">Edit</a>
 | <a href="<?=BASE_URL.'delete_category/'.$category->getCategoryID()?>" onclick="return confirm('Click OK to confirm.\nNote: This can NOT be undone!');">Delete</a><br />
</p>

<!--
Directory creation (for files)
<p><form action="<?php //echo Page::url('view_category', $category->getCategoryID()); ?>" method="post" name="" id="">
<?php //echo $EchoDir; ?>
</form></p>
-->

<table>
	<tr>
		<th>Name</th>
		<td><?=$category->getName()?></td>
	</tr>
	<tr>
		<th>Description</th>
		<td><?=$category->getDescription()?></td>
	</tr>
</table>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>