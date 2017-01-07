<?php

namespace App\Services;

use Socialite;
use Google_Client;
use Google_Service_Oauth2;
use App\Repositories\UserRepository;

class AuthManager
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function redirectFacebook()
	{
		return Socialite::driver('facebook')->redirect();
	}

	public function redirectGoogle()
	{
		return redirect($this->googleClient()->createAuthUrl());
	}

	public function callbackFacebook()
	{
		$user = Socialite::driver('facebook')->user();

		dd($user);
	}

	/**
	 * @param $code string The authorization code.
	 */
	public function callbackGoogle($code)
	{
		$this->googleClient()->authenticate($code);
		$googleAuth = new Google_Service_Oauth2($this->googleClient());
		$googleUser = $googleAuth->userinfo->get();

		$user = $this->userRepository->getByEmail($googleUser->email);
		if ( ! $user) {
		}
	}

	/**
	 * @return Google_Client
	 */
	private function googleClient()
	{
		static $google = null;
		if ($google === null) {
			$google = new Google_Client();
			$google->setClientId(env('GOOGLE_CLIENT_ID'));
			$google->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
			$google->setRedirectUri(env('GOOGLE_REDIRECT'));
			$google->setScopes(['email', 'profile']);
		}
		return $google;
	}
}