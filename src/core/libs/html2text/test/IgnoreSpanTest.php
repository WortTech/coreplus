<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lib/Html2Text/Html2Text.php';

class IgnoreSpanTest extends TestCase
{
    public $input =<<< EOT
Outside<span class="_html2text_ignore">Inside</span>
EOT;


    public function testIgnoreSpans()
    {
        $expected_output =<<<EOT
Outside
EOT;

        $html2text = new \Html2Text\Html2Text($this->input);
        $output = $html2text->get_text();

        $this->assertEquals($expected_output, $output);
    }
}
