<?php

/*
Plugin Name: Caspio Deploy2
Plugin URI: http://www.caspio.com
Description: Enables ShortCode placeholders for use with the Caspio cloud computing database application service. Can be used for SEO deployment of content, as well as embedding the AJAX widget used to display Caspio forms. Replaces the earlier Caspio Deployment Control plugin (which did not use shortcodes).
Author: Caspio
Version: 1.6
Author URI: http://www.caspio.com
*/

function caspio ($atts = NULL, $content = '') {

if($content && strpos($content,'AppKey') ) {

// try to extract variables from javascript snippet or direct deployment link
	$regex = "/(http[a-z0-9\/:]+).caspio.(com|net).dp.asp.AppKey.([A-Za-z0-9]+)/";
	preg_match($regex,$content,$matches);
	$atts["embed"] = $matches[1].'.caspio.'.$matches[2];
	$atts["key"] = $matches[3];
}
elseif($content)
	{
	//hack for html entities replacing single quote
	$content = preg_replace('/&[^;]+;/',"'",$content);
	// hack to extract parameters from php code snippet
	$regex = "/'(http[^']+)','([^']+)','([^'])'/";
	if(preg_match($regex,$content,$matches))
		{
		$atts["url"] = $matches[1];
		$atts["key"] = $matches[2];
		$atts["style"] = $matches[3];
		}
	}

if($atts["seo"] ) {
	$regex = "/(http[a-z0-9\/:]+).caspio.(com|net).dp.asp.AppKey.([A-Za-z0-9]+)/";
	if(preg_match($regex,$atts["seo"],$matches) )
		{
		$atts["url"] = $matches[1].'.caspio.'.$matches[2].'/';
		$atts["key"] = $matches[3];
		}
}

if( ($embed = $atts["embed"]) && ($app_key = $atts["key"]) )
	{
	//check if ssl mode
	if(strpos($embed,'https:') !== false)
		$s = 's';
	return '<script type="text/javascript" src="'.$embed.'/scripts/e1.js"></script>
<script type="text/javascript" language="javascript">try{f_cbload("'.$app_key.'","http'.$s.':");}catch(v_e){;}</script>
<div id="cxkg"><a href="'.$embed.'/dp.asp?AppKey='.$app_key.'">Click here</a> to load this Caspio <a href="http://www.caspio.com" title="Online Database">Online Database</a>.</div>';
	}
elseif( ($url = $atts["url"]) && ($app_key = $atts["key"]) )
	{
	$dp_cbstyle = ($atts["style"]) ? $atts["style"] : 'l';
	ob_start();
	dpload($url, $app_key, $dp_cbstyle);
	return ob_get_clean();
	}

}

add_shortcode('caspio','caspio');

// WordPress customization of session start function in PHP library

function caspio_session() {
if(!$_SESSION)
	session_start();
}

add_action('init','caspio_session');


/**
* (c) Copyright 2010 Caspio, Inc. All rights reserved.
* 
* Caspio Bridge PHP SEO engine.
*/

// Try to start session
// In case if the SSD deploy code added after some HTML output the "headers already sent" Warning will be generated

// Post request function
function PostRequest($_url, $_data, $_do_post) {
	return PostRequestEx($_url, $_data, $_do_post, 3);
}

