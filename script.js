;
/**  Custom Accordian with Jquery * /
jQuery(document).ready(function() { 
	jQuery('#accordion').find('.accordion-toggle').each(function(){
		jQuery(".accordion-content").slideUp('fast');
	});
    jQuery('#accordion').find('.accordion-toggle').click(function(){

      //Expand or collapse this panel
	  jQuery(this).next().slideToggle('fast');
	  
      //Hide the other panels
	  jQuery(".accordion-content").not(jQuery(this).next()).slideUp('fast');
	  
	  // Add/remove active class
	  jQuery(this).toggleClass('active');
	  jQuery(".accordion-toggle").not(jQuery(this)).removeClass('active');

    });
  });
  

/*START TABS */
jQuery(document).ready(function() { jQuery('ul.xydac-custom-meta li a').each(function(i) { var thisTab = jQuery(this).parent().attr('class').replace(/active /, ''); 
if ( 'active' != jQuery(this).attr('class') ) jQuery('div.' + thisTab).hide(); 
jQuery('div.' + thisTab).addClass('tab-content'); jQuery(this).click(function(){ jQuery(this).parent().parent().parent().children('div').hide(); jQuery(this).parent().parent('ul').find('li.active').removeClass('active'); jQuery(this).parent().parent().parent().find('div.'+thisTab).show(); jQuery(this).parent().parent().parent().find('li.'+thisTab).addClass('active'); }); }); 
jQuery('.heading').hide(); jQuery('.xydac-custom-meta').show(); }); 
/*END TABS */

/*START jcarousellite_1.0.1.min.js */
(function($){$.fn.jCarouselLite=function(o){o=$.extend({btnPrev:null,btnNext:null,btnGo:null,mouseWheel:false,auto:null,speed:200,easing:null,vertical:false,circular:true,visible:3,start:0,scroll:1,beforeStart:null,afterEnd:null},o||{});return this.each(function(){var b=false,animCss=o.vertical?"top":"left",sizeCss=o.vertical?"height":"width";var c=$(this),ul=$("ul",c),tLi=$("li",ul),tl=tLi.size(),v=o.visible;if(o.circular){ul.prepend(tLi.slice(tl-v-1+1).clone()).append(tLi.slice(0,v).clone());o.start+=v}var f=$("li",ul),itemLength=f.size(),curr=o.start;c.css("visibility","visible");f.css({overflow:"hidden",float:o.vertical?"none":"left"});ul.css({margin:"0",padding:"0",position:"relative","list-style-type":"none","z-index":"1"});c.css({overflow:"hidden",position:"relative","z-index":"2",left:"0px"});var g=o.vertical?height(f):width(f);var h=g*itemLength;var j=g*v;f.css({width:f.width(),height:f.height()});ul.css(sizeCss,h+"px").css(animCss,-(curr*g));c.css(sizeCss,j+"px");if(o.btnPrev)$(o.btnPrev).click(function(){return go(curr-o.scroll)});if(o.btnNext)$(o.btnNext).click(function(){return go(curr+o.scroll)});if(o.btnGo)$.each(o.btnGo,function(i,a){$(a).click(function(){return go(o.circular?o.visible+i:i)})});if(o.mouseWheel&&c.mousewheel)c.mousewheel(function(e,d){return d>0?go(curr-o.scroll):go(curr+o.scroll)});if(o.auto)setInterval(function(){go(curr+o.scroll)},o.auto+o.speed);function vis(){return f.slice(curr).slice(0,v)};function go(a){if(!b){if(o.beforeStart)o.beforeStart.call(this,vis());if(o.circular){if(a<=o.start-v-1){ul.css(animCss,-((itemLength-(v*2))*g)+"px");curr=a==o.start-v-1?itemLength-(v*2)-1:itemLength-(v*2)-o.scroll}else if(a>=itemLength-v+1){ul.css(animCss,-((v)*g)+"px");curr=a==itemLength-v+1?v+1:v+o.scroll}else curr=a}else{if(a<0||a>itemLength-v)return;else curr=a}b=true;ul.animate(animCss=="left"?{left:-(curr*g)}:{top:-(curr*g)},o.speed,o.easing,function(){if(o.afterEnd)o.afterEnd.call(this,vis());b=false});if(!o.circular){$(o.btnPrev+","+o.btnNext).removeClass("disabled");$((curr-o.scroll<0&&o.btnPrev)||(curr+o.scroll>itemLength-v&&o.btnNext)||[]).addClass("disabled")}}return false}})};function css(a,b){return parseInt($.css(a[0],b))||0};function width(a){return a[0].offsetWidth+css(a,'marginLeft')+css(a,'marginRight')};function height(a){return a[0].offsetHeight+css(a,'marginTop')+css(a,'marginBottom')}})(jQuery);
/*END jcarousellite_1.0.1.min.js */

jQuery(document).ready(function($) {
	if($('a.xydac_add_more').length)
		$('a.xydac_add_more').click(function () {
			$.post(ajaxurl, {
				action: 'xydac_cms_post_type',
				subaction: 'xydac_add_more',
				field_name: $(this).attr('id'),
				type: 	$(this).parents('div.xydac_cms_field').attr('rel')
			}, function(data) {
				var d = $(data).last().attr('rel');
				var w = d.substring(0,d.lastIndexOf('-'));
				$('div#'+w).append(data);
				if(typeof(xydac_cms_post_type_sucess)=='function')
					xydac_cms_post_type_sucess();
				if(typeof(tinyMCE) !== 'undefined'){
					var _id = $(data).find('textarea').attr('id');
					tinyMCE.execCommand('mceAddControl', false, _id);
					}
					
				return false;
				});
			return false;
		});
	if($('a.xydac_add_more_page').length)
		$('a.xydac_add_more_page').click(function () {
			$.post(ajaxurl, {
				action: 'xydac_cms_page_type',
				subaction: 'xydac_add_more',
				field_name: $(this).attr('id'),
				type: 	$(this).parents('div.xydac_cms_field').attr('rel')
			}, function(data) {
				var d = $(data).find('span').last().attr('class');
				var _id = $(data).find('textarea').attr('id');
				var w = d.substring(0,d.lastIndexOf('-'));
				$('div#'+w).append(data);
				if(typeof(xydac_cms_post_type_sucess)=='function')
					xydac_cms_post_type_sucess();
				if(typeof(tinyMCE) !== 'undefined'){
					tinyMCE.execCommand('mceAddControl', false, _id);
				}
				
				return false;
				});
			return false;
		});
	});


function xydac_loadScript(url, callback){

    var script = document.createElement("script")
    script.type = "text/javascript";

    if (script.readyState){  //IE
        script.onreadystatechange = function(){
            if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  //Others
        script.onload = function(){
            callback();
        };
    }

    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                