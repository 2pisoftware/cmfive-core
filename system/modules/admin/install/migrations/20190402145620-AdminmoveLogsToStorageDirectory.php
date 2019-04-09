<?php


class AdminmoveLogsToStorageDirectory extends CmfiveMigration {

	public function up() {
		// UP
		// move any logs from boiler-plate/logs to boilerplate/storage/logs
		$log_path = ROOT_PATH . '/log';
		$storage_log_path = STORAGE_PATH .'/log';
		if (is_dir($log_path)) {
			// Scan directory
			$dirListing = scandir($log_path);
			if (!empty($dirListing)) {
				// Loop through listing
				foreach ($dirListing as $item) {
					if (!is_dir($log_path . '/' .$item)) {
						// check if file is logfile
						if (strpos($item,'.log') === false) {
							unlink($log_path . '/' . $item);
							continue;
						}
						//check if log for day exists
						if (file_exists($storage_log_path . '/' . $item)) {
							//merge files together
							$file_one = file_get_contents($log_path . '/' . $item);
							$file_two = file_get_contents($storage_log_path . '/' . $item);
							file_put_contents($storage_log_path . '/' . $item,$file_one . "\n" . $file_two);
							unlink($log_path . '/' . $item);
						} else {
							rename($log_path . '/' . $item, $storage_log_path . '/' . $item);
						}
					}
				}
			}
			rmdir($log_path);
		}

	}

	public function down() {
		// DOWN
		// move any logs from boiler-plate/logs to boilerplate/storage/logs
		$log_path = ROOT_PATH . '/log';
		$storage_log_path = STORAGE_PATH .'/log';
		rename($storage_log_path, $log_path);
	}

	public function preText()
	{
		return null;
	}

	public function postText()
	{
		return null;
	}

	public function description()
	{
		return null;
	}
}
