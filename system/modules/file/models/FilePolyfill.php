<?php

class FilePolyfill {
    protected $key;
    protected $filesystem;

    /**
     * Content variable is lazy. It will not be read from filesystem until it's requested first time.
     *
     * @var mixed content
     */
    protected $content = null;

    /**
     * @var array metadata in associative array. Only for adapters that support metadata
     */
    protected $metadata = null;

    /**
     * Human readable filename (usually the end of the key).
     *
     * @var string name
     */
    protected $name = null;

    /**
     * File size in bytes.
     *
     * @var int size
     */
    protected $size = 0;

    /**
     * File date modified.
     *
     * @var int mtime
     */
    protected $mtime = null;

    /**
     * @param string     $key
     * @param \League\Flysystem\Filesystem $filesystem
     */
    public function __construct($key, \League\Flysystem\Filesystem $filesystem)
    {
        $this->key = $key;
        $this->name = $key;
        $this->filesystem = $filesystem;
    }

    /**
     * Returns the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the content.
     *
     * @throws FileNotFound
     *
     * @param array $metadata optional metadata which should be set when read
     *
     * @return string
     */
    public function getContent($metadata = [])
    {
        if (isset($this->content)) {
            return $this->content;
        }
        // $this->setMetadata($metadata);

        return $this->content = $this->filesystem->read($this->key);
    }

    /**
     * @return string name of the file
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int size of the file
     */
    public function getSize()
    {
        if ($this->size) {
            return $this->size;
        }

        return $this->size = $this->filesystem->fileSize($this->getKey());
    }

    /**
     * Returns the file modified time.
     *
     * @return int
     */
    public function getMtime()
    {
        return $this->mtime = $this->filesystem->lastModified($this->key);
    }

    /**
     * @param int $size size of the file
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Sets the content.
     *
     * @param string $content
     * @param array  $metadata optional metadata which should be send when write
     *
     * @return int The number of bytes that were written into the file, or
     *             FALSE on failure
     */
    public function setContent($content, $metadata = [])
    {
        $this->content = $content;
        // $this->setMetadata($metadata);

        return $this->size = $this->filesystem->write($this->key, $this->content);
    }

    /**
     * @param string $name name of the file
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Indicates whether the file exists in the filesystem.
     *
     * @return bool
     */
    public function exists()
    {
        // echo "{$this->key} - " . ($this->filesystem->fileExists($this->key) ? 'exists' : 'not exists');
        return $this->filesystem->fileExists($this->key);
    }

    /**
     * Deletes the file from the filesystem.
     *
     * @throws FileNotFound
     * @throws \RuntimeException when cannot delete file
     *
     * @param array $metadata optional metadata which should be send when write
     *
     * @return bool TRUE on success
     */
    public function delete($metadata = [])
    {
        // $this->setMetadata($metadata);

        return $this->filesystem->delete($this->key);
    }

    /**
     * Creates a new file stream instance of the file.
     *
     * @return Stream
     */
    public function createStream()
    {
        return $this->filesystem->readStream($this->key);
    }

    /**
     * Rename the file and move it to its new location.
     *
     * @param string $newKey
     */
    public function rename($newKey)
    {
        $this->filesystem->move($this->key, $newKey);

        $this->key = $newKey;
    }

    /**
     * Sets the metadata array to be stored in adapters that can support it.
     *
     * @param array $metadata
     *
     * @return bool
     */
    // protected function setMetadata(array $metadata)
    // {
    //     if ($metadata && $this->supportsMetadata()) {
    //         $this->filesystem->getAdapter()->setMetadata($this->key, $metadata);

    //         return true;
    //     }

    //     return false;
    // }

    // /**
    //  * @return bool
    //  */
    // private function supportsMetadata()
    // {
    //     return $this->filesystem->getAdapter() instanceof MetadataSupporter;
    // }
}
