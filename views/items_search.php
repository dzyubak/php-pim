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
<title>Searching items</title>

<?php include_once 'includes/head.php'; ?>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="list">

<h1>Searching items</h1>

<table>
<tr>
	<!-- <th>ID</th> -->
	<th>Name</th>
	<!-- <th>CategoryID</th> -->
	<th>Category</th>
	<th>Note</th>
	<th>Task</th>
	<th>DueDateTime</th>
	<th>Priority</th>
	<th>Actions</th>
	<th>Security:<br /><span style="font-size:80%">Last Edited<br />Last Accessed</span></th>
</tr>
<?php if( $items == NULL ): ?>
    <tr><td colspan="8"><center>No results found.</center></td></tr>
<?php else: ?>
<tr>
<?php $i = 0; ?>
<?php foreach( $items as $item ): ?>
<?php
if( $i % 2 ) {
	echo '<tr>';
} else {
	echo '<tr class="odd">';
}
?>
<!-- <td class="center"><?php //echo $item->getItemID(); ?></td> -->
<td><strong><?php echo $item->getName(); ?></strong></td>
<!-- <td><?php //echo $item->getCategoryID(); ?></td> -->
<td><?php echo $item->getCategoryName(); ?></td>
<td><?= mb_strlen($item->getNote(),"UTF-8") < 85 ? $item->getNote() : mb_substr(strip_tags($item->getNote()),0,85,"UTF-8").'...'; ?></td>
<td class="center"><?php echo $item->getTask(); ?></td>

<?php if( $item->getDueDateTime() != null && $item->getPriority() != null ): ?>
<td class="center"><?php echo $item->getDueDateTime(); ?></td>
<td class="center"><?php echo $item->getPriority(); ?></td>
<?php else: ?>
<td class="center">-</td>
<td class="center">-</td>
<?php endif ?>

<td class="center"><a href="<?=BASE_URL.'view_item/'.$item->getItemID()?>">View</a></td>
<td class="center"><span style="font-size:80%">
<?php echo $item->getLastEdited() . "<br />" . $item->getLastAccessed(); ?>
</span></td>
</tr>
<?php $i++; ?>
<?php endforeach ?>
<?php endif ?>

</table>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>