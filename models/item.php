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

// constructor and getters for item
class Item {
	protected $item_id;
	protected $name;
	protected $category_id;
	protected $note;
	protected $task;
	protected $last_edited;
	protected $last_accessed;
	
	public function __construct() { }
	
	public function getItemID() {
		return $this->item_id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getCategoryID() {
		return $this->category_id;
	}
	
	public function getNote() {
		return $this->note;
	}
	
	public function getTask() {
		return $this->task;
	}
	
	public function getLastEdited() {
		return $this->last_edited;
	}
	
	public function getLastAccessed() {
		return $this->last_accessed;
	}
}

// constructor and getters for task
class ItemTask extends Item {
    protected $date_time;
    protected $priority;

    public function __construct() {
        parent::__construct();
        // write code here
    }

    public function getDueDateTime() {
        return $this->date_time;
    }

    public function getPriority() {
        return $this->priority;
    }
}

// override functions specific to form output
class ItemTaskForm extends ItemTask {
    public function __construct() {
        parent::__construct();
        // write code here
    }

    public function getName() {
        return htmlspecialchars($this->name);
    }

    public function getNote() {
        return htmlspecialchars($this->note);
        //return stripslashes($this->note); // before adding: "syntaxhighlighter"
    }

    public function getLastEdited() {
        if($this->last_edited == null) {
            return "-";
        }
        return $this->last_edited;
    }

    public function getLastAccessed() {
        if($this->last_accessed == null) {
            return "-";
        }
        return $this->last_accessed;
    }

    public function getDueDateTime() {
        if($this->date_time == 0) {
            return "";
        }
        return $this->date_time;
    }

    public function getPriority() {
        $output = '<select name="priority">';
        $selector = array(
            ""  => "Not Set",
            "5" => "Lowest",
            "4" => "Low",
            "3" => "Normal",
            "2" => "High",
            "1" => "Highest",
        );
        foreach( $selector as $value => $text ) {
            $output .= '<option value="'.$value.'"';
            if( $value == $this->priority ) { $output .= ' selected="selected"'; }
            $output .= '>'.$text.'</option>';
        }
        $output .= '</select>';
        return $output;
    }
}

// override functions specific to display (view) output
class ItemTaskDisplay extends ItemTask {
    protected $category_name;

    public function __construct() {
        parent::__construct();
        // write code here
    }

    public function getCategoryName() {
        return htmlspecialchars($this->category_name);
    }

    public function getNote() {
        return $this->note;
        //return stripslashes($this->note); // before adding: "syntaxhighlighter"
    }

    public function getTask() {
        if($this->task) {
            return '<span style="background-color: #FF0;">+</span>';
        } else {
            return '-';
        }
    }

    public function getDueDateTime($n = 9) {
        if($this->date_time == 0) {
            return "-";
        }
        $valueInt = strtotime($this->date_time); // convert string to timestamp (YYYY-MM-DD hh:mm)
        $current = time(); // current date (current timestamp)
        // recalculate $current for "Daylight Saving Time" (comment the lines below, if not active, do NOT delete)
        //$current += 3600; // 60 seconds * 60 minutes = 3600 seconds = 1 hour
        $day = 86400; // 60 seconds * 60 minutes * 24 hours = 86400 seconds = 1 day
        $upcoming = $current + ($day * $n); // upcoming date (add $n days to current timestamp)
        if( $valueInt < $current ) { // date is missed
            $bg_color = "#F00";
        } elseif ( $valueInt < $upcoming ) { // date will occur during the next $n days
            $bg_color = "#FF0";
        } else { // date will occur in more then $n days (a future date)
            return $this->date_time;
        }
        return '<span style="background-color: '.$bg_color.';">'.$this->date_time.'</span>';
    }

    public function getPriority() {
        if($this->priority < 1 || 5 < $this->priority) {
            return "-"; // priority is not set or unknown
        }
        $text = array(
            5 => "Lowest",
            4 => "Low",
            3 => "Normal",
            2 => "High",
            1 => "Highest",
        );
        $bg_color = array(
            5 => "#FFC", // lowest priority
            4 => "#FF9", // low priority
            3 => "#FF0", // normal priority
            2 => "#F60", // high priority
            1 => "#F00", // highest priority
        );
        return '<span style="background-color: '.$bg_color[$this->priority].';">'.$text[$this->priority].'</span>';
    }
}

class ModelItem extends DbSql {
    public function __construct() {
        parent::__construct();
    }
    
