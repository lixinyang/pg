<?php
require_once( dirname(__FILE__).'/t_qq_helper.php' );

class SnsFactory
{
	function getMBApiClient( $MB_AKEY ,$MB_SKEY , $sns_oauth_token , $sns_oauth_token_secret) {
		$c = new \qq\MBApiClient( $MB_AKEY ,$MB_SKEY , $sns_oauth_token , $sns_oauth_token_secret);
		return $c;
	}
	function getMBOpenTOAuth($MB_AKEY ,$MB_SKEY , $sns_oauth_token=null , $sns_oauth_token_secret=null) {
		return new \qq\MBOpenTOAuth( $MB_AKEY , $MB_SKEY , $sns_oauth_token , $sns_oauth_token_secret);;
	}
}
