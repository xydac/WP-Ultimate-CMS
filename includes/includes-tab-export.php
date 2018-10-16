<?php
/**
 * This is partial Code included in class-xydac-cms-module.php 
 * @function view_export_func
 * Renders the Export tab on all modules and handles Export display.
 */
    $helpText = __('You can use below content to import to another instance of Ultimate CMS from the Import tab. You can also export individual items by using the Export link under each item.',XYDAC_CMS_NAME);
    $title = "Exporting ";
    $data = "";

    if(isset($_GET[$this->module_name."_name"])){
        $obj = $_GET[$this->module_name."_name"];
        $title .=  $this->get_module_label().": ".$obj;
        $data = $this->export_object('main', 'name', $obj);	

    }else{
        $title .=  "all ".$this->get_module_label();
        $data = $this->export_object('main', 'name');
    }
    
    echo "<div class='editbox'><h1>".$title."</h1><hr>";
    echo "<p>".$helpText."</p>";
    
    echo '<textarea class="codemirror_custom_json" rows="50" cols="70" >';
    echo $data;	
    echo '</textarea>';
    echo "</div>";

?>