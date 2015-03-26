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
<title>User Administration</title>

<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>views/styles/main.css" />
<script src="<?=BASE_URL?>views/scripts/jquery-1.9.1.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">

window.onload = function() {
    init();
};

function init()
{
	document.getElementById('search_box').focus();
	document.getElementById('search_box').select();
}

function updateRow(button) {
    if (button === "") { return; }
    var tr = button.parentNode.parentNode;
    $.ajax({
        url: "<?=BASE_URL?>ajax_gateway.php",
        type: 'POST',
        data: "user_id="  + tr.cells[0].innerHTML
            + "&username="   + tr.cells[1].childNodes[0].value
            + "&email="      + tr.cells[2].childNodes[0].value
            + "&last_name="  + tr.cells[3].childNodes[0].value
            + "&first_name=" + tr.cells[4].childNodes[0].value
            + "&state="      + tr.cells[5].childNodes[0].checked,
        dataType: 'html',
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        success: function (data, textStatus, jqXHR) {
                        $(tr).replaceWith(data);
                    },
        error: function (jqXHR, textStatus, errorThrown) {
                        alert("Error! AJAX request error. Please, try again later.");
                    }
    });
    
    /*
    if (window.XMLHttpRequest) { // IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {                     // IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            //alert(xmlhttp.responseText);
            //tr.innerHTML = xmlhttp.responseText; // does not work in IE
            //document.getElementById("output").innerHTML = xmlhttp.responseText;
            
            $(tr).replaceWith(xmlhttp.responseText);
        }
    };
    xmlhttp.open("POST", "<?=BASE_URL?>admin_ajax.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //xmlhttp.send("tableRow="+encodeURI(str.cells[0].firstChild.nodeValue));
    //xmlhttp.send("tableRow="+encodeURI(tr.innerHtml));
    //onclick="updateRow(this.parentNode.parentNode)"
    //xmlhttp.send("tableRow="+tr.cells[0].innerHTML+"&tableRow2="+tr.cells[3].innerHTML); // encodeURI()
    
    var tr = button.parentNode.parentNode;
    
    xmlhttp.send("user_id="  + tr.cells[0].innerHTML
            + "&username="   + tr.cells[1].childNodes[0].value
            + "&email="      + tr.cells[2].childNodes[0].value
            + "&last_name="  + tr.cells[3].childNodes[0].value
            + "&first_name=" + tr.cells[4].childNodes[0].value
            + "&state="      + tr.cells[5].childNodes[0].checked);*/
}

</script>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="list">

<h1>User Administration</h1>

<table>
<tr>
    <th>User ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Last Name</th>
    <th>First Name</th>
    <th>State</th>
    <th>Actions</th>
</tr>
<tr>
<?php $i = 0; ?>
<?php foreach( $forms as $form ): ?>
<?php
if( $i % 2 ) {
    echo '<tr>';
    //echo '<tr id="'.$form->getUserID().'">';
} else {
    echo '<tr class="odd">';
    //echo '<tr class="odd" id="'.$form->getUserID().'">';
}
?>
    <td class="center"><?=$form->getUserID()?></td>
    <td class="center"><input type="text" name="username" value="<?=$form->getUsername()?>" size="8" /></td>
    <td class="center"><input type="text" name="email" value="<?=$form->getEmail()?>" size="15" /></td>
    <td class="center"><input type="text" name="last_name" value="<?=$form->getLastName()?>" size="11" /></td>
    <td class="center"><input type="text" name="first_name" value="<?=$form->getFirstName()?>" size="11" /></td>
    <td class="center"><input type="checkbox" name="state" value="state"<?=$form->getState() ? ' checked="checked"' : ''?> /></td>
    <td class="center"><button onclick="updateRow(this)">update</button></td>
</tr>
<?php $i++; ?>
<?php endforeach ?>

</table>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>