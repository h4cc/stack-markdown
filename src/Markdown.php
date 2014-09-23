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

        if($this->isMarkdownResponse($response)) {
            $response = $this->transformResponse($response);
        }

        return $response;
    }

    private function isMarkdownResponse(Response $response)
    {
        return in_array($response->headers->get('Content-Type'), $this->contentTypes);
    }

    private function transformResponse(Response $response)
    {
        $htmlContent = $this->markdown->transform($response->getContent());

        $response->setContent($htmlContent);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}
