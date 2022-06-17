<?php
require_once "MyPdo.php";
error_reporting(0);
if (isset($_COOKIE['country'])) {
    $country = $_COOKIE['country'];
    $myPdo = new MyPDO();
    $towns = $myPdo->run("SELECT * FROM logins
                                    WHERE id IN
                                    (SELECT MIN(id) FROM logins WHERE country=? GROUP BY city )", [$country])->fetchAll();
    $usersFromTowns = $myPdo->run("SELECT COUNT(id) FROM logins WHERE country=? GROUP BY city", [$country])->fetchAll();
    $cities = $myPdo->run("SELECT * FROM logins WHERE country=?", [$country])->fetchAll();
    $t6TO15 = $myPdo->run("SELECT COUNT(l.id) FROM logins l where l.country=? and cast(l.time as time)> cast('06:00:00' as time) and cast(l.time as time)< cast('15:00:00' as time)", [$country])->fetch();
    $t15TO21 = $myPdo->run("SELECT COUNT(l.id) FROM logins l where l.country=? and cast(l.time as time)> cast('15:00:00' as time) and cast(l.time as time)< cast('21:00:00' as time)", [$country])->fetch();
    $t21TO24 = $myPdo->run("SELECT COUNT(l.id) FROM logins l where l.country=? and cast(l.time as time)> cast('21:00:00' as time) and cast(l.time as time)< cast('24:00:00' as time)", [$country])->fetch();
    $t0TO6 = $myPdo->run("SELECT COUNT(l.id) FROM logins l where l.country=? and cast(l.time as time)> cast('00:00:00' as time) and cast(l.time as time)< cast('06:00:00' as time)", [$country])->fetch();
    $loginsCount = "";
    $coordinates = "";

    foreach ($cities as $city) {
        $coordinates .= ',' . $city['lon'] . ',' . $city['lat'];
    }
    setcookie("coordinates", $coordinates, time() + (86400 * 30), "/");
    setcookie("15TO21", $t15TO21["COUNT(l.id)"], time() + (86400 * 30), "/");
    setcookie("6TO15", $t6TO15["COUNT(l.id)"], time() + (86400 * 30), "/");
    setcookie("21TO24", $t21TO24["COUNT(l.id)"], time() + (86400 * 30), "/");
    setcookie("0TO6", $t0TO6["COUNT(l.id)"], time() + (86400 * 30), "/");

}
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
          integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
            integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
            crossorigin=""></script>
    <script
            src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Štatistika</title>
</head>
<body class="statistic-body">
<div class="container">
    <?php
    if (isset($country)) {
        echo "<header><h1>" . $country . "</h1></header>";
        ?>
        <table class="table table-dark">
            <thead>
            <tr>
                <th>Mesto</th>
                <th>Počet návštevníkov</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 0;
            foreach ($towns as $town) {
                echo "<tr>";
                echo "<td>" . $town["city"] . "</td>";
                echo "<td>" . $usersFromTowns[$index]["COUNT(id)"] . "</td>";
                echo "</tr>";
                $index++;
            }
            ?>
            </tbody>
        </table>
        <div id="map"></div>
        <canvas id="myChart"></canvas>
        <script src="main.js"></script>
        <?php
    }
    ?>
</div>
</body>
</html>
