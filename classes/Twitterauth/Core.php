<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitterauth_Core extends TwitterOAuth {
	public $request_token = array();
	public static $token_name = 'twitterauth_tokens';
	public $config;

	function __construct($consumer_key = NULL, $consumer_secret = NULL, $oauth_token = NULL, $oauth_token_secret = NULL)
	{
		$this->config = Kohana::$config->load('twitterauth');

		parent::__construct($this->config->consumer_key, $this->config->consumer_secret);
	}

	public static function factory()
	{
		return new self();
	}

	function init()
	{
		if (!isset($_SESSION[self::$token_name]))
		{
			$this->request_token = $this->oauth('oauth/request_token', ['oauth_callback' => URL::site('account/twittercb', Request::$current)]);
			$_SESSION[self::$token_name] = $this->request_token;
			$this->setOauthToken($this->request_token['oauth_token'], $this->request_token['oauth_token']);
		}
		else
		{
			$token = $_SESSION[self::$token_name];
			$this->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);
		}

		return $this;
	}

	/**
	 * @param null $token
	 * @return string
	 * @throws \Kohana_Exception
	 */
	function getAuthorizeURL($token = null)
	{
		$_SESSION['twitterauth_url'] = URL::site(Request::$current->detect_uri(), Request::$current);
		$oauth_token = $token ? : $this->request_token['oauth_token'];
		return $this->url('oauth/authorize', ['oauth_token' => $oauth_token]);
	}

	/**
	 * The access token will contain the following:
	 * - oauth_token
	 * - oauth_token_secret
	 * - user_id
	 * - screen_name
	 * - x_auth_expires
	 * @param null $oauth_verifier
	 * @throws \Abraham\TwitterOAuth\TwitterOAuthException
	 */
	function getAccessToken($oauth_verifier = null)
	{
		$access_token = $this->oauth('oauth/access_token', ['oauth_verifier' => $oauth_verifier]);
		$_SESSION[self::$token_name] = $access_token;
		$this->setOauthToken($access_token['oauth_token'], $access_token['oauth_token_secret']);
	}

	/**
	 * @return array|bool|object
	 */
	public function getUser()
	{
		$user = $this->get('account/verify_credentials');
		if (!empty($user) && !empty($user->id))
		{
			return $user;
		}
		return false;
	}

}
