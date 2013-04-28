<?php
error_reporting('E_NONE');
header('Content-type: text/css');
ob_start();
require_once("../../../wp-config.php");
ob_clean();
include 'style.css';
global $xydac_cms_fields;
$style='';

if(isset($_GET['type'])&& ('admin'==$_GET['type'])){
	echo $xydac_cms_fields['adminstyle'];
	echo stripslashes_deep(apply_filters( 'xydac_cms_admin_style',$style));
}
else{
	echo $xydac_cms_fields['sitestyle'];
	echo stripslashes_deep(apply_filters( 'xydac_cms_site_style',$style));
}
exit;


?>