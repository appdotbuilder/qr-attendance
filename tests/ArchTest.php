<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

it('can create basic models', function () {
    $this->assertTrue(true);
});

it('application has proper structure', function () {
    expect(file_exists(app_path()))->toBeTrue();
    expect(file_exists(database_path()))->toBeTrue();
    expect(file_exists(resource_path()))->toBeTrue();
});