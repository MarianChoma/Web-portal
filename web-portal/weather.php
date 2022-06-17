<?php
error_reporting(0);
require_once "MyPdo.php";
session_start();
if ((isset($endOfDay) && $endOfDay - $_SESSION['LAST_ACTIVITY'] <= 0)) {
    session_unset();
    session_destroy();
    header("Refresh:0");
}
$myPdo = new MyPDO();
if (isset($_SESSION['LAST_ACTIVITY'])) {

    if (isset($_POST["address"])) {
        if (!isset($_SESSION['writeToDatabase'])) {
            $_SESSION['writeToDatabase'] = true;
        }
        $_SESSION['searchQuery'] = $_POST['address'];
    }
}

/**
 * Position Stack
 */
if (isset($_SESSION['searchQuery']) && isset($_SESSION['LAST_ACTIVITY'])) {


    $searchQuery = $_SESSION['searchQuery'];


    $buildQuery = http_build_query([
        'access_key' => '9875b2e915aa52c503f195ffef618298',
        'query' => $searchQuery
    ]);

    $ch = curl_init(sprintf('%s?%s', 'http://api.positionstack.com/v1/forward', $buildQuery . "&country_module=1"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true, JSON_PRETTY_PRINT);

    $imageSrc = 'https://www.geonames.org/flags/x/' . strtolower($result["data"][0]["country_module"]["global"]["alpha2"]) . '.gif';

    /**
     * Open weather
     */

    $url = "http://api.openweathermap.org/data/2.5/weather?lat=" . $result["data"][0]["latitude"] . "&lon=" . $result["data"][0]["longitude"] . "&lang=sk&APPID=b2af723661d1ed3ff5d3491daa42faf6&units=metric";
    $json = file_get_contents($url);
    $data = json_decode($json, true, JSON_PRETTY_PRINT);
    $latitude = $result["data"][0]["latitude"];
    $longitude = $result["data"][0]["longitude"];
    $country = $result["data"][0]["country"];
    $city = $result["data"][0]["locality"];
    $date = date('Y-m-d H:i:s', $data["dt"] + $data["timezone"]);

    $beginOfDay = strtotime("today", time());
    $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;
    if ($_SESSION["writeToDatabase"] === true) {
        $_SESSION["writeToDatabase"] = false;
        $myPdo->run("INSERT into logins
            (`lat`, `lon`, `time`, `country`, `flag`, `city`) values (?, ?, ?, ?, ?, ?)",
            [$latitude, $longitude, $date, $country, $imageSrc, $city]);
    }

    $allLogins = $myPdo->run("SELECT * FROM logins
                                    WHERE id IN
                                    (SELECT MIN(id) FROM logins GROUP BY country)")->fetchAll();
    $countUsers = $myPdo->run("SELECT COUNT(id) FROM logins GROUP BY country")->fetchAll();


}

?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Počasie</title>
</head>
<body class="weather-body">
<header>
    <h1>Info</h1>
</header>
<div class="container">


    <?php
    if (isset($result)) {
    ?>
    <table class="table info-table">
        <tbody>
        <tr class="table-warning">
            <td><b>Názov Štátu</b></td>
            <?php

            echo "<td>$country</td>";
            ?>
        </tr>
        <tr class="table-warning">
            <td><b>Hlavné mesto</b></td>
            <?php
            $capitalCity = $result["data"][0]["country_module"]["capital"];
            echo "<td>$capitalCity</td>";
            ?>
        </tr>
        <tr class="table-warning">
            <td><b>Latitude</b></td>
            <?php
            echo "<td>$latitude</td>";
            ?>
        </tr>
        <tr class="table-warning">
            <td><b>Longitude</b></td>
            <?php
            echo "<td>$longitude</td>";
            ?>
        </tr>
        </tbody>
    </table>
        <table class="table weather-table">
            <tbody>
        <?php
        if (isset($data)) {
            $actualTemp = $data["main"]['temp'];
            $description = $data['weather'][0]['description'];
            $maxTemperature = $data["main"]["temp_max"];
            $minTemperature = $data["main"]["temp_min"];
            echo "<tr class='table-info'><td>Aktuálna teplota</td>";
            echo "<td> $actualTemp °C</td></tr>";
            echo "<tr class='table-info'><td>Maximálna teplota</td>";
            echo "<td> $maxTemperature °C</td></tr>";
            echo "<tr class='table-info'><td>Minimálna teplota</td>";
            echo "<td> $minTemperature °C</td></tr>";
            echo "<tr class='table-info'><td>Popis</td>";
            echo "<td>$description";
            echo '<img src="http://openweathermap.org/img/w/' . $data['weather'][0]['icon'] . '.png"
                    class="weather-icon" /></td></tr>';
        }
        }
        ?>
            </tbody>
        </table>

    <?php
    if (isset($allLogins)) {
        ?>
        <table class="table table-dark table-hover">
            <thead>
            <tr>
                <th class='text-center'>Štát</th>
                <th class='text-center'>Vlajka</th>
                <th class='text-center'>Počet návštevníkov</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 0;
            foreach ($allLogins as $login) {
                echo "<tr class='countryRow'>";
                $imageSrc = $login['flag'];
                echo "<td class='text-center'>" . $login["country"] . "</td>";
                echo "<td class='text-center'><img src=$imageSrc alt='flag' width='120' height='80'></td>";
                echo "<td class='text-center'>" . $countUsers[$index]["COUNT(id)"] . "</td>";
                echo "</tr>";
                $index++;
            }
            ?>
            </tbody>
        </table>
        <?php
    }
    ?>
</div>
<script src="main.js"></script>
</body>
</html>
