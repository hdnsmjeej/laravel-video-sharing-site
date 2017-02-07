<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Generator;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Repositories\UserRepository;

class AccountTest extends DuskTestCase
{
	/**
	 * A basic browser test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$repository = $this->app->make(UserRepository::class);
		$repository->create([
			'email' => 'blah@blah.com'
		]);

		$this->assertNotEmpty($repository->getByEmail('blah@blah.com'));
	}

	/**
	 * A basic browser test example.
	 *
	 * @return void
	 */
	public function testBasicExample2()
	{
		$repository = $this->app->make(UserRepository::class);

		$this->assertEmpty($repository->getByEmail('blah@blah.com'));
	}

	public function testAccountNormalUser()
	{
		$this->browse(function(Browser $browser) {
//			$userRepository = $this->app->make(UserRepository::class);
//
//			$user = $userRepository->getByEmail('user@test.com');
//
//			$generator = $this->app->make(Generator::class);
//
//			$email = 'newemail@test.com';
//			$firstName = $generator->firstName;
//			$lastName = $generator->lastName;

			$browser->visit(route('account.basics'))
				->assertPathIs(route('home', [], false));
		});
	}
}
