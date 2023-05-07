<?php

class TableFromJson {
    private $html;
	private $api_array;


    function __construct($url) {
        $this->html = '';
        //read json file from url in php
        $readJSONFile = file_get_contents($url);
        $this->api_array = json_decode($readJSONFile, TRUE);
    }


    //build table
    public function build_table($table_titles = false, $search_term = false){
        if (!$table_titles) {
            $table_titles = array();
            foreach ($this->api_array[0] as $key=>$value) {
                $table_titles[] = $key;
            }      
        }  
        // start table
        $this->html .= '<table>';
        // header row
        $this->add_title_table($table_titles);    
        // data rows
        $titles_length = count($table_titles);
        foreach($this->api_array as $key=>$value){
            //count titles
            $counter=0;
            if (!$search_term || strstr($value["ADDRESS"], $search_term) || strstr($value["Name"], $search_term)) {
                $this->html .= '<tr>';
                foreach($value as $key2=>$value2){
                    if ($counter < $titles_length) {
                        $this->html .= '<td>' . htmlspecialchars($value2) . '</td>';
                    }
                    $counter++;
                }  
                $this->html .= '</tr>';             
            }
        }        
        // finish table and return it
        $this->html .= '</table>';
        return $this->html;
    } 


    //add titles to table
    private function add_title_table($table_titles) {
        $this->html .= '<tr>';
        foreach($table_titles as $value){
            $this->html .= '<th>' . htmlspecialchars($value) . '</th>';
        }
        $this->html .= '</tr>';  
    }

    
    //create locations array
    public function location_array($search_term = false){
        $map_array = array();
        foreach ($this->api_array as $key=>$value) {
            if (!$search_term || strstr($value["ADDRESS"], $search_term) || strstr($value["Name"], $search_term)) {
            $map_array[] = array ($value["Name"], $value["lat"], $value["lon"]);
            }
        }
        return json_encode($map_array);
    }
} 
?>