<?php
error_reporting(0);
require_once("../../../wp-config.php");
header('Content-type: text/javascript');
$script='; ';
include 'script.js';

if(isset($_GET['type'])&& ('admin'==$_GET['type'])){
	echo apply_filters( 'xydac_cms_admin_script',$script);
}
else{
	echo stripslashes_deep(apply_filters( 'xydac_cms_site_script',$script));
}
exit;
?>