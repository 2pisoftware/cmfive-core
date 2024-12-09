<?php

class MenuLinkStruct
{
    public function __construct(
        public string $title,
        public string $url,
        public MenuLinkType $type = MenuLinkType::Link,
    ) {
        if ($url[0] !== '/') {
            $this->url = '/'.$url;
        }
    }
}
