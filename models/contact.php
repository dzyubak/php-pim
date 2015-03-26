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

// constructor and getters for contact
class Contact {
    protected $contact_id;
    protected $family_name;
    protected $given_name;
    protected $dial_code1;
    protected $phone1;
    protected $dial_code2;
    protected $phone2;
    protected $country;
    protected $skype;
    protected $email;
    protected $website;
    protected $notes;
    
    public function __construct() { }
    
    public function getContactID() {
        return $this->contact_id;
    }

    public function getFamilyName() {
        return $this->family_name;
    }

    public function getGivenName() {
        return $this->given_name;
    }

    public function getDialCode1() {
        return $this->dial_code1;
    }

    public function getPhone1() {
        return $this->phone1;
    }

    public function getDialCode2() {
        return $this->dial_code2;
    }

    public function getPhone2() {
        return $this->phone2;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getSkype() {
        return $this->skype;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getWebsite() {
        return $this->website;
    }

    public function getNotes() {
        return $this->notes;
    }
}

// override functions specific to form output
class ContactForm extends Contact {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getDialCode1() {
        if ($this->dial_code1 != 0) {
            return $this->dial_code1;
        } else {
            return "";
        }
    }
    
    public function getPhone1() {
        if ($this->phone1 != 0) {
            return $this->phone1;
        } else {
            return "";
        }
    }
    
    public function getDialCode2() {
        if ($this->dial_code2 != 0) {
            return $this->dial_code2;
        } else {
            return "";
        }
    }
    
    public function getPhone2() {
        if ($this->phone2 != 0) {
            return $this->phone2;
        } else {
            return "";
        }
    }
    
    public function getNotes() {
        return htmlspecialchars($this->notes);
    }
}

// override functions specific to display (view) output
class ContactDisplay extends Contact {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getDialCode1() {
        if ($this->dial_code1 != 0) {
            return $this->dial_code1;
        } else {
            return "";
        }
    }
    
    public function getPhone1() {
        if ($this->phone1 != 0) {
            return $this->phone1;
        } else {
            return "-";
        }
    }
    
    public function getDialCode2() {
        if ($this->dial_code2 != 0) {
            return $this->dial_code2;
        } else {
            return "";
        }
    }
    
    public function getPhone2() {
        if ($this->phone2 != 0) {
            return $this->phone2;
        } else {
            return "-";
        }
    }
    
    public function getNotes() {
        return htmlspecialchars($this->notes);
    }
}

class ModelContact extends DbSql {
    public function __construct() {
        parent::__construct();
    }
    
    public function select($id, $userId) {
        $query = 'SELECT * FROM contacts'
                .' WHERE contact_id = '.$id
                .' AND user_id = '.$userId.'';
        $row = $this->db->query($query, PDO::FETCH_CLASS, 'ContactDisplay');
        $contact = $row->fetch();
        return $contact;
    }
    
    public function insert($valuesContacts) {
        $st = $this->db->prepare('INSERT INTO contacts (family_name, given_name, dial_code1, phone1, dial_code2, phone2, country, skype, email, website, notes, user_id)'
                                .' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $st->execute($valuesContacts);
        $lastInsertContactId = $this->db->lastInsertId(); // get last inserted `contact_id`
        return $lastInsertContactId;
    }
    
    public function update($contactId, $valuesContacts) {
        $valuesContacts[] = $contactId;
        $st = $this->db->prepare('UPDATE contacts'
                .' SET family_name = ?, given_name = ?, dial_code1 = ?, phone1 = ?, dial_code2 = ?, phone2 = ?, country = ?, skype = ?, email = ?, website = ?, notes = ?'
                .' WHERE user_id = ? AND contact_id = ?');
        $st->execute($valuesContacts);
        //var_dump($valuesContacts);
    }
    
    public function delete($id, $userId) {
        $query = 'DELETE FROM contacts WHERE contact_id = '.$id
                .' AND user_id = '.$userId.'';
        $this->db->exec($query);
    }
    
    // get contact for form
    public function getForm($id) {
            $query = 'SELECT * FROM contacts'
                .' WHERE contact_id = '.$id.'';
            $row = $this->db->query($query, PDO::FETCH_CLASS, 'ContactForm');
            $form = $row->fetch();
            return $form;
    }
    
    public function getEmptyForm() {
            return new ContactForm(); // create an empty ContactForm object
    }
    
    public function listAll($userId) {
        $query = 'SELECT * FROM contacts'
                .' WHERE user_id = '.$userId
                .' ORDER BY family_name, given_name';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ContactDisplay');
        $contacts = $rows->fetchAll();
        return $contacts;
    }
}