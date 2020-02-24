<?php

use Aws\AwsClientInterface;
use Aws\Credentials\CredentialsInterface;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger as Logger;
use Monolog\Formatter\LineFormatter as LineFormatter;
use Monolog\Handler\RotatingFileHandler as RotatingFileHandler;
use Monolog\Processor\WebProcessor as WebProcessor;

defined("LOG_SERVICE_DEFAULT_RETENTION_PERIOD") or define("LOG_SERVICE_DEFAULT_RETENTION_PERIOD", 30);

class LogService extends \DbService
{
    private $loggers = [];
    private $logger;
    private static $system_logger = 'cmfive';
    private $formatter = null;

    private $retention_period = LOG_SERVICE_DEFAULT_RETENTION_PERIOD;

    public function __construct(\Web $w)
    {
        parent::__construct($w);

        $this->retention_period = Config::get('admin.logging.retention_period', LOG_SERVICE_DEFAULT_RETENTION_PERIOD);

        $this->addLogger(LogService::$system_logger);
    }

    public function setFormatter()
    {
        if (empty($this->formatter)) {
            // Add millisecond precision to logs
            $dateFormat = "Y-m-d H:i:s.u";
            $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $this->formatter = new LineFormatter($output, $dateFormat);
        }
    }

    public function logger()
    {
        return $this->loggers['cmfive'];
    }

    public function getLogger($name)
    {
        if (!empty($this->loggers[$name])) {
            return $this->loggers[$name];
        } else {
            return null;
        }
    }

    public function addLogger($name, $logToSystemFile = true)
    {
        if (!empty($this->loggers[$name])) {
            return;
        }

        $this->setFormatter();
        $this->loggers[$name] = new Logger($name);

        switch (Config::get('admin.logging.target', 'file')) {
            case 'aws':
                try {
                    $cw_client = new CloudWatchLogsClient(Config::get('admin.logging.cloudwatch'));

                    // Log group name, will be created if none
                    $cw_group_name = Config::get('admin.logging.cloudwatch.group_name', 'cmfive-app-logs');

                    // Instance ID as log stream name
                    $cw_stream_name_app = Config::get('admin.logging.cloudwatch.stream_name_app', "CmfiveApp");
                    $cw_handler = new CloudWatch($cw_client, $cw_group_name, $cw_stream_name_app, $this->retention_period, 10000);

                    $cw_handler->setFormatter($this->formatter);
                    $this->loggers[$name]->pushHandler($cw_handler);
                    break;
                } catch (Exception $e) {
                    syslog(LOG_ERR, "LogService: exception caught when using Cloudwatch: " . $e->getMessage());
                }
                // If an exception is caught, we should fall back to file (hence why "break" is in the try block)
            case 'file':
            default:
                if ($logToSystemFile === true) {
                    $filename = STORAGE_PATH . "/log/" . LogService::$system_logger . ".log";
                } else {
                    $filename = STORAGE_PATH . "/log/{$name}.log";
                }
                $handler = new RotatingFileHandler($filename, $this->retention_period);
                $handler->setFormatter($this->formatter);
                $this->loggers[$name]->pushHandler($handler);
        }
    }

    public function setLogger($name)
    {
        if (empty($this->loggers[$name])) {
            $this->addLogger($name);
        }

        $this->logger = $this->loggers[$name];
        return $this;
    }

    // Pass on missed calls to the logger (info, error, warning etc)
    public function __call($name, $arguments)
    {
        if (empty($this->logger)) {
            $this->logger = $this->loggers[LogService::$system_logger];
        }

        if ((!empty($arguments[0]) && $arguments[0] === "info") || stristr($name, "err") !== false) {
            // Add the introspection processor if an error (Adds the line/file/class/method from which the log call originated)
            $this->logger->pushProcessor(new WebProcessor());
        }
        $this->logger->$name($arguments[0], ["user" => $this->w->session('user_id')]);

        // In the interest of not breaking system logs, we will return the logger back to cmfive
        // This means for every log that isn't system, the call should look something like this:
        // $w->Log->setLogger('my_log', true|false)->info('Hello, world!');
        $this->logger = $this->loggers[LogService::$system_logger];
    }
}
