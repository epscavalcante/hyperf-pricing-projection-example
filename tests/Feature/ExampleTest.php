<?php

test('example', function () {
    $result = $this->client->get('/');

    expect(true)->toBeTrue();
})->skip();
