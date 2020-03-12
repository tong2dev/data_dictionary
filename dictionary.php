<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Data Dictionary <?php echo $project; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid">
<?php
$stmt = $pdo->prepare("select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE'");
$stmt->execute();
$res = $stmt->fetchAll();

foreach ($res as $k => $v) {

    $stmt = $pdo->prepare("SELECT objsubid,description FROM pg_description WHERE objoid = '".$v[0]."'::regclass order by objsubid asc;");
    $stmt->execute();
    $res_des = $stmt->fetchAll();
    foreach ($res_des as $n => $m) {
        $arr_des[$m['objsubid']] = $m['description'];
    }

    echo '<div class="table-responsive-md"><table class="table table-striped table-bordered table-sm">
    <thead class="thead-dark">
    <tr>
        <th scope="col">Table Name</th>
        <th scope="col" colspan="5">'.$v[0].'</th>
    </tr>
    <tr>
        <th scope="col">Description</th>
        <th scope="col" colspan="5">'.$arr_des[0].'</th>
    </tr>
    <tr>
        <th scope="col">Field Name</th>
        <th scope="col">Field Type</th>
        <th scope="col">CONSTRAINT</th>
        <th scope="col">Not Null</th>
        <th scope="col">Default</th>
        <th scope="col">Remark</th>
    </tr>
    </thead><tbody>';

    $stmt = $pdo->prepare("select column_name,column_default,is_nullable,data_type,character_maximum_length,numeric_precision,numeric_scale,ordinal_position from information_schema.columns where table_name='".$v[0]."' order by ordinal_position asc;");
    $stmt->execute();
    $res_col = $stmt->fetchAll();
    foreach ($res_col as $z => $x) {
        echo "<tr>";
        echo "<td>".$x['column_name']."</td>";
        echo "<td>".check_type($x)."</td>";
        echo "<td>".check_pk($x)."</td>";
        echo "<td>".$x['is_nullable']."</td>";
        echo "<td>".check_default($x)."</td>";
        echo "<td>".$arr_des[$x['ordinal_position']]."</td>";
        echo "</tr>";
    }

    echo '</tbody></table></div><hr>';
}

function check_type($data){
    if($data['data_type']=='character varying'){
        return $data['data_type']."(".$data['character_maximum_length'].")";
    }else if($data['data_type']=='numeric'){
        return $data['data_type']."(".$data['numeric_precision'].",".$data['numeric_scale'].")";
    }else{
        return $data['data_type'];
    }
}

function check_pk($data){
    if (strpos($data['column_default'], 'nextval') !== false) {
        return 'PK';
    }else{
        return '';
    }
}

function check_default($data){
    if (strpos($data['column_default'], 'nextval') !== false) {
        return '';
    }else{
        return $data['column_default'];
    }
}
?>
</div>
</body>
</html>