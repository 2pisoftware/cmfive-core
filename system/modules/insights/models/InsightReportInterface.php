<?php
class InsightReportInterface
{
    public $title;
    public $header;
    public $data;
    public function __construct(string $title, array $header, array $data)
    {
        $this->title = $title;
        $this->header = $header;
        $this->data = $data;
    }
}
