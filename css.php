<?php
require_once("../../../wp-config.php");

header('Content-type: text/css');
include 'style.css';
$style='';
if(isset($_GET['type'])&& ('admin'==$_GET['type'])){
	echo stripslashes_deep(apply_filters( 'xydac_cms_admin_style',$style));
}
else
	echo stripslashes_deep(apply_filters( 'xydac_cms_site_style',$style));
exit;


?>