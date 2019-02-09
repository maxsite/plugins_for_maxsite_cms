<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * Plugin Name: Special Text Box
 * Authors: Tux(http://6log.ru), minimus(http://blogovod.co.cc/)
 * Plugin URL: http://6log.ru/special-text-boxes
 */

function text_Code($text)
{
	
	$php_replace = array(
			'function_exists' => '<b>function_exists</b>', 
			'function' => '<b>function</b>', 
			'foreach' => '<b>foreach</b>', 
			'endforeach' => '<b>endforeach</b>', 
			'true' => '<b>true</b>', 
			'false' => '<b>false</b>', 
			'endif' => '<b>endif</b>', 
			'if' => '<b>if</b>', 
			'else' => '<b>else</b>', 
			'isset' => '<b>isset</b>', 
			'explode' => '<b>explode</b>', 
			'return' => '<b>return</b>', 
			'count' => '<b>count</b>', 
			'global' => '<b>global</b>',
			'echo' => '<b>echo</b>',
			'include' => '<b>include</b>',
			'print' => '<b>print</b>',
			'exit' => '<b>exit</b>',
			'stristr' => '<b>stristr</b>',
			'<?php' => '<b><?php</b>',
			'&lt;?php' => '<b>&lt;?php</b>',
			'?&gt;' => '<b>?&gt;</b>',
			'?>' => '<b>?></b>',
			'new ' => '<b>new </b>',
			'var ' => '<b>var </b>',
			'class ' => '<b>class </b>',
			'as ' => '<b>as </b>',
			'continue' => '<b>continue</b>',
			'__FUNCTION__' => '<b>__FUNCTION__</b>',
			'$_GET' => '<b>$_GET</b>',
			'$_POST' => '<b>$_POST</b>',
			'md5' => '<b>md5</b>',
			'serialize' => '<b>serialize</b>',
			'ob_start' => '<b>ob_start</b>',
			'define' => '<b>define</b>',
			'endwhile' => '<b>endwhile</b>',
			'while' => '<b>while</b>',
			'trim' => '<b>trim</b>',
			'unset' => '<b>unset</b>',
			'implode' => '<b>implode</b>',
			'ob_get_flush' => '<b>ob_get_flush</b>',
			'strtolower' => '<b>strtolower</b>',
			'str_replace' => '<b>str_replace</b>',
			'array_keys' => '<b>array_keys</b>',
			'in_array' => '<b>in_array</b>',
			'array(' => '<b>array</b>(',
			'//' => '<span class="php-comment">//</span>',
			'# ' => '<span class="php-comment"># </span>',
			'/*' => '<span class="php-comment">/*</span>',
			'*/' => '<span class="php-comment">*/</span>',
			"\t" => '    ',
	);	
	
	if ( isset($text) )
	{
		$text = strtr($text, 
				  array(
						chr(13) => '',
						"\p" => '', 
						"</p>\n" => "\n", 
						"\n<p>" => "\n", 
						'<p>' => "", 
						'</p>' => "", 
					
						'<br>' => "", 
						'<br />' => "", 
						
						'< ?' => '&lt;?', 
						'<code>' => '---', 
						'<pre>' => '---', 
						
						"'" => '&#039;',
						'\"' => '&quot;',
						'\\' => '\\\\',
						
						'<' => '&lt;',
						'>' => '&gt;', 	
						'[' => '&#91;',
						']' => '&#93;', 	
								
					) );
		$t = trim($text);
		$t = explode("\n", $t);
		
		$alt = true;
		foreach ($t as $k => $v )
		{
			$v = strtr($v, $php_replace); 
			
			if ($alt) 
			{
				$t[$k] = '<li class="odd">&nbsp;' . $v . "</li>";
				$alt = false;
			}
			else 
			{
				$t[$k] = '<li>&nbsp;' . $v . "</li>";
				$alt = true;
			}
			
		}
		
		$t = implode("\n", $t);
		
		$text = '<div class="pre"><ol class="pre">' . $t ."</ol></div>\n";
		
	}

	return $text;
}


function include_css($css)
{
	$options_key = 'plugin_specialbox';
	$Options = mso_get_option($options_key, 'plugins', array());	
}
	
	function get_opt($text)
	{	
		//$array = array();
		$text = str_replace("'", '"', $text);
		$keys = explode('" ', $text);
		foreach ($keys as $key)
		{
			$a = explode('=', $key);
			$b = str_replace('"', '', $a[1]);
			//$b = str_replace("'", "", $b);
			$array[$a[0]] = $b;
		}
		return $array;
	}

	function get_Style($atts = null) 
	{
$stbOptions = array( 
			'rounded_corners' => 'true', 
			'text_shadow' => 'false', 
			'box_shadow' => 'false', 
			'border_style' => 'solid',
			'top_margin' => '10',
			'left_margin' => '10',
			'right_margin' => '10',
			'bottom_margin' => '10',
			'cb_color' => '000000',
			'cb_caption_color' => 'ffffff',
			'cb_background' => 'f7cdf5',
			'cb_caption_background' => 'f844ee',
			'cb_border_color' => 'f844ee',
			'cb_image' => '',
			'cb_bigImg' => '',
			'bigImg' => 'false',
			'showImg' => 'true' );			
		
			$bstyle = '';
			$cstyle = '';
			$styleStart = 'style="';
			$styleBody = '';
			$styleCaption = '';
			$styleEnd = '"';
			$needResizing = ( ( $atts['big'] !== '' ) & ( $atts['big'] !==  $stbOptions['bigImg'] ) );


		// Body style 
		$styleBody .= ( $atts['color'] === '' ) ? '' : "color:#{$atts['color']}; ";
		$styleBody .= ( $atts['bcolor'] === '' ) ? '' : "border-top-color: #{$atts['bcolor']}; border-left-color: #{$atts['bcolor']}; border-right-color: #{$atts['bcolor']}; border-bottom-color: #{$atts['bcolor']}; ";
		$styleBody .= ( $atts['bgcolor'] === '' ) ? '' : "background-color: #{$atts['bgcolor']}; ";
			
		// Caption style
		$styleCaption .= ( $atts['ccolor'] === '' ) ? '' : "color:#{$atts['ccolor']}; ";
		$styleCaption .= ( $atts['bcolor'] === '' ) ? '' : "border-top-color: #{$atts['bcolor']}; border-left-color: #{$atts['bcolor']}; border-right-color: #{$atts['bcolor']}; border-bottom-color: #{$atts['bcolor']}; ";
		$styleCaption .= ( $atts['cbgcolor'] === '' ) ? '' : "background-color: #{$atts['cbgcolor']}; ";
			  
		// Image logic
		if ($atts['caption'] === '') 
		{
			if ($atts['image'] === '') 
			{
				if ($needResizing & ($stbOptions['showImg'] === 'true')) 
				{
					if (!in_array($atts['id'], array('custom', 'grey'))) 
					{
				  		$styleBody .= ( $atts['big'] === 'true' ) ? "background-image: url(" . 
	getinfo('plugins_url') . 'specialbox/images/'."{$atts['id']}-b.png); " : "background-image: url(". 
	getinfo('plugins_url') . 'specialbox/images/'."{$atts['id']}.png); ";
				  		$styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-left: 50px; ' : 'min-height: 20px; padding-left: 25px; ';
		  		    }
					elseif ($atts['id'] === 'custom') 
					{
		  		    	$styleBody .= ( $atts['big'] === 'true' ) ? "background-image: url({$stbOptions['cb_bigImg']}); " : "background-image: url({$stbOptions['cb_image']}); ";
		  		    	$styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-left: 50px; ' : 'min-height: 20px; padding-left: 25px; ';
		  		    } 
					else 
					{
		  		    	$styleBody .= 'min-height: 20px; padding-left: 5px; ';
		  		    }							
				} 
			} 
			elseif ($atts['image'] === 'null') 
			{
			  	$styleBody .= 'background-image: url(none); min-height: 20px; padding-left: 5px; ';
			} 
			else 
			{
			  	$styleBody .= "background-image: url({$atts['image']}); ";
			  	if ($needResizing | ($stbOptions['showImg'] === 'false')) $styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-left: 50px; ' : 'min-height: 20px; padding-left: 25px; ';
			}
		} 
		else 
		{
			if ( $atts['image'] !== '' )
			$styleCaption .= ( $atts['image'] === 'null' ) ? "background-image: url(none); padding-left: 5px; " : "background-image: url({$atts['image']}); padding-left: 25px; ";
		}
			
		return array('body' => ( $styleBody !== '' ) ? $styleStart.$styleBody.$styleEnd : '', 'caption' => ( $styleCaption !== '' ) ? $styleStart.$styleCaption.$styleEnd : '');
		
	}


	function get_SpecialBox($options, $text)
	{
		$stextbox_classes = array( 'alert', 'download', 'info', 'warning', 'black', 'custom', 'grey' );
		$style = array('body' => '', 'caption' => '');
				
		if ( !isset($options['id']) ) $options['id'] = 'warning';
		if ( !isset($options['caption']) ) $options['caption'] = '';
		if ( !isset($options['color']) ) $options['color'] = '';
		if ( !isset($options['ccolor']) ) $options['ccolor'] = '';
		if ( !isset($options['bcolor']) ) $options['bcolor'] = '';
		if ( !isset($options['bgcolor']) ) $options['bgcolor'] = '';
		if ( !isset($options['cbgcolor']) ) $options['cbgcolor'] = '';
		if ( !isset($options['image']) ) $options['image'] = '';
		if ( !isset($options['big']) ) $options['big'] = '';
		
		if ( !isset($options['format']) ) $options['format'] = '';
	
		$style =  get_Style($options);
		//echo $options['id'] . $options['caption'];
//
$kk = mso_get_option('plugin_specialbox', 'plugins', array());
if ( !isset($kk['boxes']) ) $kk['boxes'] = array();
$boxes = $kk['boxes'];
//print_r($boxes);
//
		if ( $options['caption'] === '') 
		{
			if ( in_array( $options['id'], $stextbox_classes ) ) 
			{
				if(  $options['format'] === 'code' ) $text = text_Code($text);
				
				return '<div class="stb-'.$options['id'].'_box stb_box" '.$style['body'].'>' . $text . '</div>';
			}
			elseif (  $options['id'] === 'code' ) 
			{
				return '<div class="stb-'.$options['id'].'_box stb_box" '.$style['body'].'>' . text_Code($text) . '</div>';
			}
			elseif ( strpos($boxes, $options['id']) ) 
			{
				return '<div class="stb-'.$options['id'].'_box stb_box" >' . $text . '</div>';
			}
			else 
			{ 
				return $text;	
			}
		}
		else 
		{
			if ( in_array( $options['id'], $stextbox_classes ) ) 
			{
				if(  $options['format'] === 'code' ) $text = text_Code($text);
				
				return '<div class="stb-'.$options['id'].'-caption_box stb_box_c" '.$style['caption'].'>' . $options['caption'] . '</div>
					<div class="stb-'.$options['id'].'-body_box stb_box_b" '.$style['body'].'>' . $text . '</div>';
			}
			elseif ( $options['id'] === 'code' ) 
			{
				return '<div class="stb-'.$options['id'].'-caption_box stb_box_c" '.$style['caption'].'>' . $options['caption'] . '</div>
					<div class="stb-'.$options['id'].'-body_box stb_box_b" '.$style['body'].'>' . text_Code($text) . '</div>';
			} 
			elseif ( strpos($boxes, $options['id']) ) 
			{
				return '<div class="stb-'.$options['id'].'-caption_box stb_box_c" '.$style['caption'].'>' . $options['caption'] . '</div>
					<div class="stb-'.$options['id'].'-body_box stb_box_b" '.$style['body'].'>' . $text . '</div>';
			} 
			else 
			{ 
				return $text;	
			}
		}
	}
?>