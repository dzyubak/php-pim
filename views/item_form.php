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
<title>Saving Item</title>

<?php include_once 'includes/head.php'; ?>
<?php include_once 'includes/head_tinymce.php'; ?>
<?php include_once 'includes/head_syntaxhighlighter.php'; ?>

<script src="<?=BASE_URL?>views/scripts/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="<?=BASE_URL?>views/scripts/jquery-ui.css" />
<script src="<?=BASE_URL?>views/scripts/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=BASE_URL?>views/scripts/jquery-ui-timepicker-addon.css" />
<script src="<?=BASE_URL?>views/scripts/jquery-ui-timepicker-addon.js"></script>

<script>
$(function() {
$("#datetimepicker").datetimepicker({
		dateFormat: 'yy-mm-dd',
		showSecond: true,
		timeFormat: 'HH:mm:ss'
	}).val();
});

$(document).ready(function () {
	if ($('#task_checkbox').is(":checked")) {
		// do not hide anything
	} else {
		$("#task_due_date_time, #task_priority").delay(2000).fadeOut("slow");
	}
});

$(document).ready(function () {
	$('#task_checkbox').change(function () {
		if(this.checked)
			$('#task_due_date_time, #task_priority').fadeIn('slow');
		else
			$('#task_due_date_time, #task_priority').fadeOut('slow');
	});
});
</script>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="entry_form">

<h1>Saving item</h1>

<p class="center">
<?php if( $form->getItemID() != null ): ?>
<!-- ItemID: <?php //echo $form->getItemID(); ?> | -->
 Actions:
 <a href="<?=BASE_URL.'view_item/'.$form->getItemID()?>">View</a>
 | Edit
 | <a href="<?=BASE_URL.'delete_item/'.$form->getItemID()?>" onclick="return confirm('Click OK to confirm.\nNote: This can NOT be undone!');">Delete</a><br />
<?php endif ?>
</p>

<form action="<?=BASE_URL?>save_item" method="post" name="" id="">
<input type="hidden" name="item_id" value="<?php echo $form->getItemID(); ?>" />
<table>
	<tr>
		<th>Name</th>
		<td><input type="text" name="name" value="<?php echo $form->getName(); ?>" size="91" maxlength="256" /></td>
	</tr>
	<tr>
		<th>Category</th>
		<td>
		<select name="category_id">
		<?php
		foreach( $categoriesArr as $categoryID => $name ) {
			echo '<option value="'.$categoryID.'"';
			if( $categoryID == $form->getCategoryID() ) { echo ' selected="selected"'; }
			echo '>'.$name.'</option>';
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<th>Note (Details)</th>
		<td><textarea name="note" cols="70" rows="15"><?php echo $form->getNote(); ?></textarea></td>
	</tr>
	<tr>
		<th>Task</th>
		<td>
<?php
if($form->getTask()) {
	$checked = ' checked="checked"'; // should the "checkbox" be "checked" ...
} else {
	$checked = '';                   // ... or not
}
?>
		<input type="checkbox" id="task_checkbox" name="task" value="1"<?php echo $checked; ?> />
		</td>
	</tr>
<!-- <div id="task_section" class="task_section"> -->
	<tr id="task_due_date_time" class="task_due_date_time">
		<th>Due Date Time</th>
		<td>
		<input type="text" id="datetimepicker" name="due_date_time" value="<?php echo $form->getDueDateTime(); ?>" size="15" maxlength="19" />
		</td>
	</tr>
	<tr id="task_priority" class="task_priority">
		<th>Priority</th>
		<td><?php echo $form->getPriority(); ?></td>
	</tr>
<!-- </div> -->
	<tr>
		<th>Last Edited</th>
		<td>
		<?php echo $form->getLastEdited(); ?>
		<input type="hidden" name="last_edited" value="<?php echo $form->getLastEdited(); ?>" />
		</td>
	</tr>
	<tr>
		<th>Last Accessed</th>
		<td>
		<?php echo $form->getLastAccessed(); ?>
		<input type="hidden" name="last_accessed" value="<?php echo $form->getLastAccessed(); ?>" />
		</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td><input type="submit" name="save_item" value=" Save Item " /></td>
	</tr>
</table>
</form>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>