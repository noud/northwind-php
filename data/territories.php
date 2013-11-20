<?php

    //$link = mysql_pconnect("localhost", "root", "root") or die("Could not connect");
    $link = mysql_pconnect("localhost", "northwind", "northwind") or die("Could not connect");

    mysql_select_db("northwind") or die("Could not select database");

    $arr = array();

    // DISCLAIMER: It is better to use PHP prepared statements to communicate with the database.
    //             this provides better protection against SQL injection.
    //             [http://php.net/manual/en/pdo.prepared-statements.php][4]

    // get the parameters from the get. escape them to protect against sql injection.
    $employeeId = mysql_real_escape_string($_REQUEST["EmployeeID"]);

    $rs = mysql_query("SELECT t.TerritoryID, TRIM(t.TerritoryDescription) AS TerritoryDescription
                       FROM EmployeeTerritories et
                       INNER JOIN Territories t ON et.TerritoryID = t.TerritoryID
                       WHERE et.EmployeeID != " .$employeeId
                       . " ORDER BY t.TerritoryDescription ASC");

    while($obj = mysql_fetch_object($rs)) {

        $arr[] = $obj;

    }


    header("Content-type: application/json");

    echo "{\"data\":" .json_encode($arr). "}";

?>
