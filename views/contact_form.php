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
<title>Saving contact</title>

<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>views/styles/main.css" />

<style type="text/css" title="text/css">
    .errorMessage {
        display: none;
    }
    .showErrorMessage {
        display: block;
        text-align: center;
        color: #F00;
        font-weight: bold;
        font-style: italic;
    }
</style>

<script language="JavaScript" type="text/javascript">

window.onload = function() {
    var fileName = "<?=BASE_URL?>selectOptions.json";
    var countrySelected = <?=json_encode($form->getCountry())?>;
    var dialCode1Selected = <?=json_encode($form->getDialCode1())?>;
    var dialCode2Selected = <?=json_encode($form->getDialCode2())?>;
    
    selectCountry(fileName, 'country', countrySelected);
    selectDialCode(fileName, 'selectDialCode1', dialCode1Selected);
    selectDialCode(fileName, 'selectDialCode2', dialCode2Selected);
    document.forms["save_contact_form"].onsubmit = validateForm;
};

function selectCountry(fileName, id, countrySelected)
{
    var ajaxReq = new XMLHttpRequest();
    ajaxReq.open("GET", fileName, true);
    ajaxReq.setRequestHeader("Content-type", "application/json");
    ajaxReq.onreadystatechange = function()
    {
        if( ajaxReq.readyState === 4 && ajaxReq.status === 200 ) {
            var select = document.getElementById(id);
            select.length = 0;
            
            var response = JSON.parse(ajaxReq.responseText);
            var countries = response.countries;
            
            select.options[select.length] = new Option("none", "");
            for(var key in countries) {
                select.options[select.length] = new Option(countries[key], key);
            }
            countrySelected = countrySelected !== null ? countrySelected : '' ;
            select.value = countrySelected; // or: "select.selectedIndex = 3;"
            
            // variant 2, does not work in IE
            /*
            output = '<option value="">none</option>'; // default value
            for (var key in countries) {
                output += '<option value="'+key+'"';
                if(key === countrySelected) {
                    output += ' selected="selected"';
                }
                output += '>'+countries[key]+'</option>';
            }
            alert(output);
            document.getElementById(id).innerHTML = output;
            */
        } else if(ajaxReq.readyState === 4 && ajaxReq.status !== 200) {
            alert("Error! Response failed. Can not populate select list with data.");
        }
    };
    ajaxReq.send();
}

function selectDialCode(fileName, id, dialCodeSelected)
{
    var ajaxReq = new XMLHttpRequest();
    ajaxReq.open("GET", fileName, true);
    ajaxReq.setRequestHeader("Content-type", "application/json");
    ajaxReq.onreadystatechange = function()
    {
        if( ajaxReq.readyState === 4 && ajaxReq.status === 200 ) {
            var select = document.getElementById(id);
            select.length = 0;
            
            var response = JSON.parse(ajaxReq.responseText);
            var dialCodes = response.dialCodes;
            
            select.options[select.length] = new Option("none", "");
            for(var key in dialCodes) {
                select.options[select.length] = new Option(key, dialCodes[key]);
            }
            select.value = dialCodeSelected;
            
            // variant 2, does not work in IE
            /*
            output = '<option value="">none</option>'; // default value
            for (var key in dialCodes) {
                output += '<option value="'+dialCodes[key]+'"';
                if(dialCodes[key] === dialCodeSelected) {
                     output += ' selected="selected"';
                }
                output += '>'+key+'</option>';
            }
            document.getElementById(id).innerHTML = output;
            */
        } else if(ajaxReq.readyState === 4 && ajaxReq.status !== 200) {
            alert("Error! Response failed. Can not populate select list with data.");
        }
    };
    ajaxReq.send();
}

