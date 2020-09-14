<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;

class FileParser
{
    public $config = [];
    public $filesystem = null;
    public $files = [];
    public $folders = [];

    public function __construct($w, $channel_name)
    {
        $this->config = ChannelService::getInstance($w)->getConfig($channel_name);

        // Load config data
        if (!empty($this->config)) {
            $adapter = new LocalAdapter($this->config["path"]);
            $this->filesystem = new Filesystem($adapter, false);

            // Load directory listing into class (try/catch because permission may be denied)
            try {
                $dir_listing = $this->filesystem->listKeys();
                if (array_key_exists("keys", $dir_listing)) {
                    $this->files = $dir_listing["keys"];
                }
                if (array_key_exists("dirs", $dir_listing)) {
                    $this->folders = $dir_listing["dirs"];
                }

                // Remove file entry if inside another directory
                for ($i = 0; $i < count($this->files); $i++) {
                    echo $this->files[$i] . "<br/>";
                    if (strpos($this->files[$i], "/") !== false) {
                        unset($this->files[$i]);
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function parseDirectory()
    {
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                // Do something with the files
                echo "$file<br/>";
            }
        }
        // This isnt working property yet
        die();
        if (!empty($this->config["mode"])) {
            switch (strtolower($this->config["mode"])) {
                case "archive":
                    foreach ($this->files as $file) {
                        $this->filesystem->write("archive/" . time() . $file, $this->filesystem->read($file), false);
                        $this->filesystem->delete($file);
                    }
                    break;
                case "delete":
                    foreach ($this->files as $file) {
                        $this->filesystem->delete($file);
                    }
                    break;
            }
        }
        return count($this->files);
    }
}
