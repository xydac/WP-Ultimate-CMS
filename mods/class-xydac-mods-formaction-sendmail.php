<?php
/*
Module Name:	Form Action - Send Mail
Type:			Mod
Description:	Adds Xydac Forms action to send mail when form is submitted. If not activated, you won't be able to send email as Form Submit Action.
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0
*/

class xydac_mods_formaction_sendmail{
	
	function __construct(){
	
        add_action('xydac_forms_submit', array($this,'handle_sendmail' ), 10, 2);
        add_filter('xydac_forms_variables',array($this,'xydac_forms_variables'));
    }
    
    public function xydac_forms_variables($form_variables){
        $updated_variables = array(
            'heading-2' => array('arr_label' => __('Send Email on Submit',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
            'sendmail_active' => array( 'arr_label' => __('Active',XYDAC_CMS_NAME) , 'name' => '[sendmail][active]' , 'type'=>'boolean', 'desc'=> __('Active',XYDAC_CMS_NAME), 'default'=>''),
            'sendmail_to' => array( 'arr_label' => __('Send Mail To',XYDAC_CMS_NAME) , 'name' => '[sendmail][to]' , 'type'=>'string', 'desc'=> __('Who to send email to on form submission',XYDAC_CMS_NAME), 'default'=>''),
            'sendmail_subject' => array( 'arr_label' => __('Send Mail Subject',XYDAC_CMS_NAME) , 'name' => '[sendmail][subject]' , 'type'=>'string', 'desc'=> __('Subject to send email with on form submission',XYDAC_CMS_NAME), 'default'=>''),
            'sendmail_from' => array( 'arr_label' => __('Send Mail From',XYDAC_CMS_NAME) , 'name' => '[sendmail][from]' , 'type'=>'string', 'desc'=> __('From email address to send from on form submission',XYDAC_CMS_NAME), 'default'=>''),
            'sendmail_template' => array( 'arr_label' => __('Email Body Template',XYDAC_CMS_NAME) , 'name' => '[sendmail][template]' , 'arr_clazz' => 'codemirror_custom_html', 'type'=>'textarea', 'desc'=> __('The email body template',XYDAC_CMS_NAME), 'default'=>''),
            
        );
        $form_variables = array_merge($form_variables, $updated_variables);
        return $form_variables;
    }
	

    public function handle_sendmail($main, $data){
        
        if($main['sendmail']['active']){
            $message = $data;
            $to = $main['sendmail']['to'];
            $subject = $main['sendmail']['subject'];
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            if(isset($main['sendmail']['from']))
                $headers[] = 'From: '.$main['sendmail']['from'];

            if(isset($main['sendmail']['template']) && !empty($main['sendmail']['template'])){
                $message = $main['sendmail']['template'];
                foreach ($data as $key => $value) {
                    $message = str_replace('##'.$key.'##', $value, $message);
                }
            }
            
            $status = wp_mail($to, $subject, $message, $headers);
            if($status)
                set_transient( 'xydac_forms_message', "Message Sent Successfully", 60*60*12 );
            else
                set_transient( 'xydac_forms_message', "Message Not Sent", 60*60*12 );
        }
    }
}

?>