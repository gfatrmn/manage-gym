<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminMembershipTest extends TestCase
{
    public function test_membership_route_is_removed(): void
    {
        $response = $this->get('/admin/membership');

        $response->assertNotFound();
    }
}
