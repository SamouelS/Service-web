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
    $params = $request->getParsedBody();
    $t = array('nom'=>'null', 'prenom'=>'null', 'sexe'=>'null', 'date_naiss'=>'null', 'date_deces'=>'null', 'ad1'=>'null', 'ad2'=>'null', 'cp'=>'null', 'ville'=>'null', 'tel_fixe'=>'null', 'tel_port'=>'null', 'mail'=>'null');
    foreach($t as $key=>$value)
    {
        if(isset($params[$key]))
        {
            $t[$key]='"'.$params[$key].'"';

        }
    }

    $sqlRequest =   'INSERT INTO personne (nom, prenom, sexe, date_naiss, date_deces, ad1, ad2, cp, ville, tel_fixe, tel_port, mail)
                    VALUES ('. $t['nom'].','. $t['prenom'].','. $t['sexe'].','. $t['date_naiss'].','. $t['date_deces'].','. $t['ad1'].','. $t['ad2'].','. $t['cp'].','. $t['ville'].','. $t['tel_fixe'].','. $t['tel_port'].','. $t['mail'].')';
    //$retour = execRequete($sqlRequest,$this->db); 
    $result = $this->db->query($sqlRequest);

    return $result;
    //var_dump($sqlRequest);


});

function execRequete($uneRequete,$obj)
{		
    $result = $obj->query($uneRequete);
    if($result){
        return $result; 
    }
    else{
        echo "\nPDO::errorInfo():\n";
        print_r($obj->errorInfo());
    }  
}