function validateLettersOnly(inputElement) {
    if( inputElement.value !== "" && !/^[a-zA-Z\u0400-\u04FF]+$/.test(inputElement.value) ) {
        
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateNumbersOnly(inputElement) {
    if( inputElement.value !== "" && !/^[0-9]+$/.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateSkype(inputElement) {
    if( inputElement.value !== "" && !/^[a-z][a-z0-9\.,\-_]{5,31}$/i.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateEmail(inputElement) {
    if( inputElement.value !== "" && !/^\w+@\w+\.\w+$/.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateURL(inputElement) {
    if( inputElement.value !== "" && !/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateForm() {
    if(!validateLettersOnly(document.forms["save_contact_form"].elements["family_name"])
        || !validateLettersOnly(document.forms["save_contact_form"].elements["given_name"])
        || !validateNumbersOnly(document.forms["save_contact_form"].elements["phone1"])
        || !validateNumbersOnly(document.forms["save_contact_form"].elements["phone2"])
        || !validateSkype(document.forms["save_contact_form"].elements["skype"])
        || !validateEmail(document.forms["save_contact_form"].elements["email"])
        || !validateURL(document.forms["save_contact_form"].elements["website"]))
    {
        return false; // cancel form submission
    }
}

</script>

</head>

<body>
<?php include_once 'includes/header.php'; ?>

<div id="entry_form">

<h1>Saving contact</h1>

<p class="center">
<?php if( $form->getContactID() != null ): ?>
<!-- ContactID: <?php //echo $form->getContactID(); ?> | -->
 Actions:
 <a href="<?=BASE_URL.'view_contact/'.$form->getContactID()?>">View</a>
 | Edit
 | <a href="<?=BASE_URL.'delete_contact/'.$form->getContactID()?>" onclick="return confirm('Click OK to confirm.\nNote: This can NOT be undone!');">Delete</a><br />
<?php endif ?>
</p>

<form action="<?=BASE_URL?>save_contact" method="post" name="save_contact_form" id="">
<input type="hidden" name="contact_id" value="<?=$form->getContactID()?>" />
<table>
    <tr>
        <th>Family Name</th>
        <td>
            <input type="text" name="family_name" value="<?=$form->getFamilyName()?>" size="45" maxlength="256" />
            <span id="errorMessage_family_name" class="errorMessage">Family Name can either be empty or contain letters only.</span>
        </td>
    </tr>
    <tr>
        <th>Given Name</th>
        <td>
            <input type="text" name="given_name" value="<?=$form->getGivenName()?>" size="45" maxlength="256" />
            <span id="errorMessage_given_name" class="errorMessage">Given Name can either be empty or contain letters only.</span>
        </td>
    </tr>
    <tr>
        <th>Phone1</th>
        <td>
            <select id="selectDialCode1" name="dial_code1"></select>
            <input type="text" name="phone1" value="<?=$form->getPhone1()?>" size="8" maxlength="10" />
            <span id="errorMessage_phone1" class="errorMessage">Phone can either be empty or contain digits only.</span>
        </td>
    </tr>
    <tr>
        <th>Phone2</th>
        <td>
            <select id="selectDialCode2" name="dial_code2"></select>
            <input type="text" name="phone2" value="<?=$form->getPhone2()?>" size="8" maxlength="10" />
            <span id="errorMessage_phone2" class="errorMessage">Phone can either be empty or contain digits only.</span>
        </td>
    </tr>
    <tr>
        <th>Country</th>
        <td><select id="country" name="country"></select></td>
    </tr>
    <tr>
        <th>Skype</th>
        <td>
            <input type="text" name="skype" value="<?=$form->getSkype()?>" size="45" maxlength="256" />
            <span id="errorMessage_skype" class="errorMessage">Skype is not valid.</span>
        </td>
    </tr>
    <tr>
        <th>Email</th>
        <td>
            <input type="text" name="email" value="<?=$form->getEmail()?>" size="45" maxlength="256" />
            <span id="errorMessage_email" class="errorMessage">Email is not valid.</span>
        </td>
    </tr>
    <tr>
        <th>Web site</th>
        <td>
            <input type="text" name="website" value="<?=$form->getWebsite()?>" size="80" maxlength="256" />
            <span id="errorMessage_website" class="errorMessage">Web site URL is not valid.</span>
        </td>
    </tr>
    <tr>
        <th>Notes</th>
        <td><textarea name="notes" cols="61" rows="15"><?=$form->getNotes()?></textarea></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="save_contact" value=" Save Contact " /></td>
    </tr>
</table>
</form>
</div>

<?php include_once 'includes/footer.php'; ?>
</body>
</html>