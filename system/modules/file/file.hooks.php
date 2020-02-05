<?php

function file_admin_extra_navigation_items(Web $w)
{
    if ($w->Auth->user()->is_admin == 1) {
        return [
            $w->menuLink("file-admin", "File transfer"),
            $w->menuLink("file/deletedfiles", "Deleted files")
        ];
    }
}

/**
 * Remove all files inside this runtime cache's temp directory.
 *
 * @param Web $w
 * @return void
 */
function file_core_web_cleanup(Web $w)
{
    $directory_path = ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::TEMP_PATH . "/" . FileService::$temp_file_parent_directory;

    if (!file_exists($directory_path)) {
        return;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory_path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files ?? [] as $file) {
        try {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        } catch (Throwable $t) {
            $w->Log->setLogger("FILE")->error("Failed to remove directory at {$directory_path}: " . $t->getMessage());
        }
    }

    rmdir($directory_path);
}
