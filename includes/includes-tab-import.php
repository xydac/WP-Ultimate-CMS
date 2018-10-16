<?php
/**
 * This is partial Code included in class-xydac-cms-module.php 
 * @function view_import_func
 * Renders the Import tab on all modules and handles import of modules using the form.
 */
	if ( isset( $_POST['xydac_import_form'] )){ 
		if(! wp_verify_nonce( $_POST['xydac_import_form'], 'xydac_import' ) ) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} else {

			$import_data = json_decode(wp_unslash(html_entity_decode($_POST['importdata'])), true);
			$logs = [];
			$backed_up_modules = [];
			$backup_data = get_option(XYDAC_CMS_MODULES_BACKUP);
			if(!is_array($backup_data))
				$backup_data = [];
			if (!is_null($import_data)) {
				if(is_array($import_data))
					foreach($import_data as $k=>$import){
						array_push($logs, "INFO: Starting $k");
						//validations
						if(isset($import['name']) && isset($import['schema'])  && isset($import['type']))
							array_push($logs, "INFO: Mandatory Fields Present");
						if($this->startsWith($import['type'], "xydac_")){
							array_push($logs, "INFO: Found Ultimate CMS Type for import.");
							$type = substr($import['type'],6);
							$name = $import['schema']['name'];
							$module = xydac()->modules->$type;
							if(isset($module)){
								array_push($logs, "INFO: Valid Module: ". $type);
								$check_name = $module->get_main_by_name($name);
								if(isset($check_name) && !empty($check_name)){
									array_push($logs, "ERROR: Name Already Exists : $name, You can Edit the name attribute and import again or delete the existing to import.");
								}else{
									array_push($logs, "INFO: All Good, Import Started...");

									if(!in_array($type, backed_up_modules)){
										$backup_data[$type] = $module->export_object('main', 'name');
										xydac()->dao->set_options(XYDAC_CMS_MODULES_BACKUP, $backup_data);
									}
									
									$insert_main = $module->insert_object('main',$name,'',$import['schema'],'name');
									if(!is_wp_error($insert_main)){
										array_push($logs, "INFO: Added $type with name : $name");
										if($import['fields'] && is_array($import['fields'])){
											$module->dao->register_option($module->get_registered_option('field')."_".$name);
											foreach($import['fields'] as $field){
												$field_name = $field['field_name'];
												$module->insert_object('field',$field['field_name'],$name,$field,'field_name');
												array_push($logs, "INFO: Added Field for : $name with name : $field_name");
											}
										}else{
											array_push($logs, "INFO: No Field Definition found for : $name");
										}
									}else{
										array_push($logs, "ERROR: Insert Failed for : $name with Error: ". $insert_main);
									}
								}
							}
						}
					array_push($logs, "-------------------------------------");
					}
				else
					array_push($logs, "ERROR: Invalid Data");
			}else{
				array_push($logs, "ERROR: Validation Failure");
			}
			
			echo '<div class="importlogs">';
			foreach($logs as $l){
				if($this->startsWith($l,'ERROR'))
					echo "<p class='xydactableerror'>".$l."</p>";
				else
					echo "<p>".$l."</p>";
			}
			echo '</div>';
		}
	} else {
    ?>
        <div class='editbox'><h1>Import</h1><hr>
            <form action="" method="post" name="xydac_import">
                <label for="importdata">Please provide the json to be imported here. You can import any module from any Import screen.</label>
                <textarea class="codemirror_custom_json" name="importdata" style="width:100%; min-height:600px; margin-top:10px;"></textarea>
                <?php wp_nonce_field( 'xydac_import', 'xydac_import_form' ); ?>
                <br/>
                <input type="submit" class="button button-primary" name="xydac_import_submit" value="Import"/>
            </form>
        </div>
    <?php
		    } ?>