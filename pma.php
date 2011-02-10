<?php
/* 
 * Copyright (c) 2011 Whirix <info@whirix.com>
 * License: http://www.opensource.org/licenses/mit-license.html
 */

// setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'db-name');
define('DB_USER', 'db-user');
define('DB_PASS', 'db-pass');
define('SCRIPT_PASS', 'passw0rd');

session_start();

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

}

echo '<html><title>Small MySQL client</title><body>';

echo '<pre>';

// check secure
if (isset($_REQUEST['q']) && $_REQUEST['p'] != SCRIPT_PASS) {
    echo "Enter a correct password, bub!";
}

$query = isset($_REQUEST['q']) ? stripslashes($_REQUEST['q']) : '';
$pass = isset($_REQUEST['p']) ? $_REQUEST['p'] : '';

if (isset($_REQUEST['q'])) {
    PMA::select($query);
}

echo '</pre></hr>';

echo "\n\n<form action='pma.php' method='post'>";

echo "\n\nPassword: <input type='password' value='{$pass}' name='p'><br>";

echo "\n\nQuery:<br><textarea cols='66' rows='10' name='q'>{$query}</textarea><br>";

echo "\n\n<input type='submit'></form>";

if (count($_SESSION['pma_history'])) {
    echo '<h3>History:</h3><pre>';
    foreach ($_SESSION['pma_history'] as $hist) {
        echo "{$hist}\n";
    }
    echo '<pre>';
}

echo "\n\n</body></html>";
?>
