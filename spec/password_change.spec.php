<?php

describe(\HibpCheck\PasswordChange::class, function () {
    beforeEach(function () {
        \WP_Mock::setUp();

        $this->hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class, function ($mock) {
            $mock->shouldReceive('passwordIsPwned');
        });
        $this->passwordChange = new \HibpCheck\PasswordChange($this->hibpApi);
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
                $this->user = \Mockery::mock(\WP_User::class, function ($mock) {
                    $mock->user_pass = null;
                });
            });

            it('does nothing', function () {
                $this->errors = \Mockery::mock(\WP_Error::class, function ($mock) {
                    $mock->shouldReceive('add')
                    ->never();
                });
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });

        context('when API is down', function () {
            beforeEach(function () {
                $this->user = \Mockery::mock(\WP_User::class, function ($mock) {
                    $mock->user_pass = 'good password';
                });
                $this->hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class, function ($mock) {
                    $mock->shouldReceive('passwordIsPwned')
                    ->with('good password')
                    ->andReturn(\Dxw\Result\Result::err('the api is down oh no'));
                });
                $this->passwordChange = new \HibpCheck\PasswordChange($this->hibpApi);
            });

            it('does nothing (and produces warning)', function () {
                $this->errors = \Mockery::mock(\WP_Error::class, function ($mock) {
                    $mock->shouldReceive('add')
                    ->never();
                });
                expect(function () {
                    $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
                })->to->throw(\ErrorException::class, 'API error: the api is down oh no');
            });
        });

        context('when password is good', function () {
            beforeEach(function () {
                $this->user = \Mockery::mock(\WP_User::class, function ($mock) {
                    $mock->user_pass = 'good password';
                });
                $this->hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class, function ($mock) {
                    $mock->shouldReceive('passwordIsPwned')
                    ->with('good password')
                    ->andReturn(\Dxw\Result\Result::ok(false));
                });
                $this->passwordChange = new \HibpCheck\PasswordChange($this->hibpApi);
            });

            it('does nothing', function () {
                $this->errors = \Mockery::mock(\WP_Error::class, function ($mock) {
                    $mock->shouldReceive('add')
                    ->never();
                });
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });

        context('when password is bad', function () {
            beforeEach(function () {
                $this->user = \Mockery::mock(\WP_User::class, function ($mock) {
                    $mock->user_pass = 'bad password';
                });
                $this->hibpApi = \Mockery::mock(\HibpCheck\HibpApi::class, function ($mock) {
                    $mock->shouldReceive('passwordIsPwned')
                    ->with('bad password')
                    ->andReturn(\Dxw\Result\Result::ok(true));
                });
                $this->passwordChange = new \HibpCheck\PasswordChange($this->hibpApi);
            });

            it('complains', function () {
                $this->errors = \Mockery::mock(\WP_Error::class, function ($mock) {
                    $mock->shouldReceive('add')
                    ->once()
                    ->with('hibp-check-found', 'Password has been found in a dump. Please choose another.');
                });
                $this->passwordChange->userProfileUpdateErrors($this->errors, null, $this->user);
            });
        });
    });

    describe('->validatePasswordReset()', function () {
        xit('todo', function () {
        });
    });
});
