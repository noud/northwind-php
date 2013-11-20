<?php
	$link = mysql_pconnect("localhost", "northwind", "northwind") or die("Unable To Connect To Database Server");
	mysql_select_db("northwind") or die("Unable To Connect To Northwind");

	// add the header line to specify that the content type is JSON
	header("Content-type: application/json");

	// determine the request type
	$verb = $_SERVER["REQUEST_METHOD"];

	// handle a GET 
	if ($verb == "GET") {
		// DISCLAIMER: It is better to use prepared statements in PHP.
		// This provides protection against sql injection.
		// [http://php.net/manual/en/pdo.prepared-statements.php][26]
		// get the employee id off the request. escape it to protect against sql injection
		$employeeID = mysql_real_escape_string($_REQUEST["filter"]["filters"][0]["value"]);

		$arr = array();
		$rs = mysql_query("SELECT CONCAT(et.EmployeeID, et.TerritoryID) AS EmployeeTerritoryID, t.TerritoryID, e.EmployeeID,
			TRIM(t.TerritoryDescription) AS TerritoryDescription
			FROM Territories t
			INNER JOIN EmployeeTerritories et ON t.TerritoryID = et.TerritoryID
			INNER JOIN Employees e ON et.EmployeeID = e.EmployeeID
			WHERE e.EmployeeID = " .$employeeID);

		while($obj = mysql_fetch_object($rs)) {
			$arr[] = $obj;
		}

		echo "{\"data\":" .json_encode($arr). "}";
	}

	if ($verb == "PUT") {
		$request_vars = Array();
		parse_str(file_get_contents('php://input'), $request_vars );

		// DISCLAIMER: It is better to use PHP prepared statements to communicate with the database.
		//             this provides better protection against SQL injection.
		//             [http://php.net/manual/en/pdo.prepared-statements.php][4]

		// get the parameters from the get. escape them to protect against sql injection.

		$territoryId = mysql_real_escape_string($request_vars["TerritoryID"]);
	
		$employeeID = mysql_real_escape_string($request_vars["EmployeeID"]);

		$sql = "INSERT INTO EmployeeTerritories (EmployeeID, TerritoryID) VALUES (" .$employeeID ."," .$territoryId .")";

		$rs = mysql_query($sql);

		if ($rs) {
			echo true;
		}
		else {
			header("HTTP/1.1 500 Internal Server Error");
			echo false;
		}
	}
?>
