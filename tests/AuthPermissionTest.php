<?php

use BFX\Models\AuthPermission;
use PHPUnit\Framework\TestCase;

class AuthPermissionTest extends TestCase
{
    private $authpermission;

    protected function setUp(): void
    {
        $this->authpermission = new AuthPermission(['test123', 1, 0]);
    }

    public function testKey()
    {
        $this->assertSame('test123', $this->authpermission->getKey());
    }

    public function testRead()
    {
        $this->assertEquals(true, $this->authpermission->getRead());
    }

    public function testWrite()
    {
        $this->assertEquals(false, $this->authpermission->getWrite());
    }
}
