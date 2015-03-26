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
<title>Listing categories</title>

<?php include_once 'includes/head.php'; ?>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="list">

<h1>Listing categories</h1>

<table>
<tr>
    <th>Name</th>
    <th>Description</th>
    <th>Actions</th>
</tr>
<tr>
<?php $i = 0; ?>
<?php foreach( $categories as $category ): ?>
<?php
if( $i % 2 ) {
	echo '<tr>';
} else {
	echo '<tr class="odd">';
}
?>
    <!-- <td class="center"><?php //echo $category->getCategoryID(); ?></td> -->
    <td><strong><?php echo $category->getName(); ?></strong></td>
    <td><?php echo $category->getDescription(); ?></td>
    <td class="center"><a href="<?=BASE_URL.'view_category/'.$category->getCategoryID()?>">View</a></td>
</tr>
<?php $i++; ?>
<?php endforeach ?>

</table>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>