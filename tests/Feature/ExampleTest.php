<?php

it('redirects guests from the home page to login', function () {
    $this->get('/')->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
