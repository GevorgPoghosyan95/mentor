
<?php
if (isset($_POST['submit']))
{
    $fullData = [];
    $handle = fopen($_FILES['filename']['tmp_name'], "r");
    $headers = fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
        $fullData[] = $data;
    }
    fclose($handle);

    $count = count($fullData);
    echo '<pre>';
    $pairsArray = [];
    for($i = 0;$i<$count;$i++){
        for($j = 0;$j <$count;$j++){
            if($i == $j){
                continue;
            }
            if($fullData[$i][2] == $fullData[$j][2]){
                echo $fullData[$i][0].' and '.$fullData[$j][0].' have the same job '.$fullData[$j][2].'<br>';
            }

        }
    }
}

?>
