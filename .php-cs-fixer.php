<?php

$header = <<<EOF
This file is part of OpenSolid package.

(c) Yonel Ceruto <open@yceruto.dev>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'header_comment' => ['header' => $header],
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
