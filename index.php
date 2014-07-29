<?php

if($_POST){
  $files = explode("\n", $_POST['files']);
  foreach($files as $file){
    $compo =  parse_url($file);
    $pathinfo = pathinfo($compo['path']);
    chdir($_SERVER['DOCUMENT_ROOT'].$pathinfo['dirname']);
    $filename = $pathinfo['basename'];
    if($pathinfo['extension']=='png'){
      $api_key = $_POST['api_key'];
      $json = json_decode(exec("curl -i --user api:$api_key --data-binary @$filename https://api.tinypng.com/shrink"));
      file_put_contents($filename.'tmp', file_get_contents($json->output->url));
      if(filesize($filename.'tmp')>0){
          rename($filename, $filename.'_bak');//make backup file
          rename($filename.'tmp', $filename);
      }
    }
    elseif($pathinfo['extension']=='jpg'){
      copy($filename, $filename.'_bak');
      exec("convert -strip -quality 65% $filename $filename");
    }
    $message = "$filename has been resized to ".filesize($filename);
  }
}

?>
<html>
<head>
<title>Shrink PNG file size</title>
</head>
<body>
<form action="" method="post" >
<label for="api_key">TinyPNG API Key</label><br/>
<input type="text" name="api_key" value="" size="36"/><br/>
<label for="files">Image URL</label><br/>
<textarea rows="10" name="files" cols="80">
</textarea>
<br/>
<?php echo $message;?>
<br/>
<input type="submit" name="submit" value="Submit"/>
</form>
</body>
</html>
