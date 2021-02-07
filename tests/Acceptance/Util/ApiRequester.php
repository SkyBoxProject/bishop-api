<?php

namespace App\Tests\Acceptance\Util;

use ByJG\ApiTools\AbstractRequester;
use ByJG\Util\Psr7\Request;
use ByJG\Util\Psr7\Response as PsrResponse;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\Stream;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final class ApiRequester extends AbstractRequester
{
    private KernelInterface $kernel;
    private HttpFoundationFactoryInterface $httpFoundationFactory;

    public function __construct(KernelInterface $kernel, HttpFoundationFactoryInterface $httpFoundationFactory)
    {
        parent::__construct();

        $this->kernel = $kernel;
        $this->httpFoundationFactory = $httpFoundationFactory;
    }

    /**
     * @param RequestInterface|Request $request
     *
     * @return PsrResponse
     *
     * @throws \Exception
     */
    protected function handleRequest(RequestInterface $request)
    {
        $serverRequest = new ServerRequest(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            $request->getBody()
        );

        $symfonyRequest = $this->httpFoundationFactory->createRequest($serverRequest);

        $symfonyResponse = $this->kernel->handle($symfonyRequest);

        return $this->createResponse($symfonyResponse);
    }

    private function createResponse(Response $serverResponse): PsrResponse
    {
        $response = new PsrResponse($serverResponse->getStatusCode());

        $response = $response->withBody(new Stream($serverResponse->getContent()));

        $headers = $serverResponse->headers->all();
        $cookies = $serverResponse->headers->getCookies();
        if (!empty($cookies)) {
            $headers['Set-Cookie'] = [];

            foreach ($cookies as $cookie) {
                $headers['Set-Cookie'][] = $cookie->__toString();
            }
        }

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $protocolVersion = $serverResponse->getProtocolVersion();
        $response = $response->withProtocolVersion($protocolVersion);

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function send(): ResponseInterface
    {
        $this->psr7Request = $this->psr7Request
            ->withAddedHeader('Accept', 'application/json')
            ->withAddedHeader('Content-Type', 'application/json');

        return parent::send();
    }
}
