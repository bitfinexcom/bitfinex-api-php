<?php

use BFX\Models\AuthPermission;
use PHPUnit\Framework\TestCase;

class AuthPermissionTest extends TestCase
{
    private $authpermission;

    protected function setUp(): void
    {
        $this->authpermission = new AuthPermission([
            0 => 'test123',
            1 => true,
            2 => false,
        ]);
    }

    public function testKey()
    {
        $this->assertEquals('test123', $this->authpermission->getKey());
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
