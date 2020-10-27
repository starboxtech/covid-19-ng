<?php

require('config.php');

//Create connection
function db_connect() {
    $covid_19_ng_conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$covid_19_ng_conn) {
        include(SHARED_PATH . '/head.php');
        echo '<body class="d-flex justify-content-center align-items-center flex-column"><div class="alert alert-danger p-3" role="alert">';
        echo '<h4 class="alert-heading">'.'Error: Unable to connect to MySQL.' . PHP_EOL . '</h4>';
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL . '<br>';
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL . '<br>';
        echo '</div></body>';
        exit;
    }
    mysqli_query($covid_19_ng_conn, "SET SESSION time_zone = '+01:00'");
    return $covid_19_ng_conn;
}

//Close connection
function db_disconnect() {
    if (isset($covid_19_ng_conn)) {
        mysqli_close($covid_19_ng_conn);
    }
}