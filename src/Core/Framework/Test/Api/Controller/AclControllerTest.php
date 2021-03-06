<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\Api\Controller;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Acl\Event\AclGetAdditionalPrivilegesEvent;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Test\TestCaseBase\AdminFunctionalTestBehaviour;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Response;

class AclControllerTest extends TestCase
{
    use AdminFunctionalTestBehaviour;

    public function testGetPrivileges(): void
    {
        Feature::skipTestIfInActive('FEATURE_NEXT_3722', $this);
        $this->getBrowser()->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/_action/acl/privileges');
        $response = $this->getBrowser()->getResponse();
        $privileges = json_decode($response->getContent(), true);

        static::assertContains('unit:read', $privileges);
        static::assertContains('system:clear:cache', $privileges);
    }

    public function testGetAdditionalPrivileges(): void
    {
        Feature::skipTestIfInActive('FEATURE_NEXT_3722', $this);
        $this->getBrowser()->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/_action/acl/additional_privileges');
        $response = $this->getBrowser()->getResponse();
        $privileges = json_decode($response->getContent(), true);

        static::assertNotContains('unit:read', $privileges);
        static::assertContains('system:clear:cache', $privileges);
    }

    public function testGetAdditionalPrivilegesEvent(): void
    {
        Feature::skipTestIfInActive('FEATURE_NEXT_3722', $this);

        $getAdditionalPrivileges = function (AclGetAdditionalPrivilegesEvent $event): void {
            $privileges = $event->getPrivileges();
            static::assertContains('system:clear:cache', $privileges);
            $privileges[] = 'my_custom_privilege';
            $event->setPrivileges($privileges);
        };
        $this->getContainer()->get('event_dispatcher')->addListener(AclGetAdditionalPrivilegesEvent::class, $getAdditionalPrivileges);

        $this->getBrowser()->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/_action/acl/additional_privileges');
        $response = $this->getBrowser()->getResponse();
        $privileges = json_decode($response->getContent(), true);

        static::assertNotContains('unit:read', $privileges);
        static::assertContains('system:clear:cache', $privileges);
        static::assertContains('my_custom_privilege', $privileges);
    }

    public function testGetAdditionalPrivilegesNoPermission(): void
    {
        Feature::skipTestIfInActive('FEATURE_NEXT_3722', $this);
        try {
            $this->authorizeBrowser($this->getBrowser(), [], []);
            $this->getBrowser()->request('GET', '/api/v' . PlatformRequest::API_VERSION . '/_action/acl/additional_privileges');
            $response = $this->getBrowser()->getResponse();

            static::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode(), $response->getContent());
        } finally {
            $this->resetBrowser();
        }
    }
}
