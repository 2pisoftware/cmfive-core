<?php

use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testSanitise()
    {
        require_once("system/web.php");
        $w = new Web();

        $table = [
            // Tags that shouldn't be stripped.
            ["string" => "This is a string", "want" => "This is a string"],
            ["string" => "<a>This is a string</a>", "want" => "<a>This is a string</a>"],
            ["string" => "<blockquote>This is a string</blockquote>", "want" => "<blockquote>This is a string</blockquote>"],
            ["string" => "<em>This is a string</em>", "want" => "<em>This is a string</em>"],
            ["string" => "<div>This is a string</div>", "want" => "<div>This is a string</div>"],
            ["string" => "<h1>This is a string</h1>", "want" => "<h1>This is a string</h1>"],
            ["string" => "<h3>This is a string</h3>", "want" => "<h3>This is a string</h3>"],
            ["string" => "<h4>This is a string</h4>", "want" => "<h4>This is a string</h4>"],
            ["string" => "<h5>This is a string</h5>", "want" => "<h5>This is a string</h5>"],
            ["string" => "<h6>This is a string</h6>", "want" => "<h6>This is a string</h6>"],
            ["string" => "<li>This is a string</li>", "want" => "<li>This is a string</li>"],
            ["string" => "<ol>This is a string</ol>", "want" => "<ol>This is a string</ol>"],
            ["string" => "<p>This is a string</p>", "want" => "<p>This is a string</p>"],
            ["string" => "<strong>This is a string</strong>", "want" => "<strong>This is a string</strong>"],
            ["string" => "<s>This is a string</s>", "want" => "<s>This is a string</s>"],
            ["string" => "<u>This is a string</u>", "want" => "<u>This is a string</u>"],
            ["string" => "<ul>This is a string</ul>", "want" => "<ul>This is a string</ul>"],
            // Tags that should be stripped.
            ["string" => "<script>This is a string</script>", "want" => "This is a string"],
            ["string" => "<section>This is a string</section>", "want" => "This is a string"],
        ];

        foreach ($table as $t) {
            $this->assertEquals($t["want"], Html::sanitise($t["string"]));
        }
    }
}