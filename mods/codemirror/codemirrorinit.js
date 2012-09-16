var xydac_codemirror = new Array();
var xydac_codemirror_name = new Array();
jQuery(document).ready(function($) {
	var j=0;
	jQuery('textarea[name*="css"],textarea[name*="customscript"],textarea[name*="js"],textarea[name*="html"],textarea[name*="loop"]').each(function(){
	if(this.name.search(/js/i)!=-1 || this.name.search(/customscript/i)!=-1){
		xydac_codemirror[j] = CodeMirror.fromTextArea(document.getElementById(this.name),{lineNumbers :true,matchBrackets: true,mode :'text/javascript',gutter:true});
		}
	else if(this.name.search(/css/i)!=-1){
		xydac_codemirror[j] = CodeMirror.fromTextArea(document.getElementById(this.name),{lineNumbers :true,matchBrackets: true,mode :'text/css',gutter:true});
		}
	else{
		xydac_codemirror[j] = CodeMirror.fromTextArea(document.getElementById(this.name),{lineNumbers :true,matchBrackets: true,mode :'htmlmixed',gutter:true});
		}
	if(this.name == 'xydac_archive_type[args][beforeloop]')
		xydac_codemirror_name[j] = "beforeloop";
	else if(this.name == 'xydac_archive_type[args][customhtml]')
		xydac_codemirror_name[j] = "customhtml";
	else if(this.name == 'xydac_archive_type[args][afterloop]')
		xydac_codemirror_name[j] = "afterloop";
	else if(this.name == 'xydac_archive_type[customcss]')
		xydac_codemirror_name[j] = "customcss";
	else if(this.name == 'xydac_archive_type[customscript]')
		xydac_codemirror_name[j] = "customscript";
	j++;
	});
	jQuery( "#accordion h3" ).click(function() {
			for(i=0;i<xydac_codemirror.length;i++){
				xydac_codemirror[i].refresh()}
		});
	
});