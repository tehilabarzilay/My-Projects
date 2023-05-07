<?php

require ('dbconfig.php');

//Get the website html
function file_get_contents_curl($url) {
    $ch = curl_init();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

//Fix the html code
function fix_html_code($code,$website) {
    $code = str_replace('src="//', 'src="https://',$code);
    $code = str_replace('href="//', 'href="https://',$code);
    $code = str_replace('src="/', 'src="https://'.$website.'/', $code);
    $code = str_replace('href="/', 'href="https://'.$website.'/', $code);
    $code = str_replace('data-src="/', 'data-src="https://'.$website.'/', $code);
    $code = str_replace('data-srcset="/', 'data-srcset="https://'.$website.'/', $code);
    return $code;
}

//Get random website from the DB
function get_random_website_from_db() {
    global $host,$dbname,$username,$password;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $stmt = $pdo->query('SELECT * FROM websites ORDER BY rand() LIMIT 1'); 
        $row = $stmt->fetch();
        return $row;
    } catch (PDOException $e) {
        die("Could not connect to the database $dbname :" . $e->getMessage());
    }   
}

//Insert websites url and html to the DB
function insert_websites_to_db($websites) {
    global $host,$dbname,$username,$password;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->query('TRUNCATE TABLE websites');
        foreach ($websites as $row) {
            $code = file_get_contents_curl('https://'.$row); 
            if (!empty($code)) {       
                $sql = "INSERT IGNORE INTO websites VALUES (?,?)";
                $q = $pdo->prepare($sql); 
                $q->execute(array($row,$code));
            }
        }
    } catch (PDOException $e) {
        die("Could not connect to the database $dbname :" . $e->getMessage());
    }
}

?>