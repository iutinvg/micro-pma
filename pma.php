<?php
/*
 * Copyright (c) Whirix <info@whirix.com>
 * License: http://www.opensource.org/licenses/mit-license.html
 */

// setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'db-name');
define('DB_USER', 'db-user');
define('DB_PASS', 'db-pass');
// change it! will not work with default password
define('SCRIPT_PASS', 'change me!!!');

session_start();

// test

class PMA {

	// create one log row
	function query($query) {
		$link = PMA::__connect();
		if ($link == null) {
			return $link;
		}

		if (!isset($_SESSION['pma_history'])) {
			$_SESSION['pma_history'] = array();
		}

		array_unshift($_SESSION['pma_history'], $query);

		$res = @mysql_query($query);
		if (!$res) {
			echo "\nQuery failed: " . mysql_error();
			echo "\nQuery: " . $query;
		}

		return $res;
	}

	function select($query) {
		$result = PMA::query($query);

		if ($result == null) {
			return $result;
		}

		// Printing results in HTML
		echo "<table border=1>\n";
		$row = 0;
		while ($line = @mysql_fetch_array($result, MYSQL_ASSOC)) {

			if ($row == 0) {
				foreach (array_keys($line) as $col_name) {
					echo "\t\t<th>$col_name</th>\n";
				}
			}
			$color = (($row % 2) == 1) ? '#cccccc' : 'white';
			$row++;
			echo "\t<tr bgcolor='{$color}'>\n";
			foreach ($line as $col_value) {
				echo "\t\t<td>$col_value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";

		// Free resultset
		@mysql_free_result($result);
	}

	function __connect() {
		// Connecting, selecting database
		if (!($link = @mysql_connect(DB_HOST, DB_USER, DB_PASS))) {
			echo "\nCould not connect: " . mysql_error();
			return null;
		}
		if (!@mysql_select_db(DB_NAME)) {
			echo "\nCould not select database";
			return null;
		}

		return $link;
	}

	function backup_tables($host, $user, $pass, $name, $tables = '*') {

		$link = mysql_connect($host, $user, $pass);
		mysql_select_db($name, $link);

		//get all of the tables
		if ($tables == '*') {
			$tables = array();
			$result = mysql_query('SHOW TABLES');
			while ($row = mysql_fetch_row($result)) {
				$tables[] = $row[0];
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',', $tables);
		}

		//cycle through
		foreach ($tables as $table) {
			$result = mysql_query('SELECT * FROM ' . $table);
			$num_fields = mysql_num_fields($result);

			//$return.= 'DROP TABLE ' . $table . ';';
			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
			$return .= "\n\n" . $row2[1] . ";\n\n";

			for ($i = 0; $i < $num_fields; $i++) {
				while ($row = mysql_fetch_row($result)) {
					$return .= 'INSERT INTO ' . $table . ' VALUES(';
					for ($j = 0; $j < $num_fields; $j++) {
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n", "\\n", $row[$j]);
						if (isset($row[$j])) {
							$return .= '"' . $row[$j] . '"';
						} else {
							$return .= '""';
						}
						if ($j < ($num_fields - 1)) {
							$return .= ',';
						}
					}
					$return .= ");\n";
				}
			}
			$return .= "\n\n\n";
		}

		//save file
		$filename = 'dump-' . date('c') . '.sql';
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $return;
		exit();
	}

	function secure() {
		if (SCRIPT_PASS == 'change me!!!') {
			die("Do not use default password!");
		}
		if ($_REQUEST['p'] != SCRIPT_PASS) {
			die("Enter a correct password, bub!");
		}
		return TRUE;
	}

}

echo '<html><title>Micro PHP MySQL Admin</title><body>';

echo '<pre>';

// check secure
if (!empty($_REQUEST['q']) && !empty($_REQUEST['submitquery'])) {
	if (PMA::secure()) {
		$query = stripslashes($_REQUEST['q']);
		PMA::select($query);
	}
} else if (!empty($_REQUEST['tables']) && !empty($_REQUEST['submitdump'])) {
	if (PMA::secure()) {
		PMA::backup_tables(DB_HOST, DB_USER, DB_PASS, DB_NAME, $_REQUEST['tables']);
	}
}

echo '</pre></hr>';

echo "\n\n<form action='' method='post'>";

echo "\n\nPassword: <input type='password' value='{$_REQUEST['p']}' name='p'><br><br>";

echo "\n\n<input type='text' value='{$_REQUEST['tables']}' name='tables'><input type='submit' name='submitdump' value='Dump Table'> * for all tables<br><br>";

echo "\n\nQuery:<br><textarea cols='66' rows='10' name='q'>{$query}</textarea><br>";

echo "\n\n<input type='submit' name='submitquery' value='Submit Query'></form>";

if (count($_SESSION['pma_history'])) {
	echo '<h3>History:</h3><pre>';
	foreach ($_SESSION['pma_history'] as $hist) {
		echo "{$hist}\n";
	}
	echo '<pre>';
}

echo "\n\n</body></html>";
?>
