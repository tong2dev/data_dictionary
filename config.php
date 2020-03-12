<?php
$database = array(
    'DEMO'=>array(
        'host'=>'localhost',
        'port'=>'5432',
        'dbname'=>'demo_db',
        'user'=>'postgres',
        'pass'=>'postgres'
    )
);

$project = $_GET['project'];
$data = $database[$project];

if(gettype($data)=='array'){

try{
    $pdo = new PDO('pgsql:host='.$data['host'].';port='.$data['port'].';dbname='.$data['dbname'].'',$data['user'],$data['pass']);
}catch (PDOException $e){
    echo $e->getMessage();
}

}