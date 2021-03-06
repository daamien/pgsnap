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

$g_flashexists = file_exists('external/open-flash-chart.php')
              && file_exists('external/open_flash_chart_object.php')
              && file_exists('external/open-flash-chart.swf')
              && file_exists('external/swfobject.js');

if ($g_version > 80) {
  $query = "SELECT rolsuper AS superuser FROM pg_roles
WHERE rolname='".pg_escape_string($PGUSER)."'";
} else {
  $query = "SELECT usesuper AS superuser FROM pg_user
WHERE usename='".pg_escape_string($PGUSER)."'";
}

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$row = pg_fetch_array($rows);
$g_superuser = !strcmp($row['superuser'], 't');

$query = "SELECT 1 FROM pg_proc WHERE proname LIKE '%buffercache%'";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$g_pgbuffercache = $g_superuser && (pg_num_rows($rows) > 0);

$query = "SELECT 1 FROM pg_proc WHERE proname LIKE '%stattuple%'";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$g_pgstattuple = $g_superuser && pg_num_rows($rows) > 0;

$query = "SELECT 1 FROM pg_proc WHERE proname LIKE '%statindex%'";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$g_pgstatindex = $g_superuser && pg_num_rows($rows) > 0;

$query = "show pool_status";

$rows = @pg_query($connection, $query);
if ($rows)
  $g_pgpool = true;
else
  $g_pgpool = false;

$query = "SELECT 1 FROM pg_proc WHERE proname LIKE 'pg_freespacemap_relations'";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$g_fsmrelations = $g_superuser && pg_num_rows($rows) > 0;

$query = "SELECT 1 FROM pg_proc WHERE proname LIKE 'pg_freespacemap_pages'";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
$g_fsmpages = $g_superuser && pg_num_rows($rows) > 0;

$query = "SELECT name, setting FROM pg_settings";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
while ($row = pg_fetch_array($rows)) {
  $g_settings[$row['name']] = $row['setting'];
}

?>
