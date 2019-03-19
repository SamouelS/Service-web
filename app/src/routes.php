<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/{[table:patients|personnes|badges|infirmieres|visites}]', function (Request $request, Response $response, array $args) {
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
    
    $sqlRequest = ' SELECT * 
                    FROM personne_login pl, personne p
                    where pl.id = p.id
                ';
    $retour = $this->db->query($sqlRequest); 
    $json['status'] = false;
    foreach ($retour as $row) {
        if($row['login'] == $request->getParams()['login'] && $row['mp'] == md5($request->getParams()['mp'])){
            $json['status'] = true;
            $json['personne']=$row;
        }
        
    }
    //var_dump($json);
    $json = json_encode($json);
    return $json;

});

$app->get('[/{table:patient|personne|badge|infirmiere|visite}/{id:\d*}]', function (Request $request, Response $response, array $args) {
    
    $sqlRequest = 'SELECT * FROM '.$args['table'].' WHERE id='.$args['id'];
    $retour = $this->db->query($sqlRequest);
    $json= array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    return $json;
    var_dump($sqlRequest);

});
