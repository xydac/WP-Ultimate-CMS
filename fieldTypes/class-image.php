<?php 

class image extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'image';
		$this->flabel = __('Image',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}

	public static function get_image_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
	{
		$r = '';
		if($value){
			if (preg_match('/\A(?:\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])\Z/i', $value))
			{
				$img_src = wp_specialchars_decode($value,ENT_QUOTES);
			}
			else
			{
				preg_match_all('/src="([^"]*)"/i', wp_specialchars_decode($value,ENT_QUOTES), $result);
				$result = $result[0][0];
				$img_src = substr($result,5,-1);
					
			}
		}
		else
			$img_src = '../wp-includes/images/blank.gif';
		extract( $args );
		if(isset($tabular) && $tabular){
			$r.='<tr class="form-field"><th scope="row" valign="top">';
			$r.='<label for="'.$name.'" style="display:inline">'.$label.'</label><p>';
			$r.='</th><td>';
		}
		$r.="<div style='position:relative;'><fieldset style='width:70%;float:left;height:75px;margin-bottom:20px;'>";
		if(!isset($tabular) || (isset($tabular) &&!$tabular)){
			$r.='<label for="'.$name.'" style="display:inline">'.$label.'</label><p>';
		}
		$r.='<a href="media-upload.php?type=image&TB_iframe=true&width=640&height=513" class="thickbox xydac_image" id="xydac_cpt_add_image_'.$name.'" name="'.$name.'"  title="Add an Image"><img src="images/media-button-image.gif" alt="Add an Image" style="padding-right:10px;">Add Image</a>';
		$r.='&nbsp;';
		$r.='<a href="#" class="xydac_image" id="xydac_cpt_remove_image_'.$name.'" name="'.$name.'" title="Remove Image">Remove Image</a>';

		if($pre_arr)
			$r.="<p><input type='text' id='".$name."' name='".$pre_arr.'['.$name.']'."' value='".esc_html( $value, 1 )."' /></p>";
		else
			$r.="<p><input type='text' id='".$name."' name='".$name."' value='".esc_html( $value, 1 )."' /></p>";


		$r.='</p><p><span class="'.$name.'">'.$desc.'</span></p>';
		$r.="</fieldset>";
		$r.="<img src='".$img_src."' id='".$name."' width='75px' height='75px' style='float:right;margin-right:5px;'/>";
		$r.="<div style=\"clear:left\"></div>";
		if($create_old)
			$r.='<input type="hidden" name="'.$name.'-old" value="'.esc_html( $value, 1 ).'" />';
		$r.= "</div>";
		if(isset($tabular) && $tabular){
			$r.='</td></tr>';
		}

		return $r;
	}
	function get_input($no='false',$val=false,$tabular=false)
	{
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		return self::get_image_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}



	public function output($vals,$atts)
	{
	 //$atts = stripslashes_deep($atts);
	 extract(shortcode_atts(array(
	 		'pre' => '',
	 		'before_element'=>'',
	 		'after_element'=>'',
	 		'post' => '',
	 ), $atts));

		$s = "";
		foreach($vals as $val)
		{
			$val = wp_specialchars_decode($val,ENT_QUOTES);
			if (preg_match('/\A(?:\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])\Z/i', $val))
				$val='<img src="'.$val.'" />';
			else
				$val=do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES));
			$s.=wp_specialchars_decode($before_element).do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES)).wp_specialchars_decode($after_element);
		}
		return wp_specialchars_decode($pre).$s.wp_specialchars_decode($post);

	}
	public function taxonomy_output($val,$atts)
	{
		//$atts = wp_specialchars_decode(stripslashes_deep($atts),ENT_QUOTES);
	 extract(shortcode_atts(array(
	 		$this->name.'_before'=>'',
	 		$this->name.'_after'=>'',
	 		'pre' =>'',
	 		'post'=>'',
	 		'rawdata'=>'0'
	 ), $atts));
	 	
		$s = "";
		if(empty($val))return;
		if($rawdata=='1')
		{	
			$arr = array();
			preg_match( '/src="([^"]*)"/i', wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES), $arr ) ;
			if(isset($arr[1]))
				return $arr[1];		
		}
		if (preg_match('/\A(?:\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])\Z/i', $val))
			$val='<img src="'.$val.'" />';
		else
			$val=do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES));
		$s.=wp_specialchars_decode(${$this->name.'_before'}).do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES)).wp_specialchars_decode(${$this->name.'_after'});
		
		return wp_specialchars_decode($pre).$s.wp_specialchars_decode($post);
	}
	public function adminscript()
	{
		add_thickbox();
		$r = <<<XYDACSCRIPT


	jQuery(document).ready(function() {

	(function($) {
	xydac_tb_position = function() {
		var tbWindow = $('#TB_window'), width = $(window).width(), H = $(window).height(), W = ( 720 < width ) ? 720 : width, adminbar_height = 0;

		if ( $('body.admin-bar').length )
			adminbar_height = 28;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top': 20 + adminbar_height + 'px','margin-top':'0'});
		};

		return $("a[id^='xydac_cpt_add_image']").each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
		});
	};

	$(window).resize(function(){ xydac_tb_position(); });
	})(jQuery);

	function xydac_cms_image(jQuery){
		var xydac_field='';
		jQuery("a[id^='xydac_cpt_add_image']").click(function() {
		 xydac_field = jQuery(this).attr('name');
		 tb_show('Add an Image', jQuery(this).attr('href'));
		 console.log('FIELD'+xydac_field);
		 return false;
		});

		//Click on Remove Image
		jQuery("a[id^='xydac_cpt_remove_image']").click(function() {
		 xydac_field = jQuery(this).attr('name');
		 jQuery("img[id='" +xydac_field+ "']").attr('src','../wp-includes/images/blank.gif');
		 jQuery("input[type='text'][id='"+xydac_field+"']").attr('value',' ');
		 return false;
		});
		jQuery("input[name^=xydac_custom]").blur(function() {
			_x_imgurl = jQuery(this).attr('value');
			xydac_field_temp = jQuery(this).attr('id');
			if (/^(?:\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[\-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])$/i.test(_x_imgurl)) {
				jQuery("img[id='" +xydac_field_temp+ "']").attr('src',_x_imgurl);

			}

		});

		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html) {
			if(xydac_field!='')
			{
			console.log('html : '+html);
				var _x_img = jQuery(html).filter('img').attr('src');
				if(typeof(_x_img)=='undefined')
					var _x_img = jQuery(html).find('img').attr('src');
				jQuery("img[id='"+xydac_field+"']").attr('src',_x_img);
XYDACSCRIPT;

		if(isset($this->fieldoptions['accesstype']) && $this->fieldoptions['accesstype']=='taxonomy')
			$r.= "\n\t\t\t\tjQuery(\"input[type='text'][id='\"+xydac_field+\"']\").attr('value',src);\n";
		else
			$r.= "\n\t\t\t\tjQuery(\"input[type='text'][id='\"+xydac_field+\"']\").attr('value',html);\n";


		$r.= <<<XYDACSCRIPTC
				tb_remove();

			}
			else
				window.original_send_to_editor(html);
			}
	}

	xydac_cms_image(jQuery);
	if(typeof(xydac_cms_post_type_sucess_original)=='function')
		xydac_cms_post_type_sucess_original = xydac_cms_post_type_sucess;
	xydac_cms_post_type_sucess = function(){
		xydac_cms_image(jQuery);
		if(typeof(xydac_cms_post_type_sucess_original)=='function')
			xydac_cms_post_type_sucess_original();
		}
	});
XYDACSCRIPTC;

		return $r;
	}

}
?>