// Post request function
function PostRequestEx($_url, $_data, $_do_post, $_allowedRedirectsCount) {
	
	reset($_data); // prepare array for iteration
	
	// convert variables array to string:
	$data = array();
	while(list($n,$v) = each($_data)) {
		$data[] = str_replace("+", "%20", urlencode($n)) . "=" . str_replace("+", "%20", urlencode($v));
	}
	// format --> key1=val1&key2=val2 etc.
	$data = implode('&', $data);
	
	// parse the given URL
	$url = parse_url($_url);
	if ($url['scheme'] != 'http' && $url['scheme'] != 'https') {
		die('Invalid protocol specified!');
	}

	// if DP is on https and user is not enforce DP http
	if ($url['scheme'] == 'https' && $_SERVER["HTTPS"] != "on") {
		$url['scheme'] = 'http';
	}
	
	// extract host and path:
	$host = $url['host'];
	$path = "/";
	if (isset($url['path']))
		$path = $url['path'];
	
	// prepare url settings
	$port = "";
	if(isset($url['port']))
		$port = $url['port'];
	$hostPrefix = "";
	if($port == "") {
		$port = 80;
		if ($url['scheme'] == 'https') {
			$port = 443;
			$hostPrefix = "ssl://";
		}
	}
	
	// prepare referrer settings
	$referrer = "";	
	if ($_do_post && isset($_SERVER["HTTP_REFERER"])) {
		$referrer = $_SERVER["HTTP_REFERER"];
	}
	else {
		$refProtocol = "http://";
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$refProtocol = "https://";
		}
		$refPort = "";
		if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" && strlen(strstr($_SERVER["HTTP_HOST"], ":")) == 0) {
			$refPort = ":".$_SERVER["SERVER_PORT"];
		}
		$referrer = $refProtocol . $_SERVER["HTTP_HOST"] . $refPort . $_SERVER["PHP_SELF"];
		if (isset($_SERVER["QUERY_STRING"]) && strlen($_SERVER["QUERY_STRING"]) > 0) {
			$referrer = $referrer . "?" . $_SERVER["QUERY_STRING"];
		}
	}
	
	// open a socket connection on port
	$fp = fsockopen($hostPrefix.$host, $port);
	
	$result = '';
	
	if (isset($fp) && $fp != '') { // if fp is OK
		
		// send the request headers:
		
		if ($_do_post) {
			// as POST
			fputs($fp, "POST $path HTTP/1.1\r\n");
		}
		else {
			// as GET
			$qPrefix = ""; if ($data != "") $qPrefix = "?";
			fputs($fp, "GET $path$qPrefix$data HTTP/1.1\r\n");
		}
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referrer\r\n");
		if ($_do_post) {
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		}
		fputs($fp, "Connection: close\r\n\r\n");
		if ($_do_post) {
			fputs($fp, $data);
		}
		
		while(!feof($fp)) {
			// receive the results of the request
			$result .= fgets($fp, 128);
		}
		
		// close the socket connection:
		fclose($fp);
	}
	
	// split the result header from the content
	$result = explode("\r\n\r\n", $result, 2);
	$header = isset($result[0]) ? $result[0] : '';
	$content = isset($result[1]) ? $result[1] : '';
	
	$header_params = explode("\r\n", $header);
	
	// if redirection allowed
	if(isset($_allowedRedirectsCount) && $_allowedRedirectsCount > 0) { 
	
		// if object moved go to location
		if (isset($header_params[0])
			&& (strlen(strstr($header_params[0],"HTTP/1.0 302"))>0 || strlen(strstr($header_params[0],"HTTP/1.1 302"))>0)
			&& (strpos($header_params[0], "HTTP/1.0 302")==0 || strpos($header_params[0], "HTTP/1.1 302")==0)) {
			
			while(list($n,$v) = each($header_params)) {
				$header_item = explode(": ", $v, 2);
				if (isset($header_item[0]) && $header_item[0]=="Location") {
					
					if (isset($header_item[1])) {
						
						reset($_data);
						
						$tmp_url = strval($header_item[1]);
						$tmp_data = array();
						$new_appkey = "";
						
						if(strlen(strstr($header_item[1], "dp.asp")) > 0 && strlen(strstr($header_item[1], "AppKey=")) > 0) { // if redirect to datapage
							
							$tmp_url = $_url;
							
							$url_items = explode("?", $header_item[1], 2);
							if(isset($url_items[1])) {
								$get_items = explode("&", $url_items[1]);
								while(list($an,$av) = each($get_items)) {
									
									if(strlen(strstr($av, "AppKey=")) > 0) {
										$appkey_items = explode("=", $av);
										if(isset($appkey_items[1])) {
											$new_appkey = $appkey_items[1]; // redirection AppKey found
										}
									}
								}
							}
						}
						
						// if AppKeys not found or they are identical
						if (!isset($new_appkey) || !isset($_data["AppKey"]) 
							|| (isset($new_appkey) && isset($_data["AppKey"]) && $new_appkey == $_data["AppKey"])) {
							$tmp_data = $_data; // pass the same data as input
						}
						else if (isset($new_appkey)) {
							$tmp_data["AppKey"] = $new_appkey; // add new AppKey
						}
						
						$_allowedRedirectsCount = $_allowedRedirectsCount - 1;
						
						// try request new localtion
						return PostRequestEx($tmp_url, $tmp_data, $_do_post, $_allowedRedirectsCount); // post request without further redirection
					}
				}
			}
		}
	}
	
	// if page is OK

	// remove extra characters on start and end of content
	$content = ereg_replace("^[0-9a-fA-F]+\r\n", "", $content);
	$content = ereg_replace("\r\n0\s*[ \t\r\n]*$", "", $content);
	
	return $content;
}

