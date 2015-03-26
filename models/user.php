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

include_once 'db_sql.php';

// constructor and getters for category
class User {
    protected $user_id;
    protected $username;
    protected $email;
    protected $last_name;
    protected $first_name;
    protected $state;
    
    public function __construct() { }
    
    public function getUserID() {
        return $this->user_id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getState() {
        return $this->state;
    }
}

// override functions specific to form output
class UserForm extends User {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getLastName() {
        return htmlspecialchars($this->last_name);
    }

    public function getFirstName() {
        return htmlspecialchars($this->first_name);
    }
}

// override functions specific to display (view) output
class UserDisplay extends User {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getLastName() {
        return htmlspecialchars($this->last_name);
    }

    public function getFirstName() {
        return htmlspecialchars($this->first_name);
    }
}

class ModelUser extends DbSql {
    public function __construct() {
        parent::__construct();
    }
    
    public function signUp($values) {
        $statement = 'INSERT INTO users (user_id, username, password, email, last_name, first_name)'
                                .' VALUES (?, ?, ?, ?, ?, ?)';
        $st = $this->db->prepare($statement);
        $st->execute($values);
    }
    
    public function authorize($username, $password) {
        //include_once 'models/user.php';
        $query = 'SELECT * FROM users WHERE username = "'.$username.'" AND password = "'.sha1($password).'"';
        $row = $this->db->query($query, PDO::FETCH_ASSOC);
        $user = $row->fetch();
        if($user) {
            if($user['state']) {
                $_SESSION['user_id']    = $user['user_id'];
                $_SESSION['last_name']  = $user['last_name'];
                $_SESSION['first_name'] = $user['first_name'];
                header("Location: ".BASE_URL.'review');
            } else {
                $_SESSION['error'] = "Your account is not activated.<br />Please, contact support.";
                header("Location: ".BASE_URL.'sign_in');
            }
            
        } else {
            $_SESSION['error'] = "Username and Password<br />do not match!";
            header("Location: ".BASE_URL.'sign_in');
        }
    }
    
    public function listAllForm() {
        $query = 'SELECT * FROM users -- ORDER BY username';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'UserForm');
        $forms = $rows->fetchAll();
        return $forms;
    }
	
	public function update($values) {
		$st = $this->db->prepare('UPDATE users'
						  .' SET username = ?, email = ?, last_name = ?,'
						  .' first_name = ?, state = ?'
						  .' WHERE user_id = ?');
		$st->execute($values);
	}
	
	public function getForm($userId) {
		$query = 'SELECT * FROM users WHERE user_id = '.$userId.'';
		$row = $this->db->query($query, PDO::FETCH_CLASS, 'UserForm');
		$form = $row->fetch();
		return $form;
	}
}