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

include_once 'models/db_sql.php';
$db = new DbSql();
$dbsql = $db->getConnection();

try {
    
    $dbsql->beginTransaction();
    
    /*
     * CREATE TABLEs
     */
    
    // CREATE TABLE users
    $dbsql->exec(<<<_SQL_
CREATE TABLE users (
 user_id INT NOT NULL PRIMARY KEY, -- user's session ID
 username VARCHAR(30),
 password BINARY(40) NOT NULL,
 email VARCHAR(90) NOT NULL,
 last_name VARCHAR(30) NOT NULL,
 first_name VARCHAR(30) NOT NULL,
 state BOOLEAN DEFAULT NULL
 ) ENGINE MyISAM
 DEFAULT CHARACTER SET 'utf8';  
_SQL_
    );
    
    // CREATE TABLE categories
    $dbsql->exec(<<<_SQL_
CREATE TABLE categories (
 category_id INT NOT NULL AUTO_INCREMENT,
 name VARCHAR(31) NOT NULL,
 description VARCHAR(255),
 user_id INT NOT NULL, -- (access_control)
 PRIMARY KEY (category_id)
 ) ENGINE MyISAM
 DEFAULT CHARACTER SET 'utf8';
_SQL_
    );
    
    // CREATE TABLE items
    // Note: 21844 is a max for utf8
    $dbsql->exec(<<<_SQL_
CREATE TABLE items (
 item_id INT NOT NULL AUTO_INCREMENT,
 name VARCHAR(255) NOT NULL, -- record name / title
 category_id INT NOT NULL,
 note VARCHAR(20777) NOT NULL,
 task BOOLEAN DEFAULT NULL,
 last_edited TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 last_accessed TIMESTAMP NOT NULL,
 user_id INT NOT NULL, -- (access_control)
 PRIMARY KEY (item_id),
 FOREIGN KEY (category_id) REFERENCES categories(category_id)
 ) ENGINE MyISAM
 DEFAULT CHARACTER SET 'utf8';
_SQL_
    );
    
    // CREATE TABLE tasks
    $dbsql->exec(<<<_SQL_
CREATE TABLE tasks (
 item_id INT NOT NULL,
 date_time DATETIME,
 priority TINYINT(1),
 PRIMARY KEY (item_id),
 FOREIGN KEY (item_id) REFERENCES items(item_id)
 ) ENGINE MyISAM
 DEFAULT CHARACTER SET 'utf8';
_SQL_
    );
    
    // CREATE TABLE contacts
    $dbsql->exec(<<<_SQL_
CREATE TABLE contacts (
 contact_id INT NOT NULL AUTO_INCREMENT,
 family_name VARCHAR(25),
 given_name VARCHAR(25),
 dial_code1 INT,
 phone1 INT,
 dial_code2 INT,
 phone2 INT,
 country VARCHAR(50),
 skype VARCHAR(50),
 email VARCHAR(50),
 website VARCHAR(70),
 notes VARCHAR(255),
 user_id INT NOT NULL,
 PRIMARY KEY (contact_id)
 ) ENGINE MyISAM
 DEFAULT CHARACTER SET 'utf8';
_SQL_
    );
    
    /*
     * create administrator and users
     */
    
    // INSERT INTO users
    $adminUsername = "admin";
    $adminPassword = sha1("admin");
    $adminId       = 1;
    $user1Username = "user1";
    $user1Password = sha1("user1");
    $user1Id       = 1427334155;
    $user1LName    = "Wilson";  // last name
    $user1FName    = "Richard"; // first name
    $user2Username = "user2";
    $user2Password = sha1("user2");
    $user2Id       = 1427334177;
    $user2LName    = "Williams"; // last name
    $user2FName    = "Daniel";   // first name
    $user3Username = "unactivated";
    $user3Password = sha1("unactivated");
    $user3Id       = 1427334201;
    
    $sql1 = "INSERT INTO users (user_id, username, password, email, last_name, first_name, state)" .
            " VALUES ($adminId, '$adminUsername', '$adminPassword', '$adminUsername@example.com', '$adminUsername', '$adminUsername', true),"; // create admin
    $sql1 .= "($user1Id, '$user1Username', '$user1Password', '$user1Username@example.com', '$user1LName', '$user1FName', true),"; // create user1
    $sql1 .= "($user2Id, '$user2Username', '$user2Password', '$user2Username@example.com', '$user2LName', '$user2FName', true),"; // create user2
    $sql1 .= "($user3Id, '$user3Username', '$user3Password', '$user3Username@example.com', 'Not', 'Activated User', false);"; // create unactivated (registered, but not activated user)
    
    $dbsql->exec($sql1);
    
    /*
     * generate data for user1
     */
    
    // INSERT INTO categories
    $dbsql->exec(<<<_SQL_
INSERT INTO categories (category_id, name, description, user_id)
VALUES
 (1, 'business', 'business category description', $user1Id),
 (2, 'personal', 'personal category description', $user1Id),
 (3, 'education', 'education category description', $user1Id),
 (4, 'weekend', 'weekend category description', $user1Id),
 (5, 'finances', 'finances category description', $user1Id);
_SQL_
    );
    
    // INSERT INTO items
    // INSERT INTO tasks
    $dateTime = time(); // current Unix timestamp
    
    $sql2 = "INSERT INTO items" .
            "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
            " VALUES " .
            "(1, 'visit Zend Framework webinar', 3," .
            " '<p>\"Visit Zend Framework webinar\" is a task, so we should check a task checkbox.</p>" .
            "<p><a href=\"https://zend.webex.com/zend/\">https://zend.webex.com/zend/</a></p>" .
            "<p>&nbsp;</p>'," .
            " true, '" .
            date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (23 * 60 * 60))) .
            "', '" .
            date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (23 * 60 * 60))) . "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(1, '" .
             date('Y-m-d H:i:s', $dateTime - (2 * 60 * 60)) .
             "', 3);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(2, 'PHP Website', 3," .
             " '<p><a href=\"http://php.net/\">http://php.net/</a></p>" .
             "<p>This is a reference information, a bookmark, thus we do not check a task checkbox.<br />" .
             "\"Due Date Time\" and \"Priority\" is not added to \"tasks\" table and is not output to screen.</p>'," .
             " false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (21 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (21 * 60 * 60))) .
             "', $user1Id);";
    //
    $sql2 .= "INSERT INTO items".
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(3, 'Dell Inspiron Laptop', 2, '<p>Reference - (block) Quote:</p>" .
             "<blockquote>" .
             "<p>Dell Inspiron 15.6 Inch Laptop with Intel Dual Core Processor 2.16 GHz,4 GB DDR3, 500 GB Hard Drive, Windows 8.1 (Certified Refurbished)</p>" .
             "<p>Processor Intel Celeron Dual Core N2830 Processor Operating System [81N6EB] Win8.1 w Bing 64-Bit Eng Memory 4GB Single Channel DDR3 1600MHz (4GBx1) Video Graphics Intel HD Graphics Hard Drive 500GB 5400 rpm SATA Hard Drive Multimedia Drive No Optical Drive Media Card Reader 8-in-1 Media Card Reader and USB 2.0 Biometrics FastAccess Facial Recognition.<br />" .
             "B00THEP15O</p>" .
             "</blockquote><p>&nbsp;</p>'," .
             " false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (22 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (22 * 60 * 60))) .
             "', $user1Id);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(4, 'listen to Elvis Presley', 4, ''," .
             " true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(4, '" .
             date('Y-m-d H:i:s', $dateTime + ((2 * 24 * 60 * 60) + (30 * 60))) .
             "', 1);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(5, 'task item 1', 1, '', true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(5, '" .
             date('Y-m-d H:i:s', $dateTime + ((14 * 24 * 60 * 60) + (30 * 60))) .
             "', 3);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(6, 'code snippet', 1," .
             " '<p>code snippet:</p>" .
             "<pre class=\"brush: php; fontsize: 100; first-line: 1; \">&lt;?php" .
             "  echo \"Hello World!\";" .
             "?&gt;</pre>" .
             "<p>...</p>'," .
             "false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(7, 'task item 2', 1, '', true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(7, '" .
             date('Y-m-d H:i:s', $dateTime + ((7 * 24 * 60 * 60) + (4 * 60 * 60))) .
             "', 2);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(8, 'task item 3', 1, '', true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(8, '" .
             date('Y-m-d H:i:s', $dateTime + ((7 * 24 * 60 * 60) + (4 * 60 * 60))) .
             "', 4);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(9, 'task item 5', 1, '', true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    $sql2 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(9, '', 5);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(13, 'list of bookmarks', 1," .
             "'<p>&nbsp;</p>" .
             "<ul>" .
             "<li><a href=\"http://google.com/\">http://google.com/</a></li>" .
             "<li><a href=\"http://www.microsoft.com/en-us/default.aspx\">http://www.microsoft.com/en-us/default.aspx</a></li>" .
             "<li><a href=\"http://example.com/\">http://example.com/</a></li>" .
             "</ul>" .
             "<p>&nbsp;</p>'," .
             "false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
    //
    $sql2 .= "INSERT INTO items" .
             "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
             " VALUES " .
             "(14, 'Unicode / special characters test', 1," .
             "'<p>a-b_c\'d\"e/f\\\\g&lt;h&gt;i&amp;j?k<br />" .
             "ˌɪntəˌn&aelig;ʃ(ə)n(ə)laɪ''zeɪʃ(ə)n<br />abcдїk&rsquo;lmn<br />" .
             "&auml; abc&ouml;def z&uuml;</p>'," .
             "false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user1Id);";
	
    $dbsql->exec($sql2);
    
    // INSERT INTO contacts
    $dbsql->exec(<<<_SQL_
INSERT INTO contacts (contact_id, family_name, given_name, dial_code1, phone1, dial_code2, phone2, country, skype, email, website, notes, user_id)
    VALUES (1, 'Davis', 'Patricia', 1, 1234567890, 44, 2071234567, 'us', 'skype1', 'someone@example.com', 'http://example.com/', 'notes, additional information about contact', $user1Id);
INSERT INTO contacts (contact_id, family_name, given_name, user_id)
VALUES
 (2, 'White', 'Oliver', $user1Id),
 (4, 'Turner', 'Matthew', $user1Id),
 (7, 'Wright', 'Olivia', $user1Id),
 (8, 'Smith', 'Emily', $user1Id),
 (9, 'Mitchell', 'Jessica', $user1Id);
_SQL_
    );
    
    /*
     * generate data for user2
     */
    
    // INSERT INTO categories
    $dbsql->exec(<<<_SQL_
INSERT INTO categories (category_id, name, description, user_id)
VALUES
 (6, 'category1', 'category1 description', $user2Id),
 (7, 'category2', 'category2 description', $user2Id),
 (8, 'category3', 'category3 description', $user2Id);
_SQL_
    );
    
    // INSERT INTO items
    // INSERT INTO tasks
    $sql3 = "INSERT INTO items" .
            "(item_id, name, category_id, note, task, last_edited, last_accessed, user_id)" .
            " VALUES " .
            "(10, 'User2Item1', 6, '', false, '" .
            date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
            "', '" .
            date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
            "', $user2Id),";
    $sql3 .= "(11, 'User2Item2', 8, '', false, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user2Id),";
    $sql3 .= "(12, 'User2Task1', 6, '', true, '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', '" .
             date('Y-m-d H:i:s', $dateTime - ((24 * 60 * 60) + (20 * 60 * 60))) .
             "', $user2Id);";
    $sql3 .= "INSERT INTO tasks (item_id, date_time, priority)" .
             " VALUES " .
             "(12, '" .
             date('Y-m-d H:i:s', $dateTime + ((2 * 24 * 60 * 60) + (6 * 60 * 60))) .
             "', 3);";
    
    $dbsql->exec($sql3);
    
    // INSERT INTO contacts
    $dbsql->exec(<<<_SQL_
INSERT INTO contacts (contact_id, family_name, given_name, user_id)
VALUES
 (3, 'FamilyName1', 'GivenName1', $user2Id),
 (5, 'FamilyName2', 'GivenName2', $user2Id),
 (6, 'FamilyName3', 'GivenName3', $user2Id);
_SQL_
    );
    
    echo "Installation completed successfully! Do not forget to delete install.php file.";
    
} catch (PDOException $ex) {
    
    $dbsql->rollback();
    echo "Error! ".$ex->getMessage()."<br />";
    exit();
    
} catch (Exception $ex) {
    
    $dbsql->rollback();
    echo "Error! ".$ex->getMessage()."<br />";
    exit();
    
}