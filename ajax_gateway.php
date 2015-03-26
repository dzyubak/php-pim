<?php
/**
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
 */

$values[] = filter_input(INPUT_POST, 'username');
$values[] = filter_input(INPUT_POST, 'email');
$values[] = filter_input(INPUT_POST, 'last_name');
$values[] = filter_input(INPUT_POST, 'first_name');
$values[] = (filter_input(INPUT_POST, 'state') == "true") ? TRUE : FALSE;
$values[] = (int) filter_input(INPUT_POST, 'user_id');

include_once 'models/user.php';
$mUser = new ModelUser();
$mUser->update($values);
$form = $mUser->getForm(filter_input(INPUT_POST, 'user_id'));

$state = $form->getState() ? ' checked="checked"' : '';

echo <<< EOT
<tr>
<td class="center">{$form->getUserID()}</td>
<td class="center"><input type="text" name="username" value="{$form->getUsername()}" size="8" /></td>
<td class="center"><input type="text" name="email" value="{$form->getEmail()}" size="15" /></td>
<td class="center"><input type="text" name="last_name" value="{$form->getLastName()}" size="11" /></td>
<td class="center"><input type="text" name="first_name" value="{$form->getFirstName()}" size="11" /></td>
<td class="center"><input type="checkbox" name="state" value="state"$state /></td>
<td class="center"><!--<button onclick="updateRow(this)">update</button><br />record updated successfully-->updated</td>
</tr>
EOT;
