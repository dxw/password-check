<?php

describe(\HibpCheck\PasswordChange::class, function () {
    beforeEach(function () {
        \WP_Mock::setUp();

        $this->hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class);
        $this->passwordChange = new \HibpCheck\PasswordChange($this->hibpApi, new \Dxw\Iguana\Value\Post([]));

        $this->mockPasswordChange = function ($return, $__post) {
            $hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class, function ($mock) use ($return) {
                $mock->shouldReceive('passwordIsPwned')
                ->with('password')
                ->andReturn($return);
            });
            $this->passwordChange = new \HibpCheck\PasswordChange($hibpApi, new \Dxw\Iguana\Value\Post($__post));
        };

        $this->mockUser = function ($password) {
            $this->user = \Mockery::mock(\WP_User::class, function ($mock) use ($password) {
                $mock->user_pass = $password;
            });
        };

        $this->mockErrors = function ($args) {
            $this->errors = \Mockery::mock(\WP_Error::class, function ($mock) use ($args) {
                $method = $mock->shouldReceive('add')
                ->times(count($args));

                if (count($args) > 0) {
                    $method->with($args[0][0], $args[0][1]);
                }
            });
        };
    });

    afterEach(function () {
        \WP_Mock::tearDown();
    });

    it('is registerable', function () {
        expect($this->passwordChange)->to->be->instanceof(\Dxw\Iguana\Registerable::class);
    });

    describe('->register()', function () {
        it('registers filters and actions', function () {
            \WP_Mock::expectFilterAdded('user_profile_update_errors', [$this->passwordChange, 'userProfileUpdateErrors'], 20, 3);
            \WP_Mock::expectActionAdded('validate_password_reset', [$this->passwordChange, 'validatePasswordReset'], 20, 2);
            $this->passwordChange->register();
        });
    });

    describe('->userProfileUpdateErrors()', function () {
        context('when password is unset', function () {
            beforeEach(function () {
                $this->mockUser(null);
            });

            it('does nothing', function () {
                $this->mockErrors([]);
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });

        context('when API is down', function () {
            beforeEach(function () {
                $this->mockUser('password');
                $this->mockPasswordChange(\Dxw\Result\Result::err('the api is down oh no'), []);
            });

            it('does nothing (and produces warning)', function () {
                $this->mockErrors([]);
                expect(function () {
                    $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
                })->to->throw(\ErrorException::class, 'API error: the api is down oh no');
            });
        });

        context('when password is good', function () {
            beforeEach(function () {
                $this->mockUser('password');
                $this->mockPasswordChange(\Dxw\Result\Result::ok(false), []);
            });

            it('does nothing', function () {
                $this->mockErrors([]);
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });

        context('when password is bad', function () {
            beforeEach(function () {
                $this->mockUser('password');
                $this->mockPasswordChange(\Dxw\Result\Result::ok(true), []);
            });

            it('complains', function () {
                $this->mockErrors([['hibp-check-found', 'Password has been found in a dump. Please choose another.']]);
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });
    });

    describe('->validatePasswordReset()', function () {
        context('when API is down', function () {
            beforeEach(function () {
                $this->mockUser(null);
                $this->mockPasswordChange(\Dxw\Result\Result::err('the api is down oh no'), [
                    'pass1' => 'password',
                ]);
            });

            it('does nothing (and produces warning)', function () {
                $this->mockErrors([]);
                expect(function () {
                    $this->passwordChange->validatePasswordReset($this->errors, $this->user);
                })->to->throw(\ErrorException::class, 'API error: the api is down oh no');
            });
        });

        context('when password is unset', function () {
            beforeEach(function () {
                $this->mockUser(null);
                $this->mockPasswordChange(\Dxw\Result\Result::ok(false), []);
            });

            it('does nothing', function () {
                $this->mockErrors([]);
                $this->passwordChange->validatePasswordReset($this->errors, $this->user);
            });
        });

        context('when password is good', function () {
            beforeEach(function () {
                $this->mockUser(null);
                $this->mockPasswordChange(\Dxw\Result\Result::ok(false), [
                    'pass1' => 'password',
                ]);
            });

            it('does nothing', function () {
                $this->mockErrors([]);
                $this->passwordChange->validatePasswordReset($this->errors, $this->user);
            });
        });

        context('when password is bad', function () {
            beforeEach(function () {
                $this->mockUser(null);
                $this->mockPasswordChange(\Dxw\Result\Result::ok(true), [
                    'pass1' => 'password',
                ]);
            });

            it('complains', function () {
                $this->mockErrors([['hibp-check-found', 'Password has been found in a dump. Please choose another.']]);
                $this->passwordChange->validatePasswordReset($this->errors, $this->user);
            });
        });
    });
});
