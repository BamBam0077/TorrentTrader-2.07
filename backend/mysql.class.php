<?php
//
//  TorrentTrader v2.x
//  MySQL wrapper
//  Author: TorrentialStorm
//
//    $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//
//    http://www.torrenttrader.org
//
//

function &SQL_Query ($query) {
	return new SQL_Query ($query);
}

class SQL_Query {
	private $query = "";
	private $params = array();

	function &__construct ($query) {
		$this->query = $query;
		return $this;
	}

	function &p ($param) {
		if (is_numeric($param)) {
			$this->params[] = $param;
		} elseif (is_array($param)) {
			$this->params[] = implode(", ", array_map(array(&$this, "escape"), $param));
		} else {
			$this->params[] = $this->escape($param);
		}
		return $this;
	}

	function &p_name ($param) {
		$this->params[] = "`".mysql_real_escape_string($param)."`";
		return $this;
	}

	function escape ($s) {
		if (is_numeric($s))
			return $s;
		return "'".mysql_real_escape_string($s)."'";
	}

	function read () {
		$ret = "";
		for ($i = 0; $i < strlen($this->query); $i++) {
			if ($this->query[$i] == "?") {
				list(, $val) = each($this->params);
				$ret .= $val;
			} else {
				$ret .= $this->query[$i];
			}
		}
		reset($this->params);
		return $ret;
	}

	function &execute () {
		$query = $this->read();
		$res = mysql_query($query);

		if ($res || mysql_errno() == 1062) {
			return $res;
		}
		$mysql_error = mysql_error();
		$mysql_errno = mysql_errno();

		// If debug_backtrace() is available, we can find exactly where the query was called from
		if (function_exists("debug_backtrace")) {
			$bt = debug_backtrace();

			if ($bt[1]["function"] == "mysql_query_cached" || $bt[1]["function"] == "get_row_count_cached" || $bt[1]["function"] == "get_row_count")
				$i = 1;
			else
				$i = 0;

			$line = $bt[$i]["line"];
			$file = str_replace(getcwd().DIRECTORY_SEPARATOR, "", $bt[$i]["file"]);
			$msg = "Database Error in $file on line $line: $mysql_error. Query was: $query.";
		} else {
			$file = str_replace(getcwd().DIRECTORY_SEPARATOR, "", $_SERVER["SCRIPT_FILENAME"]);
			$msg = "Database Error in $file: $mysql_error. Query was: $query";
		}

		error_log($msg);
		show_error_msg("Database Error", "Database Error. Please report this to an administrator.");

	}
}

/* Example Usage:

// Note: Any values passed to p() or p_name() MUST NOT be escaped, this will be done internally.
// for p() arrays are also taken and imploded with , for use in IN(...)
// p_name() is for field/table names
// p() is for where conditions, insert/update values, etc...

$ids = range(1, 10);
//$res = SQL_Query("SELECT `id`, `username` FROM `users` WHERE ? IN (?) ORDER BY ? ASC")->p_name("id")->p($ids)->p_name("id")->execute();

$q = SQL_Query("SELECT `id`, `username` FROM `users` WHERE ? IN (?) ORDER BY ? ASC")->p_name("id")->p($ids)->p_name("id");

echo "Query: ".$q->read()."\n";
$res = $q->execute();

while ($row = mysql_fetch_array($res)) {
	echo "$row[id] - $row[username]\n";
}

// Trigger a SQL error to test logging
SQL_Query("SELECT")->execute();
*/
?>