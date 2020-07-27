<?php
    declare(strict_types=1);

    include_once '../sys/core/init.inc.php';

    $db = new DatabaseConnection();
    $calendar = new Calendar($db, "2020-07-25 22:28:28");
    echo $calendar->buildCalendar();

?>