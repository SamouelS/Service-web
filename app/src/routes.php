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
    
    $execRequete = $this->execRequete;
    $vretour = $execRequete($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;

});
/*
$app->post('/patient', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'informations_medicales' => array('type'=>'string','value'=>'null'),
        'personne_de_confiance' => array('type'=>'int','value'=>'null'),
        'infirmiere_souhait' => array('type'=>'int','value'=>'null')     
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
    $sqlRequest =   'INSERT INTO patient (id, informations_medicales, personne_de_confiance, infirmiere_souhait)
                    VALUES ('. $t['id']['value'].','. $t['informations_medicales']['value'].','. $t['personne_de_confiance']['value'].','. $t['infirmiere_souhait']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);

    return $vretour;
    
});

$app->post('/infirmiere', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'fichier_photo' => array('type'=>'string','value'=>'null')  
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
    $sqlRequest =   'INSERT INTO infirmiere (id, fichier_photo)
                    VALUES ('. $t['id']['value'].','. $t['fichier_photo']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/administrateur', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null') 
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
    $sqlRequest =   'INSERT INTO administrateur (id)
                    VALUES ('. $t['id']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/badge', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'uid'=> array('type'=>'string','value'=>'null'), 
        'actif'=> array('type'=>'int','value'=>'null')
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
    $sqlRequest =   'INSERT INTO badge (id,uid,actif)
                    VALUES ('.$t['id']['value'].','.$t['uid']['value'].','.$t['actif']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/categorie_indisponibilite', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'libelle'=> array('type'=>'string','value'=>'null')

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
    $sqlRequest =   'INSERT INTO categorie_indisponibilite (id,libelle)
                    VALUES ('.$t['id']['value'].','.$t['libelle']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/categ_soins', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'libel'=> array('type'=>'string','value'=>'null'), 
        'description'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO categ_soins (id,libel,description)
                    VALUES ('.$t['id']['value'].','.$t['libel']['value'].','.$t['description']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/chambre_forte', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'badge'=> array('type'=>'int','value'=>'null'), 
        'date'=> array('type'=>'string','value'=>'null'), 
        'acces_ok'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO chambre_forte (badge, date, acces_ok)
                    VALUES ('.$t['badge']['value'].','.$t['date']['value'].','.$t['acces_ok']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});
$app->post('/convalescence', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id_patient'=> array('type'=>'int','value'=>'null'), 
        'id_lieux'=> array('type'=>'int','value'=>'null'), 
        'date_deb'=> array('type'=>'string','value'=>'null'), 
        'date_fin'=> array('type'=>'string','value'=>'null'), 
        'chambre'=> array('type'=>'string','value'=>'null'), 
        'tel_direct'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO convalescence (id_patient, id_lieux, date_deb, date_fin, chambre, tel_direct)
                    VALUES ('.$t['id_patient']['value'].','.$t['id_lieux']['value'].','.$t['date_deb']['value'].','.$t['date_fin']['value'].','.$t['chambre']['value'].','.$t['tel_direct']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/indisponibilite', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'infirmiere'=> array('type'=>'int','value'=>'null'), 
        'date_debut'=> array('type'=>'string','value'=>'null'), 
        'date_fin'=> array('type'=>'string','value'=>'null'),
        'heure_deb'=> array('type'=>'string','value'=>'null'),  
        'heure_fin'=> array('type'=>'string','value'=>'null'), 
        'categorie'=> array('type'=>'int','value'=>'null')

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
    $sqlRequest =   'INSERT INTO convalescence (infirmiere, date_debut, date_fin, heure_deb, heure_fin, categorie)
                    VALUES ('.$t['infirmiere']['value'].','.$t['date_debut']['value'].','.$t['date_fin']['value'].','.$t['heure_deb']['value'].','.$t['heure_fin']['value'].','.$t['categorie']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/infirmiere_badge', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id_infirmiere'=> array('type'=>'int','value'=>'null'), 
        'id_badge'=> array('type'=>'int','value'=>'null'), 
        'date_deb'=> array('type'=>'string','value'=>'null'), 
        'date_fin'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO convalescence (id_infirmiere, id_badge, date_deb, date_fin)
                    VALUES ('.$t['id_infirmiere']['value'].','.$t['id_badge']['value'].','.$t['date_deb']['value'].','.$t['date_fin']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/lieu_convalescence', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'titre'=> array('type'=>'strin','value'=>'null'), 
        'ad1'=> array('type'=>'string','value'=>'null'), 
        'ad2'=> array('type'=>'string','value'=>'null'),
        'ville'=> array('type'=>'string','value'=>'null'),
        'tel_fixe'=> array('type'=>'string','value'=>'null'),
        'contact'=> array('type'=>'int','value'=>'null')
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
    $sqlRequest =   'INSERT INTO lieu_convalescence (id, titre,ad1,ad2,ville,tel_fixe,contact)
                    VALUES ('.$t['id']['value'].','.$t['titre']['value'].','.$t['ad1']['value'].','.$t['ad2']['value'].','.$t['ville']['value'].','.$t['tel_fixe']['value'].','.$t['contact']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/peronne_login', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'login'=> array('type'=>'string','value'=>'null'), 
        'mp'=> array('type'=>'string','value'=>'null'), 
        'derniere_connexion'=> array('type'=>'string','value'=>'null'),
        'nb_tentative_erreur'=> array('type'=>'int','value'=>'null')
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
    $sqlRequest =   'INSERT INTO personne_login (id,login,mp,derniere_connexion,nb_tentative_erreur)
                    VALUES ('.$t['id']['value'].','.$t['login']['value'].','.$t['mp']['value'].','.$t['derniere_connexion']['value'].','.$t['nb_tentative_erreur']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/soins', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'id_categ_soins'=> array('type'=>'int','value'=>'null'), 
        'id_type_soins'=> array('type'=>'int','value'=>'null'), 
        'libelle'=> array('type'=>'string','value'=>'null'), 
        'description'=> array('type'=>'string','value'=>'null'), 
        'coefficient'=> array('type'=>'int','value'=>'null'),
        'date'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO soins (id, id_categ_soins, id_type_soins, libelle, description, coefficient, date)
                    VALUES ('.$t['id']['value'].','.$t['id_categ_soins']['value'].','.$t['id_type_soins']['value'].','.$t['libelle']['value'].','.$t['description']['value'].','.$t['coeficient']['value'].','.$t['date']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});
$app->post('/soins_visite', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'visite'=> array('type'=>'int','value'=>'null'), 
        'id_categ_soins'=> array('type'=>'int','value'=>'null'), 
        'id_type_soins'=> array('type'=>'int','value'=>'null'), 
        'id_soins'=> array('type'=>'int','value'=>'null'), 
        'prevu'=> array('type'=>'string','value'=>'null'), 
        'realise'=> array('type'=>'int','value'=>'null')
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
    $sqlRequest =   'INSERT INTO soins_visite (visite,id_categ_soins,id_type_soins,id_soins,prevu,realise)
                    VALUES ('.$t['visite']['value'].','.$t['id_categ_soins']['value'].','.$t['id_type_soins']['value'].','.$t['id_soins']['value'].','.$t['prevu']['value'].','.$t['realise']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});
$app->post('/temoignage', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'personne_login'=> array('type'=>'int','value'=>'null'), 
        'contenu'=> array('type'=>'int','value'=>'null'), 
        'date'=> array('type'=>'int','value'=>'null'), 
        'valide'=> array('type'=>'string','value'=>'null')       
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
    $sqlRequest =   'INSERT INTO temoignage (id, personne_login, contenu, date,valide)
                    VALUES ('.$t['id']['value'].','.$t['personne login']['value'].','.$t['contenu']['value'].','.$t['date']['value'].','.$t['valide']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/token', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'id_login'=> array('type'=>'int','value'=>'null'), 
        'date'=> array('type'=>'string','value'=>'null'), 
        'jeton'=> array('type'=>'istring','value'=>'null')      
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
    $sqlRequest =   'INSERT INTO token (id, id_login,date,jeton)
                    VALUES ('.$t['id']['value'].','.$t['id_login']['value'].','.$t['date']['value'].','.$t['jeton']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/type_soins', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id_categ_soins'=> array('type'=>'int','value'=>'null'), 
        'id_type_soins'=> array('type'=>'int','value'=>'null'), 
        'libel'=> array('type'=>'string','value'=>'null'), 
        'description'=> array('type'=>'istring','value'=>'null')      
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
    $sqlRequest =   'INSERT INTO type_soins (id_categ_soins,id_type_soins,libel,description)
                    VALUES ('.$t['id_categ_soins']['value'].','.$t['id_type_soins']['value'].','.$t['libel']['value'].','.$t['description']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});

$app->post('/visite', function (Request $request, Response $response, array $args) {

    $params = $request->getParsedBody();
    $t = array(
        'id'=> array('type'=>'int','value'=>'null'), 
        'patient'=> array('type'=>'int','value'=>'null'), 
        'infirmiere'=> array('type'=>'int','value'=>'null'), 
        'date_prevue'=> array('type'=>'string','value'=>'null'),
        'date_reelle'=> array('type'=>'string','value'=>'null'),
        'duree'=> array('type'=>'int','value'=>'null'),   
        'compte_rendu_infirmiere'=> array('type'=>'string','value'=>'null'),
        'compte_rendu_patient'=> array('type'=>'string','value'=>'null')
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
    $sqlRequest =   'INSERT INTO visite (id, patient, infirmiere, date_prevue, date_reelle, duree, compte_rendu_infirmiere, compte_rendu_patient)
                    VALUES ('.$t['id']['value'].','.$t['patient']['value'].','.$t['date_prevu']['value'].','.$t['date_reelle']['value'].','.$t['duree']['value'].','.$t['compte_rendu_infirmiere']['value'].','.$t['compte_rendu_patient']['value'].')';

    $vretour = ($this->execRequete)($sqlRequest,$this->db);
    $vretour = json_encode($vretour);
    return $vretour;
    
});
*/
//DELETE COLONNE VIA ID
$app->delete('[/deletepersonne/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest ='DELETE FROM personne WHERE id = '.$args['id'].';';
    if($this->db->query($sqlRequest))
        $vretour = true;
    else
        $vretour = false;
    echo $sqlRequest;
    return $vretour;
});


//UPDATE A FAIRE -> VERIFICATION TYPE
$app->put('[/personnes/{id:\d*}]', function (Request $request, Response $response, array $args) {
    $sqlRequest = 'UPDATE personne SET';
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
    if($this->db->query($sqlRequest))
        $vretour = true;
    else
        $vretour = false;
    return $vretour;
});