// Load DP function
function dpload($url, $app_key, $dp_cbstyle)
{
	$eString = "<div id=\"cxkg\">Click <a href=\"" . $url . "dp.asp?AppKey=" . $app_key . "\">here</a> to load this <a href=\"http://caspio.com\">Caspio Bridge DataPage</a>.</div>";

	// output error message if other SEO DP alredy deployed on this page
	if(isset($GLOBALS['v_cb_SEO_DP_USED']) && $GLOBALS['v_cb_SEO_DP_USED'] == true) {
		echo $eString;
		return;
	}
	else {
		$GLOBALS['v_cb_SEO_DP_USED'] = true;
	}
	
    $params = array('AppKey' => $app_key);
    $params['ServerDeploy'] = "true";
    $params['cbstyle'] = $dp_cbstyle;
    
    $doPostAddParams = false;
    $doPost = false; // by defaulr GET
	
    if (!isset($_SESSION[$app_key]['v_cb_sesions_list'])) {
		$_SESSION[$app_key]['v_cb_sesions_list'] = "";
    }
    //  if we have anything in the query string which is related to this DP
    if (
		(isset($_GET['appSession']) && strlen(strstr($_SESSION[$app_key]['v_cb_sesions_list'], $_GET['appSession'])) > 0 ) ||
        (isset($_POST['AppKey']) && $_POST['AppKey'] == $app_key)) {
		
		if (isset($_POST) && isset($_POST['AppKey'])) {
			$params = array_merge($params, $_POST);
		}
		else if (isset($_REQUEST)) {
			$params = array_merge($params, $_REQUEST);
		}
		
		if(isset($_POST['AppKey'])) {
			$doPostAddParams = true;
			$doPost = true; // should be sent as POST
		}
    }
    else {
		$doPostAddParams = true;
    }
	
    $addQuery = "";
	$addPost = "";
	$doAddParams = false;
	
	// params support
	if (isset($_SERVER["QUERY_STRING"]) && strlen($_SERVER["QUERY_STRING"]) > 0) {
		
		$as_pos = strpos($_SERVER["QUERY_STRING"], "appSession");
		if ($as_pos > 0) { // if appSession not first param
			$addQuery = substr($_SERVER["QUERY_STRING"], 0, $as_pos);
		}
		else if (is_bool($as_pos) && !$as_pos) { // if no appSession at all
			$addQuery = $_SERVER["QUERY_STRING"] . "&";
		}
		
		$uniqueParamNames = array();
		while (list($k,$v) = each($params)) {
			if (!isset($uniqueParamNames[strtolower($k)])) {
				$uniqueParamNames[strtolower($k)] = true;
			}
		}
		
		if(strlen($addQuery) > 0) {
			$queryArray = explode("&", $addQuery);
			while (list($k,$v) = each($queryArray)) {
				if(strlen($v) > 0) {
					$valArray = explode("=", $v);
					if (!isset($uniqueParamNames[strtolower($valArray[0])])) {
						if (isset($valArray[1]))
							$params[$valArray[0]] = urldecode($valArray[1]);
					}
				}
			}
		}
		
		$addQuery = str_replace(" ", "%20", $addQuery);
        if($addQuery != "") {
			$addPost="?" . substr($addQuery, 0, strlen($addQuery)-1);
		}
    }
    
    $content = PostRequest($url.'dp.asp', $params, $doPost);
    
    // remove outstanding app session cache
    if (isset($_GET['appSession']) && $_SESSION[$app_key]['v_cb_sesions_list'] != "") {
		$sessions = explode('_', $_SESSION[$app_key]['v_cb_sesions_list']);
		$limit = 50; //cache size
		if (count($sessions) > $limit) {
			$_SESSION[$app_key]['v_cb_sesions_list'] = "";
			for($i=count($sessions)-$limit; $i<count($sessions); $i++) {
				if($sessions[$i] != "") {
					$_SESSION[$app_key]['v_cb_sesions_list'] = $_SESSION[$app_key]['v_cb_sesions_list']."_".$sessions[$i];
				}
			}
		}
	}
	
    // App Session search
    $appSess_str = "href\\s*=\\s*\"[^\"]*dp.asp\\?([^\"]*(&|&amp;))?appSession\\s*=\\s*([0-9]*)";
    if (ereg($appSess_str, $content)) {
		ereg($appSess_str, $content, $matches);
		$appSession = $matches[3];
		
		$_SESSION[$app_key]['v_cb_sesions_list'] = $_SESSION[$app_key]['v_cb_sesions_list']."_".$appSession;
	}
	
	// skip query string params if already present in DP links
	if ($addQuery != "")
	{
		$rgx_str = "(href\\s*=\\s*\"[^\"]*dp.asp)\\?([^\"]*)(appSession)";
		ereg($rgx_str, $content, $matches);
		if (isset($matches) && isset($matches[2])) {
			$dpAddQuery = $matches[2];
			if ($dpAddQuery != "" && str_replace("&amp;", "&", $dpAddQuery) == $addQuery) {
				$addQuery = "";
			}
		}
	}	
    
    // a href replacement
    $rgx_str = "href\\s*=\\s*\"[^\"]*dp.asp\\?([^\"]*)\"";
    $content = ereg_replace($rgx_str, "href=\"?" . $addQuery . "\\1\"", $content);
	
    // a href with download param restore
    $rgx_str = "href\\s*=\\s*\"([^\"]*)(&|&amp;)download=1([^\"]*)\"";
    $content = ereg_replace($rgx_str, "href=\"".$url."/dp.asp\\1\\2download=1\\3\"", $content);
    
    // form action replacement
    $protocol = "http://";
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$protocol = "https://";
    }
    $port = "";
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != 443 && strlen(strstr($_SERVER["HTTP_HOST"], ":")) == 0) {
		$port = ":".$_SERVER["SERVER_PORT"];
    }
    $uriPath = '';
    if (isset($_SERVER['REQUEST_URI']))
		$uriPath = $_SERVER['REQUEST_URI'];
	else
		$uriPath = $_SERVER["PHP_SELF"];
    $qst_pos = strpos($uriPath, "?");
	if ($qst_pos !== false) {
		$uriPath = substr($uriPath, 0, $qst_pos);
	}
    $rgx_str = "action\\s*=\\s*\"[^\"]*dp.asp[^\"]*\"";
    $content = ereg_replace($rgx_str, "action=\"" . $protocol . $_SERVER["HTTP_HOST"] . $port . $uriPath . $addPost . "\"", $content);
	
	if($content == '') { // if content is empty - show error
		$content = $eString;
	}
	else { // show DP
	
		global $caspiodeploymentctrl_version;
		
		// if WordPress and caspio-deployment-control plugin > 2.0 installed
		if (isset($caspiodeploymentctrl_version) && $caspiodeploymentctrl_version >= 2)
		{	
			// post buffered content, wrapped by [cb:seo_raw] pseudo tags
			
			$bufferLen = 64000;
			$curPosition = 0;
			echo "[cb:code]";
			while ($curPosition + $bufferLen < strlen($content))
			{
				echo substr($content, $curPosition, $bufferLen);
				echo "[/cb:code]";
				echo "[cb:code]";
				$curPosition = $curPosition + $bufferLen;
			}
			echo substr($content, $curPosition);
			echo "[/cb:code]";
		}
		else // not wordPress
		{
			echo $content;
		}
    }
}

?>