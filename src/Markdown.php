<?php

/*
* This file is part of the h4cc/stack-markdown package.
*
* (c) Julius Beckmann <github@h4cc.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace h4cc\StackMarkdown;

use Michelf\MarkdownInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Markdown implements HttpKernelInterface
{
    private $app;
    private $markdown;
    private $contentTypes;

    public function __construct(HttpKernelInterface $app, MarkdownInterface $markdown = null, array $contentTypes = array('text/markdown', 'text/x-markdown'))
    {
        $this->app = $app;
        $this->markdown = ($markdown) ? $markdown : new \Michelf\Markdown();
        $this->contentTypes = $contentTypes;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $response = $this->app->handle($request, $type, $catch);

        if ($this->needToTransformResponse($response)) {
            $response = $this->transformResponse($response);
        }

        return $response;
    }

    private function needToTransformResponse(Response $response)
    {
        if(!$this->contentTypes) {
            // No content type means, "transform all".
            return true;
        }

        $contentType = $response->headers->get('Content-Type');

        if(false !== stripos($contentType, ';')) {
            list($contentType, $encoding) = explode(';', $contentType, 2);
        }

        return in_array(trim($contentType), $this->contentTypes);
    }

    private function transformResponse(Response $response)
    {
        $newResponse = new Response(
            $this->markdown->transform($response->getContent()),
            $response->getStatusCode(),
            $response->headers->all()
        );

        $newResponse->headers->set('Content-Type', 'text/html');

        return $newResponse;
    }
}
