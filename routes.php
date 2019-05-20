<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

function utf8($n) {
    return (utf8_encode($n));
}

//RETOURNES TOUT LES TUPLES DUNE TABLE
$app->get('/{table:patients|personnes|badges|infirmieres|visites}', function (Request $request, Response $response, array $args) {
    //.preg_replace(RegEx, New, Variable)
    $sth = $this->db->prepare('SELECT * FROM '.preg_replace('/s$/', '', $args['table']).';');
    $sth->execute();
    $json = array();
    while($row = $sth->fetch (PDO::FETCH_ASSOC))
    {
        $json[]=array_map('utf8', $row);
    }
    $response = $json;
    
    return $this->response->withJson($response);
});

//GET LA COLONNE D'UNE TABLE VIA UN ID
$app->get('/{table:patient|personne|badge|infirmiere|visite}/{id:\d*}', function (Request $request, Response $response, array $args) {
    $sth = $this->db->prepare('SELECT * FROM '.$args['table'].' WHERE id='.$args['id']);
    $sth->execute();
    $json = array();
    while($row = $sth->fetch (PDO::FETCH_ASSOC))
    {
        $json[]=array_map('utf8', $row);
    }
    $response = $json;
    
    return $this->response->withJson($response);
});

//RENVOIE LES DETAILS D'UNE PERSONNE SI ELLE REUSSI A SE CONNECTER OU STATUS=FALSE SI CELA ECHOUE
$app->get('/connect', function ($request, $response, $args) {
    $req = 'SELECT p.* FROM personne_login pl, personne p 
    where pl.id = p.id AND pl.login = "'.$request->getParams()['login'].'" AND pl.mp = "'.md5($request->getParams()['mp']).'";';
    $this->db->exec("SET CHARACTER SET utf8");
    $sth=$this->db->query($req);
    if($sth) {
        if($sth ->rowCount()==1) {
            $response = $sth->fetchObject();
        }
        else {
            $response = [];
            $response['status'] = "false";
        }
    }
    else {
        $response = [];
        $response['status'] = "false";
    } 
    return $this->response->withJson($response);
});

//INSERE UNE PUERSONNE
$app->post('/personne', function (Request $request, Response $response, array $args) {
    $params = $request->getParsedBody();
    $t = array(
        'nom'=> array('type'=>'string','value'=>'null'), 
        'prenom'=>array('type'=>'string','value'=>'null'), 
        'sexe'=>array('type'=>'string','value'=>'null'), 
        'date_naiss'=>array('type'=>'string','value'=>'null'), 
        'date_deces'=>array('type'=>'string','value'=>'null'), 
        'ad1'=>array('type'=>'string','value'=>'null'), 
        'ad2'=>array('type'=>'string','value'=>'null'), 
        'cp'=>array('type'=>'int','value'=>'null'), 
        'ville'=>array('type'=>'string','value'=>'null'), 
        'tel_fixe'=>array('type'=>'string','value'=>'null'), 
        'tel_port'=>array('type'=>'string','value'=>'null'), 
        'mail' => array('type'=>'string','value'=>'null')
        
    );
    foreach($t as $key=>$value)
    {
        if(isset($params[$key]))
        {           
            if($t[$key]['type']=='string')
            {
                $t[$key]['value']='"'.$params[$key].'"';
            }
            elseif($t[$key]['type'] == 'int')
            {
                $t[$key]['value']=$params[$key];
            }
        }
    }
    $sqlRequest =   'INSERT INTO personne (nom, prenom, sexe, date_naiss, date_deces, ad1, ad2, cp, ville, tel_fixe, tel_port, mail)
                        VALUES ('. $t['nom']['value'].','. $t['prenom']['value'].','. $t['sexe']['value'].','. $t['date_naiss']['value'].','. $t['date_deces']['value'].','. $t['ad1']['value'].','. $t['ad2']['value'].','. $t['cp']['value'].','. $t['ville']['value'].','. $t['tel_fixe']['value'].','. $t['tel_port']['value'].','. $t['mail']['value'].')';
    $this->db->exec("SET CHARACTER SET utf8");
    $vretour = $this->db->query($sqlRequest);
});

//INSERE UN BADGE A UNE INFIRMIERE
$app->post('/infirmiere_badges', function (Request $request, Response $response, array $args) {
    $params = $request->getParsedBody();
    $tableau = array(
        'id_infirmiere'=> array('type'=>'int', 'value'=>'null'),
        'id_badge'=> array('type'=>'int', 'value'=>'null'),
        'date_deb'=> array('type'=>'string', 'value'=>'null')
    );
    foreach($tableau as $key=>$value) {
        if(isset($params[$key])) {           
            if($tableau[$key]['type']=='string') {
                $tableau[$key]['value']='"'.$params[$key].'"';
            }
            elseif($tableau[$key]['type'] == 'int') {
                $tableau[$key]['value']=$params[$key];
            }  
        }
    }
    var_dump($tableau);
    $sqlRequest = 'INSERT INTO infirmiere_badge(id_infirmiere, id_badge, date_deb) VALUES ('.$tableau['id_infirmiere']['value'].','.$tableau['id_badge']['value'].','.$tableau['date_deb']['value'].')';
    $this->db->exec("SET CHARACTER SET utf8");
    $vretour = $this->db->query($sqlRequest);
});

//UPDATE
$app->put('[/{table:patient|personne|badge|infirmiere|visite|infirmiere_badge}/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest = 'UPDATE '.$args['table'].' SET';
    $retour = $request->getParsedBody();
    $i = count($retour);
    foreach($retour as $paramerte=>$valeur) {
        $i = $i - 1;
        if(gettype($valeur) == "integer") {
            $sqlRequest = $sqlRequest." ".$paramerte." = ".$valeur;
        } else {
            $sqlRequest = $sqlRequest." ".$paramerte." = '".$valeur."'";
        }
        if ($i != 0) {
            $sqlRequest = $sqlRequest.',';
        } else {
            $sqlRequest = $sqlRequest.' WHERE id = '.$args['id'].';';
        }
    }
    $this->db->exec("SET CHARACTER SET utf8");
    $this->db->query($sqlRequest);
});

//DELETE COLONNE VIA ID
$app->delete('[/{table:patient|personne|badge|infirmiere|visite}/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest ='DELETE FROM '.$args['table'].' WHERE id = '.$args['id'].';';
    $this->db->query($sqlRequest);
});