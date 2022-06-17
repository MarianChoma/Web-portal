<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['LAST_ACTIVITY'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
}
if ((isset($endOfDay) && $endOfDay - $_SESSION['LAST_ACTIVITY'] <= 0)) {
    echo 'skoncila session';
    session_unset();
    session_destroy();
    header("Refresh:0");
}
?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Zadanie 7</title>
</head>
<body class="input-body">
<div class="container">


    <form action="weather.php" id="myInput" method="post">
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Zadajte svoju adresu</span>
            <input type="text" class="form-control" name="address" id="address"
                   placeholder="565 5th Ave, New York, NY 10017, United States" aria-label="Username"
                   aria-describedby="basic-addon1">
        </div>
        <input type="submit" class="btn btn-light" value="enter">
    </form>
</div>
</body>
</html>
