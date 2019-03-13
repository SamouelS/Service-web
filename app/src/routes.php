<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{table:patient|personne}]', function (Request $request, Response $response, array $args) {
    $sqlRequest = 'SELECT * FROM '.$args['table'];
    $retour = $this->db->query($sqlRequest);
    $json = array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    var_dump($json);

    //var_dump($sqlRequest);


});
