<?php
ini_set('memory_limit', '-1');
require_once('./config.php');
require_once('./parse_signed_request.php');

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'GET'])) {
    http_response_code(405);
    echo "Method Not Allowed";
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=' . DATABASE, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $err) {
    die($err->getMessage());
}

if ('POST' == $_SERVER['REQUEST_METHOD']) {

    //validate input
    if (!filter_input(INPUT_POST, 'user_score', FILTER_VALIDATE_INT) ||
        !filter_input(INPUT_POST, 'signed_request', FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9 .]*$/")))
    ) {

        http_response_code(400);
        echo "Bad Request";
    }
    $signed_request = $_POST['signed_request'];
    $user_score = (int)$_POST['user_score'];

    $output = parse_signed_request($signed_request);
    $user_id = (int)$output['user_id'];
    $posted = date('Y-m-d');


    $stmt = $pdo->prepare("INSERT INTO users(user_id, user_score, posted) VALUES (?, ?, ?)");
    $stmt->execute(array($user_id, $user_score, $posted));
} elseif ('GET' == $_SERVER['REQUEST_METHOD']) {

    $stmt = $pdo->prepare('SELECT count(distinct `user_id`) from users');
    $stmt->execute();
    $total_players = $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT count(distinct `user_id`) from users where posted = :todayDate');
    $stmt->bindValue(':todayDate', date('Y-m-d'));
    $stmt->execute();
    $total_players_today = $stmt->fetchColumn();

    $top_players = $pdo->query('SELECT distinct `user_id`, `user_score` from users ORDER BY `user_score` DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);

    $mondayLastWeek = date("Y-m-d", strtotime('monday', strtotime('last week')));
    $sundayLastWeek = date("Y-m-d", strtotime('sunday', strtotime('last week')));
    $mondayThisWeek = date("Y-m-d", strtotime('monday', strtotime('this week')));
    $today = date("Y-m-d", strtotime('today'));
    $improvedPlayers = $pdo->query("SELECT
   tmp.user_id,
   tmp.old_best,
   tmp.new_best 
FROM
   (
      SELECT
         t1.user_id,
         t1.user_score AS old_best,
         t2.user_score AS new_best 
      FROM
         users t1 
         LEFT JOIN
            users AS t2 
            ON t1.user_id = t2.user_id 
            AND 
            (
               t2.posted BETWEEN '" . $mondayThisWeek . "' AND '" . $today . "' 
            )
            AND t2.user_score > t1.user_score 
            AND t2.user_score IS NOT NULL 
      WHERE
         (
            t1.posted BETWEEN '" . $mondayLastWeek . "' AND '" . $sundayLastWeek . "' 
         )
   )
   AS tmp 
WHERE
   tmp.new_best IS NOT NULL;")->fetchAll(PDO::FETCH_ASSOC);

    $improvedPlayersCleanUp = [];
    foreach ($improvedPlayers as $player) {
        if (!isset($improvedPlayersClean[$player['user_id']])) {
            $improvedPlayersCleanUp[$player['user_id']] = array(
                'user_id' => $player['user_id'],
                'old_best' => $player['old_best'],
                'new_best' => $player['new_best']
            );
        }
    }

    http_response_code(200);
    header("Content-type:application/json");
    $response = array(
        'Total players' => $total_players,
        'Players today' => $total_players_today,
        'Top players' => $top_players,
        'Improved players' => $improvedPlayersCleanUp
    );
    echo json_encode($response);
}
