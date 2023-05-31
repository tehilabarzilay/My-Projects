<?php
header('Content-Type: application/json; charset=utf-8');
//Display errors in page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require ('dbconfig.php'); 

//check winning
function is_winning_move($board) {
    return
        ($board['0,0'] == $board['0,1'] && $board['0,1'] == $board['0,2'] && !empty($board['0,0'])) || 
        ($board['1,0'] == $board['1,1'] && $board['1,1'] == $board['1,2'] && !empty($board['1,0'])) || 
        ($board['2,0'] == $board['2,1'] && $board['2,1'] == $board['2,2'] && !empty($board['2,0'])) || 
        ($board['0,0'] == $board['1,0'] && $board['1,0'] == $board['2,0'] && !empty($board['0,0'])) || 
        ($board['0,1'] == $board['1,1'] && $board['1,1'] == $board['2,1'] && !empty($board['0,1'])) || 
        ($board['0,2'] == $board['1,2'] && $board['1,2'] == $board['2,2'] && !empty($board['0,2'])) || 
        ($board['0,0'] == $board['1,1'] && $board['1,1'] == $board['2,2'] && !empty($board['0,0'])) || 
        ($board['0,2'] == $board['1,1'] && $board['1,1'] == $board['2,0'] && !empty($board['0,2']));
}

//get the empty values in the array
function get_empty_values($var){
    return empty($var);
}

//play move function
function play($session_id, $player_id, $posion) {
    global $host,$dbname,$username,$password;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $stmt = $pdo->prepare("SELECT * FROM `Tic-Tac-Toe-Game` WHERE session_id=?"); 
                $stmt->execute([$_GET['session_id']]); 
                $board = $stmt->fetch();
                $boardArray = json_decode($board['board'],true);
            
            //single player mode
            if ($board['mode'] == 'single') {
                if (empty($boardArray[$_GET['posion']]) && ($board['status'] == 'new' || $board['status'] == '0')){
                    $boardArray[$_GET['posion']] = 'O';
                } else {
                    echo json_encode(
                        array(
                            'error' =>  'NOT ALLOWED',
                            'board' => $boardArray
                        )
                    );
                    exit();
                }
                
                if (is_winning_move($boardArray)) {
                    echo json_encode (
                        array (
                            'winner' => '0',
                            'board' => $boardArray
                        )
                    );
                } else {
                    $result = array_filter($boardArray, "get_empty_values");
                    if (empty($result)) {
                        echo json_encode (
                            array (
                                'winner' =>false,
                                'board' => $boardArray
                            )
                        );
                    } else {
                        $key = array_rand($result);
                        $boardArray[$key] = 'X';
                        echo json_encode (
                            array (
                                'winner' =>is_winning_move($boardArray) ? '1': false,
                                'board' => $boardArray
                            )
                        );
                    }
                }

                $board = json_encode($boardArray);
                $sql = "UPDATE `Tic-Tac-Toe-Game` SET `board`=?, `status`=? WHERE `session_id`=?";
                $stmt= $pdo->prepare($sql);
                $stmt->execute([$board, '0', $_GET['session_id']]);

            } else {
                //multiplayer mode
                if (empty($boardArray[$_GET['posion']]) && ($board['status'] == 'new' || $board['status'] == $_GET['player_id'])){
                    if ($_GET['player_id'] == 0){
                        $boardArray[$_GET['posion']] = 'O';
                        $status = 1;
                    } elseif ($_GET['player_id'] == 1) {
                        $boardArray[$_GET['posion']] = 'X';
                        $status = 0;
                    }
                        
                    $board = json_encode($boardArray);
                    $sql = "UPDATE `Tic-Tac-Toe-Game` SET `board`=?, `status`=? WHERE `session_id`=?";
                    $stmt= $pdo->prepare($sql);
                    $stmt->execute([$board, $status, $_GET['session_id']]);

                    echo json_encode (
                        array (
                            'winner' =>is_winning_move($boardArray) ? $_GET['player_id']: false,
                            'board' => $boardArray
                        )
                    );

                } else {
                    echo json_encode(
                        array(
                            'error' =>  'NOT ALLOWED'
                        )
                    );
                } 
            }
 
    } catch (PDOException $e) {
        die("Could not connect to the database $dbname :" . $e->getMessage());
    }
}

$player_id = !empty($_GET['player_id']) ? $_GET['player_id'] : '';
play($_GET['session_id'], $player_id, $_GET['posion']);
