<?php
if ( !defined( 'XYDAC_CMS_EXPORT_PATH' ) )define('XYDAC_CMS_EXPORT_PATH',get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php");
if ( !defined( 'XYDAC_CMS_EXPORT_CACHE' ) )define('XYDAC_CMS_EXPORT_CACHE',"xydac_cms_export_cache");
//Supporting Functions
function xydac_export($cptname)
{
	$final = array();
	if(!is_array($cptname))
		return;
	//$cptname['post_type']
	//$cptname['page_type']
	//$cptname['taxonomy']

	$final['xydac_post_type'] =array();
	$final['xydac_page_type'] = array();
	$final['xydac_archive'] = array();
	$final['xydac_taxonomy'] = array();
	$package = get_option(XYDAC_CMS_EXPORT_CACHE);
	$pack_det = "";
	if(is_array($package))
	{
		$pack_det.="<package_name>".$package['package_name']."</package_name>";
		$pack_det.="<developer_name>".$package['developer_name']."</developer_name>";
		$pack_det.="<website>".$package['website']."</website>";
		$pack_det.="<description>".$package['description']."</description>";
		$pack_det.="<email>".$package['email']."</email>";
		update_option(XYDAC_CMS_EXPORT_CACHE,"");
	}
	//--START posttype
	$cpts = get_option(XYDAC_CMS_POST_TYPE_OPTION);
	if(isset($cptname['post_type']))
		if(is_array($cpts))
		foreach($cpts as $k=>$cpt)
		if(in_array($cpt['name'],$cptname['post_type']))
		array_push($final['xydac_post_type'],$cpts[$k]);
	if(!empty($final['xydac_post_type']))
		foreach($final['xydac_post_type'] as $k=>$cpt)
		{
			$cpt = $cpt['name'];
			$final['xydac_post_type'][$k]['fields']=getCptFields($cpt);
		}
		//--END posttype

		//--START pagetype
		$cpts = get_option(XYDAC_CMS_PAGE_TYPE_OPTION);
		if(is_array($cpts))
			foreach($cpts as $k=>$cpt)
			if(in_array($cpt['name'],$cptname['page_type']))
			array_push($final['xydac_page_type'],$cpts[$k]);
		if(!empty($final['xydac_page_type']))
			foreach($final['xydac_page_type'] as $k=>$cpt)
			{
				$cpt = $cpt['name'];
				$final['xydac_page_type'][$k]['fields']=get_page_type_fields($cpt);
			}
			//--END pagetype
			//--START archive
			$cpts = get_option(XYDAC_CMS_ARCHIVE_OPTION);
			if(is_array($cpts))
				foreach($cpts as $k=>$cpt)
				if(in_array($cpt['name'],$cptname['archive']))
				array_push($final['xydac_archive'],$cpts[$k]);

			//return maybe_serialize($final);
			return "<?xml version=\"1.0\"?>\n<xydac>".$pack_det."\n\t".xydac_arr_xml($final)."\n</xydac>";

}


function xydac_arr_xml($array, $num_prefix = "index")
{
	if(!is_array($array)) // text
	{
		return $array;
	}
	else
	{
		foreach($array as $key=>$val) // subnode
		{
			$key = (is_numeric($key)? $num_prefix : $key);
			if(($key=='content_html') || ($key=='query') || ($key=='beforeloop') || ($key=='customhtml') ||($key=='afterloop') )
				$return.="<".$key."><![CDATA[".xydac_arr_xml($val, $num_prefix)."]]></".$key.">\n";
			else
				$return.="<".$key.">".xydac_arr_xml($val, $num_prefix)."</".$key.">\n";
		}
	}

	return $return;
}

class xydac_export_manager
{
	function __construct()
	{
		if(isset($_POST['xydac_export_form']))
			if(isset($_POST['package_name']) && isset($_POST['cbval']))
			{
				$str = "?";
				if(is_array($_POST['cbval']['xydac_post_type']))
				{
					$str.= "cpt_name=";
					foreach($_POST['cbval']['xydac_post_type'] as $val)
						$str.=$val.",";
					$str = substr($str,0,-1);
					$str.= "&";
				}
				if(is_array($_POST['cbval']['xydac_page_type']))
				{
					$str.="page_type_name=";
					foreach($_POST['cbval']['xydac_page_type'] as $val)
						$str.=$val.",";
					$str = substr($str,0,-1);
					$str.= "&";
				}
					
				if(is_array($_POST['cbval']['xydac_taxonomy']))
				{
					$str.="taxonomy_name=";
					foreach($_POST['cbval']['xydac_taxonomy'] as $val)
						$str.=$val.",";
					$str = substr($str,0,-1);
					$str.= "&";
				}
				if(is_array($_POST['cbval']['xydac_archive']))
				{
					$str.="archive_name=";
					foreach($_POST['cbval']['xydac_archive'] as $val)
						$str.=$val.",";
					$str = substr($str,0,-1);
					$str.= "&";
				}
				$postdata = array();
				$postdata['package_name'] = esc_attr($_POST['package_name']);
				$postdata['developer_name'] = esc_attr($_POST['developer_name']);
				$postdata['website'] = esc_url($_POST['website']);
				$postdata['description'] = esc_attr($_POST['description']);
				$postdata['email'] = esc_attr($_POST['email']);
				update_option(XYDAC_CMS_EXPORT_CACHE,$postdata);
				echo "<div id='message' class='updated'><p><a href='".XYDAC_CMS_EXPORT_PATH.$str."'>".__('Your Export File is Ready. Click Here to Download The Export File',XYDAC_CMS_NAME)."</a></p></div>";
			}
			else
			{
				echo "<div id='error' class='error'><p>".__('Your Need To Select atleast one item and provide package name',XYDAC_CMS_NAME)."</p></div>";
			}
			$final = array();
			$data = get_xydac_archive_Name();
			if(is_array($data))
				foreach($data as $val)
				{
					$temp = array();
					$temp['arr']= 'xydac_archive';
					$temp['name']= $val;
					$temp['type']= 'Archive';
					array_push($final,$temp);
				}
				$data = get_reg_page_type_name();
				if(is_array($data))
					foreach($data as $val)
					{
						$temp = array();
						$temp['arr']= 'xydac_page_type';
						$temp['name']= $val;
						$temp['type']= 'Page Type';
						array_push($final,$temp);
					}
					$data = get_xydac_cptName();
					if(is_array($data))
						foreach($data as $val)
						{
							$temp = array();
							$temp['arr']= 'xydac_post_type';
							$temp['name']= $val;
							$temp['type']= 'Post Type';
							array_push($final,$temp);
						}

						$this->displayData($final);

	}
	function displayData($data)
	{
		$user = wp_get_current_user();
		?>
<form id="xydac_export_form"
	action="<?php echo XYDAC_MAIN_IMPORTEXPORT_PATH; ?>" method="post">
	<table class="widefat tag fixed" cellspacing="0">
		<thead class="content-types-list">
			<tr>
				<th class="manage-column column-cb check-column" id="cb" scope="col"><input
					type="checkbox"></th>
				<th class="manage-column column-name" scope="col"><?php _e('Name',XYDAC_CMS_NAME);?>
				</th>
				<th class="manage-column column-name" scope="col"><?php _e('Type',XYDAC_CMS_NAME);?>
				</th>

			</tr>
		</thead>
		<tbody id="the-list">
			<?php foreach($data as $val) { 
extract($val); ?>
			<tr id="content-type-">
				<th class="check-column" scope="row"><input type="checkbox"
					value="<?php echo $name; ?>" name="cbval[<?php echo $arr; ?>][]" />

				</th>
				<td class="name column-name"><strong><?php echo $name; ?> </strong>
					<br />
				</td>
				<td class="name column-name"><?php echo $type; ?>
				</td>

			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column" id="cb" scope="col"><input
					type="checkbox"></th>
				<th class="manage-column column-name" scope="col"><?php _e('Name',XYDAC_CMS_NAME);?>
				</th>
				<th class="manage-column column-name" scope="col"><?php _e('Type',XYDAC_CMS_NAME);?>
				</th>

			</tr>
		</tfoot>
	</table>
	<br />
	<h4>
		<?php _e('Please Input Extra Details about the Package',XYDAC_CMS_NAME);?>
	</h4>
	<table class="xydac_form_inline">
		<tr>
			<td class="xydac_title"><label for="package_name"><?php _e('Package Name',XYDAC_CMS_NAME);?>
			</label></td>
			<td class="xydac_detail"><input type="text" name="package_name"
				id="package_name" /></td>
		</tr>
		<tr>
			<td class="xydac_title"><label for="developer_name"><?php _e('Developer Name',XYDAC_CMS_NAME);?>
			</label></td>
			<td class="xydac_detail"><input type="text" name="developer_name"
				id="developer_name" value="<?php echo $user->user_nicename; ?>" /></td>
		</tr>
		<tr>
			<td class="xydac_title"><label for="website"><?php _e('Website',XYDAC_CMS_NAME);?>
			</label></td>
			<td class="xydac_detail"><input type="text" name="website"
				id="website" /></td>
		</tr>
		<tr>
			<td class="xydac_title"><label for="email"><?php _e('E Mail',XYDAC_CMS_NAME);?>
			</label></td>
			<td class="xydac_detail"><input type="text" name="email" id="email"
				value="<?php echo $user->user_email; ?>" /></td>
		</tr>
		<tr>
			<td class="xydac_title"><label for="description"><?php _e('Description',XYDAC_CMS_NAME);?>
			</label></td>
			<td class="xydac_detail"><textarea name="description"
					id="description"></textarea></td>
		</tr>
	</table>
	<input type="submit" class="button-primary action"
		name="xydac_export_form"
		value="<?php _e('Export Selected',XYDAC_CMS_NAME);?>" />
</form>
<?php
	}
}


?>