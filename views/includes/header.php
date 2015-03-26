<div id="header">
    <div id="headerLeft">
    <a href="<?=BASE_URL?>review"><img src="<?=BASE_URL?>views/images/logo.png" alt="Personal Information Manager" height="135" width="185" /></a>
    </div>
    <div id="headerRight">Account:<br />
<?php
echo $_SESSION['first_name'];
echo " ".$_SESSION['last_name'];
?><br />
    <a href="<?=BASE_URL?>sign_out">Sign out</a><br /><br />
    <form name="search" action="<?=BASE_URL?>search_items" method="POST">
        <input type="text" id="search_box" name="search_query" value="<?=isset($SearchQuery)?$SearchQuery:''?>" size="25" /><br />
        <input type="submit" value=" search " name="search" />
    </form>
<?php
if ($_SESSION['user_id'] == 1) {
    echo '<a href="'.BASE_URL.'admin">Administration</a>';
}
?>
    </div>

<br style="clear: left;" />
</div>
<div id="navigation">
    <a href="<?=BASE_URL?>review">Review</a> | <a href="<?=BASE_URL?>add_item">Add Item</a> | <a href="<?=BASE_URL?>list_items_paginated">List Items</a> | <a href="<?=BASE_URL?>sort_items_by_date_paginated">Sort Items by Date</a> | <a href="<?=BASE_URL?>sort_items_by_priority_paginated">Sort Items by Priority</a> | <a href="<?=BASE_URL?>filter_items_by_category">Filter Items by Category</a><br /><a href="<?=BASE_URL?>add_category">Add Category</a> | <a href="<?=BASE_URL?>list_categories">List Categories</a> | <a href="<?=BASE_URL?>add_contact">Add Contact</a> | <a href="<?=BASE_URL?>list_contacts">List Contacts</a> | <a href="<?=BASE_URL?>links_to_xml" title="Warning! This may take some time.">Export Bookmarks to XML</a>
</div>