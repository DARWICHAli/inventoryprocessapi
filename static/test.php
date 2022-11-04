<?php
  $json = '{"foo-bar": 12345}';

  $data = json_decode($json);
  echo $data->{"foo-bar"} . "\n";
?>