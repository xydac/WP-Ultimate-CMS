<?php


class xydac_forms_use{

	function __construct()
	{
        add_action('init', array($this, 'init'));
		add_shortcode('xydac_forms',array($this,'xydac_forms_shortcode'));
        
    }
    
    public function init()
    {
        
        if (!empty($_POST['nonce_xydac_form']))
        {
            if (!wp_verify_nonce($_POST['nonce_xydac_form'], 'xydac_forms_submit'))
            {
                die('You are not authorized to perform this action.');
            } else
            {
                $output = $_POST['xydac_custom'];
                $text = $output['xydac_form_key'];
                
                if(isset($text)){
                    $main = xydac()->modules->forms->get_main_by_name($text);
                    $fields = xydac()->modules->forms->get_field($text);
                    $data = [];
                    foreach ($output as $a => $value) {
                        $b=explode('-',$a);
                        $field_name = $b[0];
                        if($field_name !== 'xydac_form_key')
                            $data[$field_name] = $value;
                    }
                    do_action('xydac_forms_submit_'.$text, $main, $data);
                    do_action('xydac_forms_submit', $main, $data);// -- Add catch all handler
                }

            }
        }
    }

    


	function xydac_forms_shortcode($atts, $text) {
		
        $text = trim($text);        
        $main = xydac()->modules->forms->get_main_by_name($text);

        $fields = xydac()->modules->forms->get_field($text);
        
        if(isset($main['content_css']) && !empty($main['content_css'])){
            echo "<style type='text/css'>".$main['content_css']."</style>";
        }
        $message = get_transient( 'xydac_forms_message' );
        delete_transient( 'xydac_forms_message' );
        if($message)
            echo '<p class="response">'.$message.'</p>';
        echo "<div class='$text'>";
        echo "<form method='post'  action=''>";
    
        wp_nonce_field('xydac_forms_submit', 'nonce_xydac_form');
    
        foreach ($fields as $key => $value)
		{
            $field_type = xydac()->modules->forms->get_field_type($text,$value['field_name']);
            $temp_field = new $field_type($value['field_name'], $value);
			$out.= $temp_field->input('','');
        }
        // -If template provided then use the template
        if(isset($main['content_html']) && !empty($main['content_html'])){
            $out = str_replace('[CONTENT]',$out,$main['content_html']);
        }
        echo $out;
        if(isset($main['content_js']) && !empty($main['content_js'])){
            echo "<script>".$main['content_js']."</script>";
        }
        echo "<input type='hidden' name='xydac_custom[xydac_form_key]' value='".$text."' />";
        echo "<input type='submit'  value='Submit'/>";
        echo "</form>";
        echo "</div>";
	}
}
?>