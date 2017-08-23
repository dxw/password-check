<?php

describe(\HibpCheck\HibpApi::class, function () {
    beforeEach(function () {
        \WP_Mock::setUp();

        $this->mockRequestAndReturn = function ($return) {
            \WP_Mock::wpFunction('wp_remote_get', [
                'args' => [
                    'https://haveibeenpwned.com/api/v2/pwnedpassword/'.sha1($this->password).'?originalPasswordIsAHash=true',
                ],
                'return' => $return,
            ]);
        };

        $this->mockIsWpErrorAndReturn = function ($return) {
            \WP_Mock::wpFunction('is_wp_error', [
                'return' => $return,
            ]);
        };
    });

    afterEach(function () {
        \WP_Mock::tearDown();
    });

    context('(default URL)', function () {
        beforeEach(function () {
            $this->hibpApi = new \HibpCheck\HibpApi();
        });

        describe('->passwordIsPwned()', function () {
            context('with a pwned password', function () {
                beforeEach(function () {
                    $this->password = 'password';
                    $this->mockRequestAndReturn([
                        'response' => ['code' => 200],
                    ]);
                    $this->mockIsWpErrorAndReturn(false);
                });

                it('returns true', function () {
                    $result = $this->hibpApi->passwordIsPwned($this->password);
                    expect($result->isErr())->to->be->false();
                    expect($result->unwrap())->to->equal(true);
                });
            });

            context('with a good password', function () {
                beforeEach(function () {
                    $this->password = "hello this is a rather good password don't you think?";
                    $this->mockRequestAndReturn([
                        'response' => ['code' => 404],
                    ]);
                    $this->mockIsWpErrorAndReturn(false);
                });

                it('returns true', function () {
                    $result = $this->hibpApi->passwordIsPwned($this->password);
                    expect($result->isErr())->to->be->false();
                    expect($result->unwrap())->to->equal(false);
                });
            });

            context('when the API is broken', function () {
                beforeEach(function () {
                    $this->password = 'password';
                    $error = \Mockery::mock(\WP_Error::class, function ($mock) {
                        $mock->shouldReceive('get_error_message')
                        ->andReturn('A valid URL was not provided.');
                    });
                    $this->mockRequestAndReturn($error);
                    $this->mockIsWpErrorAndReturn(true);
                });

                it('returns error', function () {
                    $result = $this->hibpApi->passwordIsPwned($this->password);
                    expect($result->isErr())->to->be->true();
                    expect($result->getErr())->to->equal('A valid URL was not provided.');
                });
            });
        });
    });
});