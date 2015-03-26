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
class Category {
    protected $category_id;
    protected $name;
    protected $description;
    
    public function __construct() { }
    
    public function getCategoryID() {
        return $this->category_id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
}

// override functions specific to form output
class CategoryForm extends Category {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getName() {
        return htmlspecialchars($this->name);
    }
	
    public function getDescription() {
        return htmlspecialchars($this->description);
    }
}

// override functions specific to display (view) output
class CategoryDisplay extends Category {
    public function __construct() {
        parent::__construct();
        // write code here
    }
    
    public function getName() {
        return htmlspecialchars($this->name);
    }
    
    public function getDescription() {
        return htmlspecialchars($this->description);
    }
}

class ModelCategory extends DbSql {
    public function __construct() {
        parent::__construct();
    }
    
    public function select($id, $userId) {
        $query = 'SELECT * FROM categories'
                .' WHERE category_id = '.$id
                .' AND user_id = '.$userId.'';
        $row = $this->db->query($query, PDO::FETCH_CLASS, 'CategoryDisplay');
        $category = $row->fetch();
        return $category;
    }
    
    public function insert($valuesCategories) {
        $st = $this->db->prepare('INSERT INTO categories (name, description, user_id)'
                                .' VALUES (?, ?, ?)');
        $st->execute($valuesCategories);
        $lastInsertCategoryId = $this->db->lastInsertId(); // get last inserted `category_id`
        return $lastInsertCategoryId;
    }
    
    public function update($categoryId, $valuesCategories) {
        $valuesCategories[] = $categoryId;
        $st = $this->db->prepare('UPDATE categories'
                .' SET name = ?, description = ?, user_id = ?'
                .' WHERE category_id = ?');
        $st->execute($valuesCategories);
        //var_dump($valuesCategories);
    }
    
    public function delete($categoryId, $userId) {
        //check, if current category does not have any items
        $query = 'SELECT item_id FROM items'
                .' WHERE category_id = '.$categoryId.''
                .' AND user_id = '.$userId.'';
        $row = $this->db->query($query, PDO::FETCH_ASSOC);
        $result = $row->fetch();
        if(!$result) {
            $query = 'DELETE FROM categories WHERE category_id = '.$categoryId
                    .' AND user_id = '.$userId.'';
            $this->db->exec($query);
            return 0;
        } else { // can not delete category while it has at least one item
            return 1;
        }
    }
    
    // get category for form
    public function getForm($id) {
            $query = 'SELECT * FROM categories'
                .' WHERE category_id = '.$id.'';
            $row = $this->db->query($query, PDO::FETCH_CLASS, 'CategoryForm');
            $form = $row->fetch();
            return $form;
    }
    
    public function getEmptyForm() {
            return new CategoryForm(); // create an empty CategoryForm object
    }
    
    public function listAll($userId) {
        $query = 'SELECT * FROM categories'
                .' WHERE user_id = '.$userId
                .' ORDER BY name';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'CategoryDisplay');
        $categories = $rows->fetchAll();
        return $categories;
    }
    
    public function getAllIdName($userId) {
        //begin:
        $query = 'SELECT category_id, name FROM categories WHERE user_id = '.$userId.'';
        $rows = $this->db->query($query, PDO::FETCH_ASSOC);
        $results = $rows->fetchAll();
        if(empty($results)) { // if there are no categories - initialize with the "!default" one
            $valuesCategories[] = "!default";
            $valuesCategories[] = "Default category is created if there are no categories available.";
            $valuesCategories[] = $userId;
            $st = $this->db->prepare('INSERT INTO categories (name, description, user_id)'
                                    .' VALUES (?, ?, ?)');
            $st->execute($valuesCategories);
            //goto begin;
            $query = 'SELECT category_id, name FROM categories WHERE user_id = '.$userId.'';
            $rows = $this->db->query($query, PDO::FETCH_ASSOC);
            $results = $rows->fetchAll();
        }
        foreach ($results as $result) {
            $categoriesArr[$result['category_id']] = $result['name']; // create an assoc array out of category "category_id" and "name"
        }
        natcasesort($categoriesArr);
        return $categoriesArr; // case-insensitive natural sorting
    }
}