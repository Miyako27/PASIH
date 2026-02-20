<?php

it('redirects guest users from root to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
