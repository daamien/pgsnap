<?php

/*
 * Copyright (c) 2008-2013 Guillaume Lelarge <guillaume@lelarge.info>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

$buffer = $navigate_dbobjects.'
<div id="pgContentWrap">

<h1>Tables Without PKEY</h1>
';

if(!$g_withoutsysobjects) {
  add_sys_and_user_checkboxes();
}

$query = "SELECT
  pg_get_userbyid(relowner) AS owner,
  nspname,
  relname,";
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(pg_relation_size(pg_class.oid)) AS size';
} else {
  $query .= '
  relpages*8192 AS size';
}
  $query .= "
FROM pg_class, pg_namespace
WHERE
  relkind='r'
  AND relhaspkey IS false
  AND relnamespace = pg_namespace.oid";
if ($g_withoutsysobjects) {
  $query .= "
  AND nspname <> 'pg_catalog'
  AND nspname <> 'information_schema'
  AND nspname !~ '^pg_toast'";
}
$query .= "
ORDER BY relowner, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst">Table Owner</th>
  <th class="colMid">Schema Name</th>
  <th class="colMid">Table Name</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colLast" width="200">Size</th>';
} else {
  $buffer .= '
  <th class="colLast" width="200">Estimated Size</th>';
}
  $buffer .= '
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr($row['nspname'])."
  <td title=\"".$comments['roles'][$row['owner']]."\">".$row['owner']."</td>
  <td title=\"".$comments['schemas'][$row['nspname']]."\">".$row['nspname']."</td>
  <td title=\"".$comments['relations'][$row['nspname']][$row['relname']]."\">".$row['relname']."</td>";
if ($g_version > 80) {
$buffer .= "
  <td>".$row['size']."</td>";
} else {
$buffer .= "
  <td>".pretty_size($row['size'])."</td>";
}
$buffer .= "
</tr>";
}

$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/tableswithoutpkey.html';
include 'lib/fileoperations.php';

?>
