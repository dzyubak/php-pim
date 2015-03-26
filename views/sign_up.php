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
<title>Sign up</title>

<link rel="stylesheet" type="text/css" href="<?=BASE_URL?>views/styles/main.css" />

<style type="text/css" title="text/css">
    .errorMessage {
        display: none;
    }
    .showErrorMessage {
        display: inline;
        color: #F00;
        font-weight: bold;
        font-style: italic;
    }
</style>

<script language="JavaScript" type="text/javascript">
    
window.onload = function() {
    document.forms["sign_up_form"].onsubmit = validateForm;
};

function validateLettersOnly(inputElement) {
    if( inputElement.value === "" || !/^[a-zA-Z\u0400-\u04FF]+$/.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validateLettersNumbersOnly(inputElement) {
    if( !/^[a-zA-Z0-9]+$/.test(inputElement.value) ) {
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
    if( !/^\w+@\w+\.\w+$/.test(inputElement.value) ) {
        document.getElementById("errorMessage_" + inputElement.name).className = "showErrorMessage";
        inputElement.focus();
        inputElement.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement.name).className = "errorMessage";
    return true;
}

function validatePassword(inputElement1, inputElement2) {
    if(inputElement1.value === "" || inputElement1.value !== inputElement2.value) {
        document.getElementById("errorMessage_" + inputElement1.name).className = "showErrorMessage";
        inputElement1.focus();
        inputElement1.select();
        return false; // cancel form submission
    }
    // hide error message, if it has been set by the previous validation
    document.getElementById("errorMessage_" + inputElement1.name).className = "errorMessage";
    return true;
}

function validateForm() {
    if(!validateLettersNumbersOnly(document.forms["sign_up_form"].elements["username"])
        || !validatePassword(document.forms["sign_up_form"].elements["password"],
                             document.forms["sign_up_form"].elements["password_conf"])
        || !validateLettersOnly(document.forms["sign_up_form"].elements["last_name"])
        || !validateLettersOnly(document.forms["sign_up_form"].elements["first_name"])
        || !validateEmail(document.forms["sign_up_form"].elements["email"]))
    {
        return false; // cancel form submission
    }
}

</script>

</head>

<body>

<form id="sign_up_form" name="sign_up_form" action="<?=BASE_URL?>sign_up" method="post">
<h1>Sign up</h1>

<span style="color:red">required fields are marked with *</span>

<p><label for="uname">Username *</label> <span id="errorMessage_username" class="<?=$usernameValid ? 'errorMessage' : 'showErrorMessage';?>">Username must not be empty and can contain letters and numbers only.</span>
<input type="text" id="uname" name="username" style="width:215px;" maxlength="28" value="<?=$username?>" /></p>

<p><label for="pass1">Password *</label> <span id="errorMessage_password" class="<?=$passwordValid ? 'errorMessage' : 'showErrorMessage';?>">Password field must not be empty. Password and Confirmation password must match.</span>
<input type="password" id="pass1" name="password" style="width:215px;" maxlength="50" /></p>

<p><label for="pass2">Confirm password *</label>
<input type="password" id="pass2" name="password_conf" style="width:215px;" maxlength="50" /></p>

<p><label for="lname">Last name *</label> <span id="errorMessage_last_name" class="<?=$lastNameValid ? 'errorMessage' : 'showErrorMessage';?>">Last name must not be empty and can contain letters only.</span>
<input type="text" id="lname" name="last_name" style="width:215px;" maxlength="28" value="<?=$lastName?>" /></p>

<p><label for="fname">First name *</label> <span id="errorMessage_first_name" class="<?=$firstNameValid ? 'errorMessage' : 'showErrorMessage';?>">First name must not be empty and can contain letters only.</span>
<input type="text" id="fname" name="first_name" style="width:215px;" maxlength="28" value="<?=$firstName?>" /></p>

<p><label for="email">Email *</label> <span id="errorMessage_email" class="<?=$emailValid ? 'errorMessage' : 'showErrorMessage';?>">Email is empty or not valid.</span>
<input type="text" id="email" name="email" style="width:215px;" maxlength="28" value="<?=$email?>" /></p>
<!-- test email with gmail.com and gmail.ua which looks valid, but does not exist -->

<p><input id="sign_up_submit" name="sign_up_submit" type="submit" value=" Sign Up " /></p>

</form>

</body>
</html>