<?php
class InsightReportInterface
                    
{
    public $title;
    public $header;
    public $data;
    public function __construct(string $title, array $header, array $data)
                    
    
{
        $this->title = $title;
        $hds = [];
        foreach ($header as $hd){
            $hds[$hd] = $hds;
        }
        //$this->header = $header;
        $this->data = $data;
    }
}


