<?php
/*
Plugin Name: AjaxCodeRender(Ajax代码高亮器)
Plugin URI: http://www.storyday.com/html/y2008/2111_ajaxcoderenderajaxcoderender.html
Description: 利用ajax+geshi完成的一个代码美化插件
Version: 1.0
Author: jiangdong
date:2008-12-16
Author URI:http://www.storyday.com
*/

define('IS_NOT_CSS',false);//若已经集成本插件ajaxcoderrender.css到模板中，请将true改成false

define('IS_NOT_JQUERY',false);//若已经集成jqeury，请将true改成false


/*下面不要修改*/
include_once 'geshi.php';

function AjaxCoderRenderFilter($string){
	
	$pattern="%\[code([^\]]*)\](.*?)\[/code\]%si";
	preg_match_all($pattern,$string,$result);

	$i = 0;
	 
	for ($index=1;$index<= count($result[0]);$index++){ 
		
		$code=$result[2][$i]; 
		$code = str_replace('<p>', '', $code);
		$code = str_replace('</p>', '<br />', $code);
		$string = str_replace($result[2][$i],$code,$string);
		$i ++ ;
	}

	$string = preg_replace('%\[code([^\]]*)\](.*?)\[/code\]%si', '<code$1>$2</code>', $string);
	return  $string;
}

$language = "html";

if($_POST['lang'] != '' )$language= $_POST['lang'];

if( strlen( $_POST['code'] ) > 2 ){

	$geshi = new GeSHi($_POST['code'], $language);

	$geshi->set_header_type(GESHI_HEADER_DIV); 

	$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);

	$out = str_replace("‘","'",$geshi->parse_code() ) ;

	$out = str_replace("’","'",$out  ) ;
	$out = str_replace("”","\"",$out  ) ;
	$out = str_replace("“","\"",$out  ) ;

	$out = "<div class='coscode'>".$out."</div>";

	echo stripslashes( $out );
}
else{// star plugin function

	if(function_exists('get_bloginfo'))	$BlogUrl = @get_bloginfo('siteurl');
	else $BlogUrl = '';

	$ThisPluginUrl =  $BlogUrl. '/wp-content/plugins/ajaxcoderender/';
	define('ThisPluginUrl',$ThisPluginUrl);

	if( !function_exists('AjaxCodeRenderCss') ){
		function AjaxCodeRenderCss(){
			echo '<link rel="stylesheet" href="'.ThisPluginUrl.'ajaxcoderrender.css" type="text/css" media="screen"/>';

		}
	}

	if( !function_exists('AjaxCodeRenderJs') ){
		function AjaxCodeRenderJs(){
			echo '<script src="'.ThisPluginUrl.'jquery.js"></script>';

		}
	}

if( !function_exists('RenderCodeJs') ){
	function RenderCodeJs(){?>
<script language="JavaScript">
<!-- 
var renderurl = "<?php echo ThisPluginUrl;?>AjaxCodeRender.php";
function cosshowcode(i){var code2=''; var codein = document.getElementById("code-div-"+i).innerText;codein==null?code2 = $("#code-div-"+i).text():code2=codein; var code="<div><textarea style='width:97%;height:100px' id='cos_code_text_"+i+"'>" + code2.replace('(show/hide)plain text','')  + "</textarea></div>";if( $('#cos_code_text_'+i).html()== null )$("#code-div-"+i).prepend(code);else{$('#cos_code_text_'+i).remove();}}
function RenderIt(ele){ $(ele).each(function(i){var code='';var lang = $(this).attr("lang");var tmp = $(this);this.innerText==null?code = tmp.text():code=this.innerText ;$(this).html('<b>Rendering...</b>'+$(this).html());		$.post(renderurl, { lang :lang, code :code },function(data){ data = "<div class='codeopt'><a  href='#nothisid' onClick='cosshowcode("+i+")'>(show/hide)plain text</a></div>" + data; tmp.attr("id","code-div-"+i);tmp.html( data ); });}); }
 function RenderNow(){RenderIt("div.code");RenderIt("code");RenderIt("pre");}
 window.onload=RenderNow; 
//-->
</script>
		<?php
		 }
	}
	if(function_exists('add_action')) {
		if(IS_NOT_CSS) add_action('wp_head','AjaxCodeRenderCss'); 
		if(IS_NOT_JQUERY) add_action('wp_head','AjaxCodeRenderJs'); 
		add_action('wp_head','RenderCodeJs'); 
		add_filter('the_content','AjaxCoderRenderFilter'); 
		add_filter('comment_text','AjaxCoderRenderFilter'); 
	}
}//end of plugin
?>