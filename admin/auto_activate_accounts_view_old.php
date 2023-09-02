<?php 
// make db connection
// details provided by Union sysadmin (Alistair Cott at present)
define("DB_SRVR", "icsqln.cc.ic.ac.uk");
define("DB_USR", "su_badmintonlink");
define("DB_PWD", "jekl6yb54nhy");
define("DB_NME", "SU_NEWERPOL");

$connection = mssql_connect(DB_SRVR, DB_USR, DB_PWD) or die(mssql_error());
mssql_select_db(DB_NME, $connection) or die(mssql_error());

// $q = "SELECT * FROM BadmintonMembers ORDER BY JoinDate";
// $r = mssql_query($q);

// $numRows = mssql_num_rows($r);
// echo "<h2>" . $numRows . " member" . ($numRows == 1 ? "" : "s") . " displayed </h2>"; // funky If statement!
// echo "<i>NB: \"Join Date\" is when the Union recognises payment [generally done daily], not when the user actually pays.</i>";
// echo "<br/>";
// echo "<i>Consequently this page can be up to 24hrs out of date, and longer on a weekend.</i>";
// echo "<br/>";
// echo "<br/>";
// echo "<table border=\"1\">";
// echo "<tr> <th>Username</th> <th>Name</th> <th>Join Date</th> </tr>";

// while($slot = mssql_fetch_array($r)){
// 	$name = ldap_get_names($slot[0]);
// 	echo "<tr> <td>$slot[0]</td> <td>$name[0] $name[1]</td> <td>$slot[1]</td> </tr>";
// }

// echo "</table>";
// echo "<br/>";
// echo "<br/>";

// mssql_close($connection); // is needed?