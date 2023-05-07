<?php

$csv_file = 'task_3.csv';

//read the csv file into an array 
$csv = csv_to_array($csv_file);

function csv_to_array($csv_file){
    $file_to_read = fopen($csv_file, 'r');
    while (!feof($file_to_read) ) {
        $lines[] = fgetcsv($file_to_read, ',');
    }
    fclose($file_to_read);
    return $lines;
}


// start table 

// The page to display 
$page = $_GET['page'] ?? 1;

// The number of records to display per page
$page_size = 10;
// Calculate total number of records, and total number of pages
$total_records = count($csv);
$total_pages   = ceil($total_records / $page_size);

// Validation: Page to display can not be greater than the total number of pages
if ($page > $total_pages) {
    $page = $total_pages;
}

// Calculate the position of the first record of the page to display
$offset = ($page - 1) * $page_size;

// Get the subset of records to be displayed from the array and print
$data = array_slice($csv, $offset, $page_size);
echo build_table($data);
echo '<br>';

function build_table($data){
    $html = '';
    $html .= '<table id="myTable">';
   
    foreach($data as $rows){
        // data rows
        $html .= '<tr>';
        foreach($rows as $cell){   
              $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
        $html .= '<tr>';
    }
    // finish table and return it
    $html .= '</table>';
    return $html;
}

// start pagination

for ($p = 1; $p <= $total_pages; $p++) {
    $style_active = '';
    if ($p == $page) {
        $style_active = 'style="font-weight:bold"';
    }

    echo "<a $style_active href='https://tehila-designs.com/xxx/?page=$p'>$p</a>  &nbsp;";

}

?>



