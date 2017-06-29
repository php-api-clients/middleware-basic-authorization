<?php declare(strict_types=1);

namespace ApiClients\Middleware\BasicAuthorization;

use ApiClients\Foundation\Middleware\ErrorTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

/**
 * Middleware that adds the authorization header in the token format.
 */
final class BasicAuthorizationHeaderMiddleware implements MiddlewareInterface
{
    use PostTrait;
    use ErrorTrait;

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        if (!isset($options[self::class][Options::USERNAME])) {
            return resolve($request);
        }

        if (empty($options[self::class][Options::USERNAME])) {
            return resolve($request);
        }

        if (!array_key_exists(Options::PASSWORD, $options[self::class])) {
            $options[self::class][Options::PASSWORD] = '';
        }

        return resolve(
            $request->withAddedHeader(
                'Authorization',
                sprintf(
                    'Basic %s',
                    base64_encode(
                        $options[self::class][Options::USERNAME] . ':' .
                        $options[self::class][Options::PASSWORD]
                    )
                )
            )
        );
    }
}
