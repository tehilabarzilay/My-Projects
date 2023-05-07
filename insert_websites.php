<?php

require ('functions.php');
require ('websites_list.php'); 

ini_set('max_execution_time', '5000');
insert_websites_to_db($websites);

?>