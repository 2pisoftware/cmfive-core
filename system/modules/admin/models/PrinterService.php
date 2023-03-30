<?php

class PrinterService extends DbService {
    
    /**
     * Returns Printer object based on it's ID
     * 
     * @param mixed $printer_id
     * @return <Printer>
     */
    public function getPrinter($printer_id) {
        return $this->getObject("Printer", $printer_id);
    }
    
    public function getPrinters() {
        return $this->getObjects("Printer");
    }
	
	public function getPrinterByName($printer_name) {
		return $this->getObject('Printer', ['name' => $printer_name]);
	}
    
    /**
     * Printjob sends a file to printer based on config rules
     * 
     * @param Printer $printer
     * @param string $file (Path to file to print)
     */
    public function printjob($filename, Printer $printer = null) {
        if (empty($filename)) {
            return;
        }
        
        // Log everywhere
        LogService::getInstance($this->w)->info("Starting print job: {$filename}");
        
        // Load print config
        $config = $this->w->moduleConf('admin', 'printing');
        if (!empty($config["command"])) {
            $command = '';
            // Get command based on OS
            switch (strtolower(substr(PHP_OS, 0, 3))) {
                case "win":
                    if (!empty($config["command"]["windows"])) {
                        $command = $config["command"]["windows"];
                    }
                    break;
                default:
                    if (!empty($config["command"]["unix"])) {
                        $command = $config["command"]["unix"];
                    }
                    break;
            }
            
            // Fill the string with our printer values            
            if (!empty($printer->id)) {
                LogService::getInstance($this->w)->info("Printing to: {$printer->name} with command: {$command}");
                $command = str_replace(array('$filename', '$servername', '$port', '$printername'), 
                    array($filename, escapeshellarg($printer->server), escapeshellarg($printer->port), escapeshellarg($printer->name)),
                    $command);
            } else {
                $command = str_replace('$filename', escapeshellarg($filename), $command);
            }

            // Run the command
            // echo $command . "<br/>";
            $response = shell_exec($command . " 2>&1");
            if (!empty($response)){
                LogService::getInstance($this->w)->info("Shell exec repsonse: {$response}");
//                echo $response;
            }
            
            return $response;
        }
    }
    
}
