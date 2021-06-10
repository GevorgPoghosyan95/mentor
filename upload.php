<?php
if (isset($_POST['submit'])) {
    $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv');
    //check if file is in current formats
    if (!in_array($_FILES['filename']['type'], $mimes)) {
        die("Sorry, document type not allowed");
    }


    $fullData = [];
    $handle = fopen($_FILES['filename']['tmp_name'], "r");
    $headers = fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //related to formats or reads header & data as one variable or as several, if one, is used in the explode, if not, then everything is fine
        if (count($headers) == 1) {
            $fullData[] = explode(',', $data[0]);
        } else {
            $fullData[] = $data;
        }

    }
    fclose($handle);

    $count = count($fullData);
    echo '<pre>';
    $pairArray = [];
    $sum = 0;
    for ($i = 0; $i < $count; $i++) {
        for ($j = 0; $j < $count; $j++) {

            //comparer each elements,except the same
            if ($i == $j) {
                continue;
            }

            //If both employees are in the same division: 30%
            if ($fullData[$i][2] == $fullData[$j][2]) {

                $sum += 30;
            }

            //If the age difference between employees is less than or equal to 5 years: 30%
            if (abs($fullData[$i][3] - $fullData[$j][3]) <= 5) {

                $sum += 30;
            }

            //If both employees are in the same timezone: 40%
            if ($fullData[$i][4] == $fullData[$j][4]) {
                $sum += 40;
            }

            //create pairs
            $pairArray[] = ['name1' => $fullData[$i][0], 'name2' => $fullData[$j][0], 'percents' => $sum];
            $sum = 0;
        }
    }

    //Sort a Multi-dimensional Array by Value
    usort($pairArray, function ($a, $b) {
        return $a['percents'] - $b['percents'];
    });

    $finalResult = [];
    $percents = [];

    for ($i = 0; $i < count($pairArray); $i++) {
        //exclude data where percentages are zero
        if ($pairArray[$i]['percents'] == 0) {
            continue;
        }

        for ($j = 0; $j < count($pairArray); $j++) {
            if ($i == $j) {
                continue;
            }
            //find similar pairs and equalize them like
            // Jacob Murray will be matched with Gabrielle Clarkson - 100%
            // Gabrielle Clarkson will be matched with Jacob Murray - 100%
            if ($pairArray[$i]['name1'] == $pairArray[$j]['name2'] && $pairArray[$i]['name2'] == $pairArray[$j]['name1'] && $pairArray[$i]['percents'] == $pairArray[$j]['percents']) {
                $name1 = $pairArray[$j]['name1'];
                $name2 = $pairArray[$j]['name2'];
                $pairArray[$j]['name2'] = $name1;
                $pairArray[$j]['name1'] = $name2;
            }
        }
        $finalResult[] = $pairArray[$i];
        $percents[] = $pairArray[$i]['percents'];
    }
    //delete duplicates for final result
    $finalResult = array_map("unserialize", array_unique(array_map("serialize", $finalResult)));

    //leave unique values and remove zeros
    $percents = array_filter(array_unique($percents));
    //count average
    $average = array_sum($percents) / count($percents);

    echo "In the case of $count employees the highest average match score is $average%<br>";

    foreach ($finalResult as $key => $value) {
        $name1 = $value['name1'];
        $name2 = $value['name2'];
        $percents = $value['percents'];
        echo " <b>$name1</b> will be matched with <b>$name2</b> - <b>$percents%</b><br>";
    }

    echo "<br>Note: There are rumors that the company has plans to update its matching requirements.";
}

?>