    public function select($id, $userId) {
            $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                            .' c.name AS category_name, t.date_time, t.priority'
                            .' FROM items i'
                            .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                            .' JOIN categories c ON i.category_id = c.category_id'
                            .' WHERE i.item_id = '.$id.' AND i.user_id = '.$userId.'';
            $row = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
            $item = $row->fetch();
            // UPDATE `last_accessed` after the data has been SELECTed
            //$query = 'UPDATE items SET last_accessed = NOW() WHERE item_id = '.$id.''; // PHP and MySQL have different summer time!!
            $query = 'UPDATE items SET last_accessed = "'.date('Y-m-d H:i:s', time()).'" WHERE item_id = '.$id.'';
            $this->db->query($query);
            return $item;
    }
    
    public function insert($valuesItems, $valuesTasks) {
            $st = $this->db->prepare('INSERT INTO items (name, category_id, note, task, last_edited, last_accessed, user_id)'
                                              .' VALUES (?, ?, ?, ?, ?, ?, ?)');
            $st->execute($valuesItems);

            $lastInsertItemId = $this->db->lastInsertId(); // get last inserted `item_id`

            if($valuesTasks != null) {
                    $valuesTasks[] = $lastInsertItemId;
                    $st = $this->db->prepare('INSERT INTO tasks (date_time, priority, item_id)'
                                            .' VALUES (?, ?, ?)');
                    $st->execute($valuesTasks);
            }
            return $lastInsertItemId;
    }
    
    public function update($itemId, $valuesItems, $valuesTasks) {
        $valuesItems[] = $itemId;
        $st = $this->db->prepare('UPDATE items SET name = ?, category_id = ?, note = ?, task = ?,'
                                .' last_edited = ?, last_accessed = ?, user_id = ?'
                                .' WHERE item_id = ?');
        $st->execute($valuesItems);
        //var_dump($valuesTasks);
        $query = 'SELECT item_id FROM tasks WHERE item_id = '.$itemId.'';
        $row = $this->db->query($query, PDO::FETCH_ASSOC);
        $result = $row->fetch();
        //var_dump($result);
        if($valuesTasks != null) {
            $valuesTasks[] = $itemId;
            if($result) {
                $st = $this->db->prepare('UPDATE tasks SET date_time = ?, priority = ?'
                                        .' WHERE item_id = ?');
            } else {
                $st = $this->db->prepare('INSERT INTO tasks (date_time, priority, item_id)'
                                        .' VALUES (?, ?, ?)');
            }
            $st->execute($valuesTasks);
        } else {
            if($result) { // if $valuesTasks == null, but the result from `tasks` is returned
                $st = $this->db->prepare('DELETE FROM tasks WHERE item_id = ?');
                $st->execute(array($itemId));
                //$st = $this->db->prepare('UPDATE tasks SET date_time = ?, priority = ?'
                //						.' WHERE item_id = ?');
                //$st->execute(array(null, null, $itemId));
            }
        }
    }
    
    public function delete($itemId, $userId) {
        $query = 'DELETE FROM items WHERE item_id = '.$itemId
                .' AND user_id = '.$userId.'';
        $this->db->exec($query);
        // delete row from `tasks` as well
        $query = 'SELECT item_id FROM tasks WHERE item_id = '.$itemId.'';
        $row = $this->db->query($query, PDO::FETCH_ASSOC);
        $result = $row->fetch();
        if($result) {
            $st = $this->db->prepare('DELETE FROM tasks WHERE item_id = ?');
            $st->execute(array($itemId));
        }
    }
    
    // get item for form
    public function getForm($id) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' WHERE i.item_id = '.$id.'';
        $row = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskForm');
        $form = $row->fetch();
        return $form;
    }
    
    public function getEmptyForm() {
        return new ItemTaskForm(); // create an empty ItemTaskForm object
    }
    
    public function listAll($userId) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                .' ORDER BY t.date_time DESC, t.priority';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function countRows($userId) {
        return $this->db->query('SELECT COUNT(*) FROM items WHERE user_id = '.$userId.'')->fetchColumn(0);
    }
    
    public function countRowsHasCategory($categoryId, $userId) {
        return $this->db->query('SELECT COUNT(*) FROM items'
                .' WHERE user_id = '.$userId
                .' AND category_id = '.$categoryId.'')->fetchColumn(0);
    }
    
