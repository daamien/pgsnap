<?php

function tr($namespace = '') {
  global $odd;

  $odd = !$odd;

  if (strlen($namespace) > 0) {
    if (!strcmp($namespace, 'information_schema')
      || !strcmp($namespace, 'pg_catalog')
      || !strcmp(substr($namespace, 0, 8), 'pg_toast')) {
      $class = 'sys';
    } else {
      $class = 'usr';
    }
  }
  else $class = '';
  if ($odd) {
    if (strlen($class) > 0) {
      $class .= '_';
    }
    $class .= 'odd';
  }

  $tr = '<tr class="'.$class.'">';

  return $tr;
}

function add_sys_and_user_checkboxes() {
  global $buffer;

  $buffer .= '<label><input id ="showusrobjects" type="checkbox" checked>Show User Objects</label>
<label><input id ="showsysobjects" type="checkbox" checked>Show System Objects</label>';

}

function pretty_size($bytes, $rounded = false) {

  if ($bytes <= 10240) {
    return "$bytes bytes";
  }

  $units = array('kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB', 'ZB');
  foreach($units as $index => $unit) {
    if ($bytes <= pow(1024,$index)) {
      $bytes /= (pow(1024,$index-1));
      return $rounded ?
        sprintf ('%d %s', $bytes, $units[$index-2]) :
        sprintf ('%.2f %s', $bytes, $units[$index-2]);
    }
  }

  return $bytes;
}


/**
 * Create a Bootstrap modal window
 *
 * @param string $query the message to display
 * 
 * @return string the modal
 */
 
function bootstrap_query_modal($query){

   $r ='';
   $r.='<div class="affix" style="top:250 px;">';
   $r.='<a href="#myModal" role="button" class="btn" data-toggle="modal">Show me the Query !</a>';
   $r.='</div>';
   $r.='<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
   $r.='<div class="modal-header">';
   $r.='<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
   $r.='<h3 id="myModalLabel">System Query</h3>';
   $r.='</div>';
   $r.='<div class="modal-body">';
   $r.='<p>'.$query.'</p>';
   $r.='</div>';
   $r.='<div class="modal-footer">';
   $r.='<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>';
   $r.='</div>';
   $r.='</div>';

	
   return $r;
}

?>
