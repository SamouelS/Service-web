<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{table:patients|personnes|badges|infirmieres|visites}]', function (Request $request, Response $response, array $args) {
    $sqlRequest = 'SELECT * FROM '.preg_replace('/s$/', '', $args['table']);
    $retour = $this->db->query($sqlRequest);
    $json = array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    return $json;
    //var_dump($json);
    //var_dump($sqlRequest);
});

$app->get('/connect', function (Request $request, Response $response, array $args) {
    
    $sqlRequest = 'SELECT * FROM personne_login';
    $retour = $this->db->query($sqlRequest); 
    $json['status'] = false;
    foreach ($retour as $row) {
        if($row['login'] == $request->getParams()['login'] && $row['mp'] == md5($request->getParams()['mdp'])){
            $json['status'] = true;
        }
    }
   
    $json = json_encode($json);
    return $json;

});
