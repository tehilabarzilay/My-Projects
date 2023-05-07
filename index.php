<?php

require ('functions.php');

$random_website = get_random_website_from_db();

$code = fix_html_code($random_website['html'],$random_website['url']);

//Add my logo
echo '<img style="z-index: 999999999; position: fixed; top: 0; background: #fff; border: solid 5px #000; width:auto;" src="C:\Users\tehil\OneDrive\Documents\My-Projects\scraping\mylogo.jpg" alt="">';

//Change the title of the website (using regular expressions)
$new_title = 'Hello there';
$code = preg_replace('/<title>.*?<\/title>/', '<title>' . $new_title . '</title>', $code);

//Display the website
if (!$code) {
    echo 'Error Fetching :' .$random_website['url'];
} else {
    echo $code;
}
?>
