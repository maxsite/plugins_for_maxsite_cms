<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class YaRu {
	var $client_id = 'bc9a7faf69af43c5aa805bce4b54f1f1';
	var $token_url = 'https://oauth.yandex.ru/token';
	var $api_url = 'https://api-yaru.yandex.ru';
	
	var $username;
	var $password;
	var $userid;
	
	function __construct( $username, $password ) {
		$this->username = $username;
		$this->password = $password;
		$this->userid = '';
	}
	
	
	function getToken()
	{
		$postdata = 'grant_type=password&client_id=' . $this->client_id .
		            '&username=' . $this->username . 
					'&password=' . $this->password;
					
		$res = $this->makeRequest( $this->token_url, $postdata);	

		if ( $res !== FALSE ) {
			$res = json_decode( $res );
			$res = (array) $res;
			if ( isset( $res['access_token'] ) and !empty( $res['access_token'] ) )
			{
				return $res['access_token'];
				
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	}
	
	function getUserId($token) {
			$header[] = "GET /me/ HTTP/1.1";
			$header[] = 'Host: api-yaru.yandex.ru';
			$header[] = 'Content-type: application/x-yaru+xml; type=person; charset=utf-8;';
			$header[] = 'Content-length: 0';		
			$res = $this->makeRequest( 'https://api-yaru.yandex.ru/me' . '?oauth_token=' . $token . '&format=json', '', $header);	
			if ( $res !== FALSE ) {
				preg_match( '/urn:ya.ru:person\/[0-9]+/', $res, $matches );
				if ( isset($matches) && !empty( $matches[0]) )
				{	
					$res = explode(  '/', $matches[0] );
					if ( isset( $res ) && !empty( $res[1] ) ) return $res[1]; else return -1;		
				} else {
					return -1;
				}	
			} else {
				return -1;
			}	
	}
	
	function makeRequest( $url, $postdata = '', $header = 0 ) {
	    $curl = curl_init();
	    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
	    //curl_setopt( $curl, CURLOPT_REFERER, $callbackurl );
		
	    curl_setopt( $curl, CURLOPT_HEADER, $header);
	    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
	    
	    curl_setopt( $curl, CURLOPT_URL, $url );

		curl_setopt( $curl, CURLOPT_POST, 1);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $postdata);
			
	 
	    $result = curl_exec( $curl );
		$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE);
		if ( $http_code == 200 || $http_code == 201 || $http_code == 202 )
		{
			
			if ( $header != 0 )
			{ 
				$pos = strpos( $result, 'HTTP/1.0 200 Connection established');
				if( $pos == 0 ) {
					$result = substr($result, 36 );
				}
				$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
				$headers = substr($result, 0, $header_size );
				$body = substr($result, $header_size);
				curl_close( $curl );
				return $body;
			} else 	{
				curl_close( $curl );			
				return $result;	
			}
		} else {
			curl_close( $curl );
			return false;
		}
	}


	
	function makePost( $page_id, $title, $content, $tags, $comments_allow = 1 ) {
		$token = $this->getToken();
		$this->userid =  $this->getUserId( $token );
		if ( $token != -1 )
		{
			$url = $this->api_url . '/person/' . $this->userid . '/post/' . '?oauth_token=' . $token . '&format=json';
			$title = strip_tags(trim( $title ));

			$postdata =  '<?xml version="1.0" encoding="utf-8" ?>' .
						 '<entry xmlns:y="yandex:data" xmlns="http://www.w3.org/2005/Atom">';
			$postdata .= '<category term="text" scheme="urn:ya.ru:posttypes"/>';
			$postdata .= '<y:access>public</y:access>'; 						 
			if ( isset($tags) && !empty($tags)) { 
				foreach ( $tags as $tag ) {
					$postdata .= '<category scheme="https://api-yaru.yandex.ru/person/' . $this->userid . '/tag" term="' . $tag . '"/>';
					
				}
			}
			
			$postdata .= ( $comments_allow ) ? '<y:comments-enabled/>' : '<y:comments-disabled/>'; 
			$postdata .= '<title>' . $title . '</title>' .
						 '<content type="html">' .
						 '<![CDATA[' . $content . ']]>' .
						 '</content>' .
						 '</entry>';
						
			$header[] = "POST /person/$this->userid/post/ HTTP/1.1";
			$header[] = 'Host: api-yaru.yandex.ru';
			$header[] = 'Content-type: application/atom+xml; type=entry; charset=utf-8;';
			$header[] = 'Content-length: ' . mb_strlen($postdata);			
			
			$res = $this->makeRequest( $url, $postdata, $header);	
			$res = json_decode( $res );
			$res = (array) $res;
			if ( isset($res['id']) ) 
			{
				$res = explode('/', $res['id'] );
				return $res[2];
			} else return false;

		} else {
			return false;
		}	
	}
	
	function editPost( $page_id, $title, $content, $postN, $comments_allow = 1 ) {
		$token = $this->getToken();
		if ( $token != -1 && $postN > 0 )
		{
			$url = $this->api_url . '/person/' . $this->username . '/post/' . $postN . '/' . '?oauth_token=' . $token . '&format=json';
			$title = strip_tags(trim( $title ));
			
			$postdata =  '<?xml version="1.0" encoding="utf-8" ?>' .
						 '<entry xmlns:y="yandex:data" xmlns="http://www.w3.org/2005/Atom">' .
						 '<category term="text" scheme="urn:ya.ru:posttypes"/>' .
						 '<y:access>public</y:access>'; 
			$postdata .= ( $comments_allow ) ? '<y:comments-enabled/>' : '<y:comments-disabled/>'; 
			$postdata .= '<title>' . $title . '</title>' .
						 '<content type="html">' .
						 '<![CDATA[' . $content . ']]>' .
						 '</content>' .
						 '</entry>';

			$header[] = "DELETE /person/$this->username/post/$postN HTTP/1.1";
			$header[] = 'Host: api-yaru.yandex.ru';
			$header[] = 'Content-type: application/atom+xml; type=entry; charset=utf-8;';
			$header[] = 'Content-length: ' . mb_strlen($postdata);			
			$res = $this->makeRequest( $url, $postdata, $header);
			// узнаем ответ ( 201 ) и получим ID поста в я.ру
			$res = json_decode( $res );
			$res = (array) $res;
						 
		} else {
			return false;
		}
	}
}

	
	


?>
