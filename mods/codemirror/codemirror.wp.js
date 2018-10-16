jQuery(document).ready(function($) {
	var javascript_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
	javascript_editorSettings.codemirror = _.extend(
		{},
		javascript_editorSettings.codemirror,
		{
			indentUnit: 2,
			tabSize: 2,
			mode: 'javascript',
		}
	);
	var css_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
	css_editorSettings.codemirror = _.extend(
		{},
		css_editorSettings.codemirror,
		{
			indentUnit: 2,
			tabSize: 2,
			mode: 'css',
		}
	);
	
	var html_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
	html_editorSettings.codemirror = _.extend(
		{},
		html_editorSettings.codemirror,
		{
			indentUnit: 2,
			tabSize: 2,
			mode: 'text/html',
		}
	);
	var json_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
	json_editorSettings.codemirror = _.extend(
		{},
		json_editorSettings.codemirror,
		{
			indentUnit: 2,
			tabSize: 2,
			mode: 'application/json',
		}
	);
	if($('.codemirror_custom_json').length)
		wp.codeEditor.initialize( $('.codemirror_custom_json'), json_editorSettings );
	if($('#xydac_ucms_form\\[cutom_js\\]').length)
		wp.codeEditor.initialize( $('#xydac_ucms_form\\[cutom_js\\]'), javascript_editorSettings );
	if($('.codemirror_custom_js').length)
		wp.codeEditor.initialize( $('.codemirror_custom_js'), javascript_editorSettings );
	if($('#xydac_ucms_form\\[cutom_css\\]').length)
		wp.codeEditor.initialize( $('#xydac_ucms_form\\[cutom_css\\]'), css_editorSettings );
	if($('.codemirror_custom_css').length)	
        wp.codeEditor.initialize( $('.codemirror_custom_css'), css_editorSettings );
        
	if($('.codemirror_custom_html').length)	
        wp.codeEditor.initialize( $('.codemirror_custom_html'), html_editorSettings );
        
    
	if($('#archive_type\\[args\\]\\[customhtml\\]').length)	
        wp.codeEditor.initialize( $('#archive_type\\[args\\]\\[customhtml\\]'), html_editorSettings );
    
	if($('#archive_type\\[args\\]\\[afterloop\\]').length)	
        wp.codeEditor.initialize( $('#archive_type\\[args\\]\\[afterloop\\]'), html_editorSettings );
        
});