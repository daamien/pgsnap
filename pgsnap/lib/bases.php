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


$query = 'SELECT datname,
  pg_get_userbyid(datdba) AS dba,
  pg_catalog.pg_encoding_to_char(encoding) AS encoding,';
if ($g_version > 83) {
  $query .= '
  datcollate,
  datctype,';
}
$query .= '
  datistemplate,
  datallowconn,';
if ($g_version > 80) {
  $query .= '
  datconnlimit,';
}
$query .= '
  datlastsysoid,
  datfrozenxid,';
if ($g_version > 74) {
  $query .= '
  spcname as tablespace,';
}
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(pg_database_size(datname)) AS size,';
}
if ($g_version < 90) {
  $query .= '
    datconfig,';
}
$query .= '
  datacl';
if ($g_version > 81) {
  $query .= ',
  age(datfrozenxid) AS freezeage, ROUND(100*(age(datfrozenxid)/freez::float)) AS perc';
}
$query .= '
FROM pg_database';
if ($g_version > 74) {
  $query .= ', pg_tablespace';
}
if ($g_version > 81) {
  $query .= "
JOIN (SELECT setting AS freez FROM pg_settings
      WHERE name = 'autovacuum_freeze_max_age') AS param
      ON (true)";
}
if ($g_version > 74) {
  $query .= '
WHERE dattablespace = pg_tablespace.oid';
}
$query .= '
ORDER BY datname';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}


// Menu
$buffer = $navigate_globalobjects;

// button : Show me the Query !
$buffer .= bootstrap_query_modal($query);

// close the row 
$buffer .= '</div> <!-- /row -->';

// content
$buffer .= '
<div class="span9">
<h1>Databases</h1>
';



$buffer .= '
<table class="table table-striped table-bordered table-hover">
<thead>
<tr>
  <th>DB Owner</th>
  <th>DB Name</th>
  <th>Encoding</th>';
if ($g_version > 83) {
  $buffer .= '
  <th>Collation</th>
  <th>CType</th>';
}
$buffer .= '
  <th>Template?</th>
  <th>Allow connections?</th>';
if ($g_version > 80) {
  $buffer .= '
  <th>Connection limits</th>';
}
$buffer .= '
  <th>Last system OID</th>
  <th>Frozen XID</th>';
if ($g_version > 74) {
  $buffer .= '
  <th>Tablespace name</th>';
}
if ($g_version > 80) {
  $buffer .= '
  <th>Size</th>';
}
if ($g_version > 81) {
  $buffer .= '
  <th>Auto Freeze</th>';
}
if ($g_version < 90) {
  $buffer .= '
  <th>Configuration</th>';
}
$buffer .= '
  <th><acronym title="Access Control List">ACL</acronym></th>
</tr>
</thead>
<tbody>';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td title="'.$comments['roles'][$row['dba']].'">'.$row['dba'].'</td>
  <td title="'.$comments['databases'][$row['datname']].'">'.$row['datname'].'</td>
  <td>'.$row['encoding'].'</td>';
if ($g_version > 83) {
  $buffer .= '
  <td>'.$row['datcollate'].'</td>
  <td>'.$row['datctype'].'</td>';
}

$icon=($row['datistemplate'] ? 'icon-ok' : 'icon-remove');
$buffer .= '  <td><i class="'.$icon.'"></i></td>';

$icon=($row['datallowconn'] ? 'icon-ok' : 'icon-remove');
$buffer .= '  <td><i class="'.$icon.'"></i></td>';

if ($g_version > 80) {
  $buffer .= '
  <td>'.$row['datconnlimit'].'</td>';
}
$buffer .= '
  <td>'.$row['datlastsysoid'].'</td>
  <td>'.$row['datfrozenxid'].'</td>';
if ($g_version > 74) {
  $buffer .= '
  <td title="'.$comments['tablespaces'][$row['tablespace']].'">'.$row['tablespace'].'</td>';
}
if ($g_version > 80) {
  $buffer .= '
  <td>'.$row['size'].'</td>';
}
if ($g_version > 81) {
  $buffer .= '
  <td>'.$row['freezeage'].' ('.$row['perc'].' %)</td>';
}
if ($g_version < 90) {
  $buffer .= '
  <td>'.$row['datconfig'].'</td>';
}
$buffer .= '
  <td><acronym X=\"Access Control List\">'.$row['datacl'].'</acronym></td>
</tr>';
}
$buffer .= '</tbody>
</table>
</div>
';


$filename = $outputdir.'/bases.html';
include 'lib/fileoperations.php';

?>
