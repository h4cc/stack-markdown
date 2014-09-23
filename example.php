<?php

/*
* This file is part of the h4cc/stack-markdown package.
*
* (c) Julius Beckmann <github@h4cc.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

require_once(__DIR__.'/vendor/autoload.php');

class App implements \Symfony\Component\HttpKernel\HttpKernelInterface
{
    public function handle(\Symfony\Component\HttpFoundation\Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $response = new \Symfony\Component\HttpFoundation\Response(
            '
# Some Markdown

## Yeah

__phat__ and _kursive_!

## A List

* a
* b
    * c
    * d   ',
            200,
            array(
                'Content-Type' => 'text/x-markdown'
            )
        );

        return $response;
    }
}


$stack = new \h4cc\StackMarkdown\Markdown(new App(), new \Michelf\Markdown());

$response = $stack->handle(new \Symfony\Component\HttpFoundation\Request());

$response->send();