    public function countRowsReview($userId) {
        $total = $this->db->query('SELECT COUNT(*) FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' WHERE user_id = '.$userId.''
                .' AND (' // 86400 seconds = 1 day; thus 9 days from the current moment
                .' t.date_time BETWEEN "2014-01-01 00:00" AND "'.date("Y-m-d H:i", time() + (86400 * 9)).'"'
                .' AND 1 <= t.priority AND t.priority <= 3'
                .' )')->fetchColumn(0);
        return $total;
    }
    
    public function countRowsDate($userId) {
        $total = $this->db->query('SELECT COUNT(*) FROM items i'
                .' INNER JOIN tasks t ON i.item_id = t.item_id'
                .' WHERE user_id = '.$userId.''
                .' AND t.date_time <> "0000-00-00 00:00:00"')->fetchColumn(0);
        return $total;
    }
    
    public function countRowsPriority($userId) {
        $total = $this->db->query('SELECT COUNT(*) FROM items i'
                .' INNER JOIN tasks t ON i.item_id = t.item_id'
                .' WHERE user_id = '.$userId.''
                .' AND t.priority <> ""')->fetchColumn(0);
        return $total;
    }
    
    public function listAllPaginated($perPage, $offset, $userId) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                //.' ORDER BY t.date_time DESC, t.priority'
                .' LIMIT '.$perPage.' OFFSET '.($offset - 1).'';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function listReviewPaginated($perPage, $offset, $userId) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                .' AND (' // 86400 seconds = 1 day; thus 9 days from the current moment
                .' t.date_time BETWEEN "2014-01-01 00:00" AND "'.date("Y-m-d H:i", time() + (86400 * 9)).'"'
                .' AND 1 <= t.priority AND t.priority <= 3'
                .' )'
                .' ORDER BY t.date_time DESC, t.priority'
                .' LIMIT '.$perPage.' OFFSET '.($offset - 1).'';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function sortByDatePaginated($perPage, $offset, $userId, $order) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' INNER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                .' AND t.date_time <> "0000-00-00 00:00:00"'
                .' ORDER BY t.date_time '.$order.', t.priority'
                .' LIMIT '.$perPage.' OFFSET '.($offset - 1).'';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function sortByPriorityPaginated($perPage, $offset, $userId, $order) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' INNER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                .' AND t.priority <> ""'
                .' ORDER BY t.priority '.$order.', t.date_time DESC'
                .' LIMIT '.$perPage.' OFFSET '.($offset - 1).'';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function filterByCategory($perPage, $offset, $categoryId, $userId) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = '.$userId.''
                .' AND i.category_id = '.$categoryId.''
                //.' ORDER BY t.date_time DESC, t.priority'
                .' LIMIT '.$perPage.' OFFSET '.($offset - 1).'';
        $rows = $this->db->query($query, PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $items = $rows->fetchAll();
        return $items;
    }
    
    public function search($SafeQuery, $userId) {
        $query = 'SELECT i.item_id, i.name, i.category_id, i.note, i.task, i.last_edited, i.last_accessed,'
                .' c.name AS category_name, t.date_time, t.priority'
                .' FROM items i'
                .' LEFT OUTER JOIN tasks t ON i.item_id = t.item_id'
                .' JOIN categories c ON i.category_id = c.category_id'
                .' WHERE i.user_id = ? AND'
                .' (i.name LIKE ?'
                .' OR i.note LIKE ?)'
                // alternative:
                //.' (i.name REGEXP ?'
                //.' OR i.note REGEXP ?)'
                .' ORDER BY t.date_time DESC, t.priority, i.item_id';
        $st = $this->db->prepare($query);
        $st->setFetchMode(PDO::FETCH_CLASS, 'ItemTaskDisplay');
        $st->execute(array($userId, "%".$SafeQuery."%", "%".$SafeQuery."%")); // alternative: $SafeQuery
        $items = $st->fetchAll();
        return $items;
    }
    
    public function getNotesAll($userId) {
        $query = 'SELECT note FROM items WHERE user_id = '.$userId.'';
        $rows = $this->db->query($query, PDO::FETCH_ASSOC);
        $rowsNotes = $rows->fetchAll();
        $notes = array();
        foreach ($rowsNotes as $rowNote) {
            $notes[] = $rowNote['note'];
        }
        return $notes;
    }
}