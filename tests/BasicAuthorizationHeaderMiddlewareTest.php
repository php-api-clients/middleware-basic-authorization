<?php declare(strict_types=1);

namespace ApiClients\Tests\Middleware\BasicAuthorization;

use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use RingCentral\Psr7\Request;
use ApiClients\Middleware\BasicAuthorization\BasicAuthorizationHeaderMiddleware;
use ApiClients\Middleware\BasicAuthorization\Options;
use function Clue\React\Block\await;

final class BasicAuthorizationHeaderMiddlewareTest extends TestCase
{
    public function preProvider()
    {
        yield [
            [],
            false,
            ''
        ];

        yield [
            [
                BasicAuthorizationHeaderMiddleware::class => [
                    Options::USERNAME => '',
                ],
            ],
            false,
            ''
        ];

        yield [
            [
                BasicAuthorizationHeaderMiddleware::class => [
                    Options::USERNAME => null,
                ],
            ],
            false,
            ''
        ];

        yield [
            [
                BasicAuthorizationHeaderMiddleware::class => [
                    Options::USERNAME => 'kroket',
                ],
            ],
            true,
            'Basic a3Jva2V0Og=='
        ];
        yield [
            [
                BasicAuthorizationHeaderMiddleware::class => [
                    Options::USERNAME => 'kroket',
                    Options::PASSWORD => 'password',
                ],
            ],
            true,
            'Basic a3Jva2V0OnBhc3N3b3Jk'
        ];
    }

    /**
     * @dataProvider preProvider
     */
    public function testPre(array $options, bool $hasHeader, string $expectedHeader)
    {
        $request = new Request('GET', 'https://example.com/');
        $middleware = new BasicAuthorizationHeaderMiddleware();
        $changedRequest = await($middleware->pre($request, $options), Factory::create());

        if ($hasHeader === false) {
            self::assertFalse($changedRequest->hasHeader('Authorization'));
            return;
        }

        self::assertTrue($changedRequest->hasHeader('Authorization'));
        self::assertSame($expectedHeader, $changedRequest->getHeaderLine('Authorization'));
    }
}
