<?php
header('Content-Type: application/json; charset=utf-8');
//Display errors in page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require ('dbconfig.php');

function init_game($mode, $difficulty, $names) {
    global $host,$dbname,$username,$password;
    $board = array(
        '0,0' => '',
        '0,1' => '',
        '0,2' => '',
        '1,0' => '',
        '1,1' => '',
        '1,2' => '',
        '2,0' => '',
        '2,1' => '',
        '2,2' => ''
    );
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $sql = "INSERT INTO `Tic-Tac-Toe-Game` (`mode`, `level`, `status`, `players_names` ,`board`) VALUES (?,?,?,?,?)";
                $q = $pdo->prepare($sql); 
                $q->execute(array($mode, $difficulty, 'new', $names, json_encode($board) ));
                $stmt = $pdo->query("SELECT LAST_INSERT_ID()");
                $lastId = $stmt->fetchColumn();
                echo json_encode(
                        array(
                            'session_id' => $lastId
                        ) 
                );
    } catch (PDOException $e) {
        die("Could not connect to the database $dbname :" . $e->getMessage());
    }
}

$difficulty = !empty($_GET['difficulty']) ? $_GET['difficulty'] : '';
init_game($_GET['mode'], $difficulty, $_GET['names']);

 
?>