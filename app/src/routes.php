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

$app->get('[/{table:patients|personnes|badges|infirmieres|visites}/{id:\d*}]', function (Request $request, Response $response, array $args) {

    $sqlRequest = 'SELECT * FROM '.preg_replace('/s$/', '', $args['table']).' WHERE id='.$args['id'];
    $retour = $this->db->query($sqlRequest);
    $json= array();
    foreach ($retour as $row) {
        $json[] = json_encode($row);
    }
    $json = json_encode($json);
    return $json;
    var_dump($sqlRequest);

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

$app->post('/personne/patient', function (Request $request, Response $response, array $args) {
    $vretour=array('statut' => false);
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
    //VERIFIER LES PARAM OBLIGATOIRE !
    foreach($t as $key=>$value)
    {
        if(isset($params[$key]))
        {           
            if($t[$key]['type']=='string')
            {
                $t[$key]['value']='"'.$params[$key].'"';
            }
            else
            {
                $t[$key]['value']=$params[$key];
            }
        }
    }

    $sqlRequest =   'INSERT INTO personne (nom, prenom, sexe, date_naiss, date_deces, ad1, ad2, cp, ville, tel_fixe, tel_port, mail)
                    VALUES ('. $t['nom']['value'].','. $t['prenom']['value'].','. $t['sexe']['value'].','. $t['date_naiss']['value'].','. $t['date_deces']['value'].','. $t['ad1']['value'].','. $t['ad2']['value'].','. $t['cp']['value'].','. $t['ville']['value'].','. $t['tel_fixe']['value'].','. $t['tel_port']['value'].','. $t['mail']['value'].')';

    if($this->db->query($sqlRequest)){
        $vretour['statut'] = 'success';
    }
    $vretour = json_encode($vretour);
    return $vretour;
    //if($result == '')
    //return $result;
    //echo $sqlRequest;
    //var_dump($result);


});



