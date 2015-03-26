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

class Controller
{
    public static function execute($action, $key)
    {
        /**Development environment: http://php-pim.dzyubak.com/
         * Production environment: http://php-pim.dzyubak.com/
         */
        define('BASE_URL', 'http://php-pim.dzyubak.com/');
        // Development OR Production environment
        define('DEVELOPMENT', TRUE); // TRUE OR FALSE
        if(DEVELOPMENT == TRUE) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }
        // Authorization and Security
        session_start();
        if(!isset($_SESSION['last_name']) && !isset($_SESSION['first_name']) && $action != "sign_in" && $action != "authorize" && $action != "sign_up" && $action != "sign_up_done") {
            header("Location: ".BASE_URL.'sign_in'); // $action = "sign_in";
        }
        switch($action)
        {
            /* sing up/in/out and authorization */
            case "sign_up":
                $username = filter_input(INPUT_POST, 'username');
                $password = filter_input(INPUT_POST, 'password');
                $passwordConf = filter_input(INPUT_POST, 'password_conf');
                $lastName = filter_input(INPUT_POST, 'last_name');
                $firstName = filter_input(INPUT_POST, 'first_name');
                $email = filter_input(INPUT_POST, 'email');
                $usernameValid = $passwordValid = $lastNameValid
                        = $firstNameValid = $emailValid = TRUE;
                include_once 'controllers/sign_up.php';
                if($username && $password) {
                    $usernameValid = Validator::validateLettersNumbersOnly($username);
                    $passwordValid = Validator::validatePassword($password, $passwordConf);
                    $lastNameValid = Validator::validateLettersOnly($lastName);
                    $firstNameValid = Validator::validateLettersOnly($firstName);
                    $emailValid = Validator::validateEmail($email);
                    if($usernameValid && $passwordValid && $lastNameValid && $firstNameValid && $emailValid) {
                        $values[] = time();
                        $values[] = $username;
                        $values[] = sha1($password); // $sha1Str = sha1($str);
                        $values[] = $email;
                        $values[] = $lastName;
                        $values[] = $firstName;
                        include_once 'models/user.php';
                        $mUser = new ModelUser();
                        $mUser->signUp($values);
                        header("Location: ".BASE_URL."sign_up_done");
                    }
                }
                include_once 'views/sign_up.php';
                break;
            case "sign_up_done":
                echo "Sign up completed successfully! You are registered to Personal Information Manager service now.<br />"
                    ." Please, wait while your profile would be verified and your account activated."
                    ." You will be contacted shortly.";
                break;
            case "sign_in":
                include_once 'views/sign_in.php';
                break;
            case "sign_out":
                session_destroy(); // ends a session and clears its data
                header("Location: ".BASE_URL);
                break;
            case "authorize":
                include_once 'models/user.php';
                $mUser = new ModelUser();
                $mUser->authorize(filter_input(INPUT_POST, 'username'), filter_input(INPUT_POST, 'password'));
                break;
            /* administration */
            case "admin":
                if($_SESSION['user_id'] == 1) {
                    include_once 'models/user.php';
                    $mUser = new ModelUser();
                    $forms = $mUser->listAllForm();
                    include_once 'views/admin.php';
                } else {
                    header("Location: ".BASE_URL."review");
                }
                break;
            /* items */
            case "review":
                include_once 'controllers/paginator.php';
                $offset = isset($key) && $key > 0 ? intval($key) : 1;
                $perPage = 5;
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $total = $mItem->countRowsReview($_SESSION['user_id']);
                $items = $mItem->listReviewPaginated($perPage, $offset, $_SESSION['user_id']);
                include_once 'views/review.php';
                break;
            case "save_item":
                if(isset($_POST['save_item'])) { // if "save" submit button has been clicked
                    //var_dump($_POST);
                    $valuesItems[] = filter_input(INPUT_POST, 'name');
                    $valuesItems[] = filter_input(INPUT_POST, 'category_id');
                    $valuesItems[] = filter_input(INPUT_POST, 'note');
                    $valuesItems[] = isset($_POST['task']) ? true : false;
                    $valuesItems[] = date('Y-m-d H:i:s', time());
                    $valuesItems[] = date('Y-m-d H:i:s', time());
                    $valuesItems[] = $_SESSION['user_id'];
                    if(isset($_POST['task'])) { // if task checkbox is ticked (selected)
                        $valuesTasks[] = filter_input(INPUT_POST, 'due_date_time');
                        $valuesTasks[] = filter_input(INPUT_POST, 'priority');
                    } else {
                        $valuesTasks = null;
                    }
                    if(isset($_POST['item_id']) && filter_input(INPUT_POST, 'item_id') != "") {
                        include_once 'models/item.php';
                        $mItem = new ModelItem();
                        $mItem->update(filter_input(INPUT_POST, 'item_id'), $valuesItems, $valuesTasks);
                    } else {
                        include_once 'models/item.php';
                        $mItem = new ModelItem();
                        $lastInsertItemId = $mItem->insert($valuesItems, $valuesTasks);
                    }
                } else {
                    throw new Exception('ERROR! Something went wrong while saving the data.'
                                       .' Please, contact the developer regarding this problem.');
                }
                // NO "break;"
            case "edit_item":
                if( isset($key) && $key != "" ) { // set $id from $_GET ...
                    $itemID = $key;
                } elseif( isset($_POST['item_id']) && filter_input(INPUT_POST, 'item_id') != "" ) { // ... or $_POST
                    $itemID = filter_input(INPUT_POST, 'item_id');
                } elseif( isset($lastInsertItemId) ) {
                    $itemID = $lastInsertItemId;
                } else {
                    throw new Exception('ERROR! ItemID can not be set.');
                }
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $form = $mItem->getForm($itemID); // create an empty ItemTaskForm object
                if ($form) {
                    include_once 'models/category.php';
                    $mCategory = new ModelCategory();
                    $categoriesArr = $mCategory->getAllIdName($_SESSION['user_id']);
                    include_once 'views/item_form.php';
                } else {
                    $title = 'ERROR! No item with such ID.';
                    $message = 'ERROR! No item with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "add_item":
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $form = $mItem->getEmptyForm(); // create an empty ItemTaskForm object
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $categoriesArr = $mCategory->getAllIdName($_SESSION['user_id']);
                //var_dump($categoriesArr);
                include_once 'views/item_form.php';
                break;
            case "view_item":
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $item = $mItem->select($key, $_SESSION['user_id']);
                if ($item) {
                    include_once 'views/item_view.php';
                } else { // This can possibly be an unauthorized access
                    $title = 'ERROR! No item with such ID.';
                    $message = 'ERROR! No item with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "delete_item": // JS confirmation and delete
                //trigger_error('Under construction! This feature: "'.$_GET['action'].'" is not yet implemented -');
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $mItem->delete($key, $_SESSION['user_id']);
                header("Location: ".BASE_URL."list_items_paginated");
                break;
            /* categories */
            case "save_category":
                if(isset($_POST['save_category'])) { // if "save" submit button has been clicked
                    //var_dump($_POST);
                    $valuesCategories[] = filter_input(INPUT_POST, 'name');
                    $valuesCategories[] = filter_input(INPUT_POST, 'description');
                    $valuesCategories[] = $_SESSION['user_id'];
                    if(isset($_POST['category_id']) && filter_input(INPUT_POST, 'category_id') != "") {
                        include_once 'models/category.php';
                        $mCategory = new ModelCategory();
                        $mCategory->update(filter_input(INPUT_POST, 'category_id'), $valuesCategories);
                    } else {
                        include_once 'models/category.php';
                        $mCategory = new ModelCategory();
                        $lastInsertCategoryId = $mCategory->insert($valuesCategories);
                    }
                } else {
                    throw new Exception('ERROR! Something went wrong while saving the data.'
                                       .' Please, contact the developer regarding this problem.');
                }
                // NO "break;"
            case "edit_category":
                if( isset($key) && $key != "" ) { // set $id from $_GET ...
                    $categoryID = $key;
                } elseif( isset($_POST['category_id']) && filter_input(INPUT_POST, 'category_id') != "" ) { // ... or $_POST
                    $categoryID = filter_input(INPUT_POST, 'category_id');
                } elseif( isset($lastInsertCategoryId) ) {
                    $categoryID = $lastInsertCategoryId;
                } else {
                    throw new Exception('ERROR! CategoryID can not be set.');
                }
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $form = $mCategory->getForm($categoryID);
                if ($form) {
                    include_once 'views/category_form.php';
                } else {
                    $title = 'ERROR! No category with such ID.';
                    $message = 'ERROR! No category with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "add_category":
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $form = $mCategory->getEmptyForm(); // create an empty CategoryForm object
                include_once 'views/category_form.php';
                break;
            case "view_category":
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $category = $mCategory->select($key, $_SESSION['user_id']);
                if ($category) {
                    include_once 'views/category_view.php';
                } else { // This can possibly be an unauthorized access
                    $title = 'ERROR! No category with such ID.';
                    $message = 'ERROR! No category with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "delete_category": // confirmation and delete
                //trigger_error('Under construction! This feature: "'.$_GET['action'].'" is not yet implemented -');
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $result = $mCategory->delete($key, $_SESSION['user_id']);
                if (!$result) {
                    header("Location: ".BASE_URL."list_categories");
                } else {
                    $title = "Error!";
                    $message = 'Error! Can not delete category while it has at least one item.';
                    include_once 'views/message.php';
                }
                break;
            case "list_categories":
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $categories = $mCategory->listAll($_SESSION['user_id']);
                include_once 'views/categories_list.php';
                break;
            /* contacts */
            case "save_contact":
                if(isset($_POST['save_contact'])) { // if "save" submit button has been clicked
                    //var_dump($_POST);
                    $valuesContacts[] = filter_input(INPUT_POST, 'family_name');
                    $valuesContacts[] = filter_input(INPUT_POST, 'given_name');
                    $valuesContacts[] = filter_input(INPUT_POST, 'dial_code1');
                    $valuesContacts[] = filter_input(INPUT_POST, 'phone1');
                    $valuesContacts[] = filter_input(INPUT_POST, 'dial_code2');
                    $valuesContacts[] = filter_input(INPUT_POST, 'phone2');
                    $valuesContacts[] = filter_input(INPUT_POST, 'country');
                    $valuesContacts[] = filter_input(INPUT_POST, 'skype');
                    $valuesContacts[] = filter_input(INPUT_POST, 'email');
                    $valuesContacts[] = filter_input(INPUT_POST, 'website');
                    $valuesContacts[] = filter_input(INPUT_POST, 'notes');
                    $valuesContacts[] = $_SESSION['user_id'];
                    if(isset($_POST['contact_id']) && filter_input(INPUT_POST, 'contact_id') != "") {
                        include_once 'models/contact.php';
                        $mContact = new ModelContact();
                        $mContact->update(filter_input(INPUT_POST, 'contact_id'), $valuesContacts);
                    } else {
                        include_once 'models/contact.php';
                        $mContact = new ModelContact();
                        $lastInsertContactId = $mContact->insert($valuesContacts);
                    }
                } else {
                    throw new Exception('ERROR! Something went wrong while saving the data.'
                                       .' Please, contact the developer regarding this problem.');
                }
                // NO "break;"
            case "edit_contact":
                if( isset($key) && $key != "" ) { // set $id from $_GET ...
                    $contactID = $key;
                } elseif( isset($_POST['contact_id']) && filter_input(INPUT_POST, 'contact_id') != "" ) { // ... or $_POST
                    $contactID = filter_input(INPUT_POST, 'contact_id');
                } elseif( isset($lastInsertContactId) ) {
                    $contactID = $lastInsertContactId;
                } else {
                    throw new Exception('ERROR! ContactID can not be set.');
                }
                include_once 'models/contact.php';
                $mContact = new ModelContact();
                $form = $mContact->getForm($contactID);
                if ($form) {
                    include_once 'views/contact_form.php';
                } else {
                    $title = 'ERROR! No contact with such ID.';
                    $message = 'ERROR! No contact with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "add_contact":
                include_once 'models/contact.php';
                $mContact = new ModelContact();
                $form = $mContact->getEmptyForm(); // create an empty ContactForm object
                include_once 'views/contact_form.php';
                break;
            case "view_contact":
                include_once 'models/contact.php';
                $mContact = new ModelContact();
                $contact = $mContact->select($key, $_SESSION['user_id']);
                $json = file_get_contents(BASE_URL.'selectOptions.json');
                $obj = json_decode($json);
                if ($contact) {
                    include_once 'views/contact_view.php';
                } else { // This can possibly be an unauthorized access
                    $title = 'ERROR! No contact with such ID.';
                    $message = 'ERROR! No contact with such ID.';
                    include_once 'views/message.php';
                }
                break;
            case "delete_contact":
                include_once 'models/contact.php';
                $mContact = new ModelContact();
                $mContact->delete($key, $_SESSION['user_id']);
                header("Location: ".BASE_URL."list_contacts");
                break;
            case "list_contacts":
                include_once 'models/contact.php';
                $mContact = new ModelContact();
                $contacts = $mContact->listAll($_SESSION['user_id']);
                $json = file_get_contents(BASE_URL.'selectOptions.json');
                $jsonObj = json_decode($json);
                include_once 'views/contacts_list.php';
                break;
            /* items */
            case "list_items_paginated":
                include_once 'controllers/paginator.php';
                $offset = isset($key) && $key > 0 ? intval($key) : 1;
                $perPage = 5;
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $total = $mItem->countRows($_SESSION['user_id']);
                $items = $mItem->listAllPaginated($perPage, $offset, $_SESSION['user_id']);
                include_once 'views/items_list_paginated.php';
                break;
            case "filter_items_by_category":
                include_once 'controllers/paginator.php';
                $categoryId = isset($_POST['category_id']) && filter_input(INPUT_POST, 'category_id') > 0 ? intval(filter_input(INPUT_POST, 'category_id')) : 1;
                $offset = isset($key) && $key > 0 ? intval($key) : 1;
                $perPage = 5;
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $total = $mItem->countRowsHasCategory($categoryId, $_SESSION['user_id']);
                $items = $mItem->filterByCategory($perPage, $offset, $categoryId, $_SESSION['user_id']);
                include_once 'models/category.php';
                $mCategory = new ModelCategory();
                $categoriesArr = $mCategory->getAllIdName($_SESSION['user_id']);
                include_once 'views/items_filter_by_category.php';
                break;
            case "sort_items_by_date_paginated":
                $order = isset($_POST['sort'])
                    && (isset($_POST['sort']) == "DESC" || "ASC") ? filter_input(INPUT_POST, 'sort') : 'DESC';
                include_once 'controllers/paginator.php';
                $offset = isset($key) && $key > 0 ? intval($key) : 1;
                $perPage = 5;
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $total = $mItem->countRowsDate($_SESSION['user_id']);
                $items = $mItem->sortByDatePaginated($perPage, $offset, $_SESSION['user_id'], $order);
                include_once 'views/items_sort_date_paginated.php';
                break;
            case "sort_items_by_priority_paginated":
                $order = isset($_POST['sort'])
                    && (isset($_POST['sort']) == "DESC" || "ASC") ? filter_input(INPUT_POST, 'sort') : 'ASC';
                include_once 'controllers/paginator.php';
                $offset = isset($key) && $key > 0 ? intval($key) : 1;
                $perPage = 5;
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $total = $mItem->countRowsPriority($_SESSION['user_id']);
                $items = $mItem->sortByPriorityPaginated($perPage, $offset, $_SESSION['user_id'], $order);
                include_once 'views/items_sort_priority_paginated.php';
                break;
            case "search_items":
                if( isset($_POST['search_query']) && filter_input(INPUT_POST, 'search_query') != "" ) {
                    // $SearchQuery - search form value; $SafeQuery - sql query value
                    $SearchQuery = $SafeQuery = filter_input(INPUT_POST, 'search_query');
                    $SafeQuery = strtr($SafeQuery, array('_' => '\_', '%' => '\%', '\\' => '\\\\'));
                    include_once 'models/item.php';
                    $mItem = new ModelItem();
                    $items = $mItem->search($SafeQuery, $_SESSION['user_id']);
		} else {
                    $items = NULL;
                }
		include_once 'views/items_search.php';
                break;
            case "links_to_xml":
                // default limit is 30 seconds
                set_time_limit(0); // maximum execution time is set to "no time limit"
                include_once 'models/item.php';
                $mItem = new ModelItem();
                $notes = $mItem->getNotesAll($_SESSION['user_id']);
                include_once 'controllers/export.php';
                $urls = BookmarksToXml::extractURLs($notes);
                if($urls) {
                    $bookmarks = BookmarksToXml::urlsToBookmarks($urls);
                    $filename = 'export/bookmarks_'.$_SESSION['user_id'].'.xml';
                    BookmarksToXml::saveAsXML($bookmarks, $filename);
                    $title = "Export Bookmarks to XML";
                    $message = 'You can <a href="'.BASE_URL.$filename.'" target="_blank">view / download</a> your XML file now.';
                    include_once 'views/message.php';
                } else {
                    $title = "Export Bookmarks to XML";
                    $message = "No URLs can be found.";
                    include_once 'views/message.php';
                }
                break;
            default:
		$title = 'ERROR! Action unknown.';
                $message = 'ERROR! Action unknown.';
                include_once 'views/message.php';
        }
    }
}

try
{
    if( !isset($_GET['action']) )
    {
        $action = "sign_in";
    }
    else
    {
        $action = filter_input(INPUT_GET, 'action');
    }

    if( !isset($_GET['key']) )
    {
        $key = 0;
    }
    else
    {
        $key = urldecode(filter_input(INPUT_GET, 'key'));
    }
    
    Controller::execute($action, $key);
} catch (Exception $e) {
    echo 'Caught exception: '.$e->getMessage().'<br />';
}