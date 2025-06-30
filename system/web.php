<?php

// ========= Session ========================
if (!headers_sent()) {
    ini_set('session.gc_maxlifetime', 21400);
}

//========== Constants =====================================
const ENVIRONMENT_DEVELOPMENT = "development";
const ENVIRONMENT_PRODUCTION = "production";

defined("DS") || define("DS", DIRECTORY_SEPARATOR);

define("ROOT_PATH", str_replace("\\", "/", getcwd()));
define("SYSTEM_PATH", str_replace("\\", "/", getcwd() . '/system'));

define("LIBPATH", str_replace("\\", "/", getcwd() . '/lib'));
define("SYSTEM_LIBPATH", str_replace("\\", "/", getcwd() . '/system/lib'));
define("FILE_ROOT", str_replace("\\", "/", getcwd() . "/uploads/")); // dirname(__FILE__)
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__) . "/../media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("STORAGE_PATH", str_replace("\\", "/", getcwd() . '/storage'));
define("SESSION_NAME", "CM5-SID");

set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_LIBPATH);

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/html.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/classes/CSRF.php";
require_once __DIR__ . "/classes/Config.php";
require_once __DIR__ . "/classes/History.php";

// Load system Composer autoloader
if (file_exists(ROOT_PATH . "/composer/vendor/autoload.php")) {
    require ROOT_PATH . "/composer/vendor/autoload.php";
} elseif (file_exists(SYSTEM_PATH . "/composer/vendor/autoload.php")) {
    require SYSTEM_PATH . "/composer/vendor/autoload.php";
}

class PermissionDeniedException extends Exception
{
}

/**
 * The Web class is the heart of Cmfive, it manages request routing, database connections,
 * templating, configurations, lifecycle hooks, access security, static file registration,
 * to name a few
 *
 * Originally based of the WebPy Python framework, the Web class has features like implied
 * routing instead of the conventional declarative routing. See how this works, and more at
 * <https://cmfive.com/docs>
 */
class Web
{
    public string $_buffer = '';
    public $_template = null;
    public $_templatePath;
    public $_templateExtension;
    public $_url;
    public $_context = [];
    public $_action;
    public $_defaultHandler;
    public $_defaultAction;
    public $_layoutContentMarker;
    public $_notFoundTemplate;
    public $_fatalErrorTemplate;
    public ?string $_layout;
    public $_headers;
    public ?string $_module;
    public ?string $_submodule;
    public $_modulePath;
    public $_moduleExtension;
    public $_modules;
    public array $_hooks = [];
    public string $_requestMethod = '';
    public bool $_action_executed = false;
    public bool $_action_redirected = false;
    public $_services;
    public array $_paths = [];
    public $_loginpath = 'auth/login';
    public $_is_mfa_enabled_path = "auth/ajax_is_mfa_enabled";
    public $_partialsdir = "partials";
    public $db;
    public $_isFrontend = false;
    public $_isPortal = false;
    public $_is_head_request = false;
    public $_languageModulesLoaded = [];
    public $currentLocale = '';
    public $_module_loaded_hooks = []; //cache loaded module hook files
    private $_classdirectory; // used by the class auto loader

    public $_scripts = [];
    public $_styles = [];
    public $sHttps = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templatePath = "templates";
        $this->_templateExtension = ".tpl.php";
        $this->_action = null;
        $this->_defaultHandler = "main";
        $this->_defaultAction = "index";
        $this->_layoutContentMarker = "body";
        $this->_notFoundTemplate = "404";
        $this->_paths = [];
        $this->_services = [];
        $this->_layout = "layout";
        $this->_headers = null;
        $this->_module = null;
        $this->_submodule = null;
        $this->_hooks = [];

        $this->checkStorageDirectory();

        // look at using schema independent url's with '//' notation - test using Apache/Nginx
        $this->_webroot = $this->getUrlSchema() . $this->getHostname();

        $this->_actionMethod = null;

        // The order of the following three lines are important
        spl_autoload_register([$this, 'modelLoader']);
        spl_autoload_register([$this, 'componentLoader']);

        defined("WEBROOT") || define("WEBROOT", $this->_webroot);

        $this->loadConfigurationFiles();

        // If a domain whitelist has been set then implement it and forbid any request that does not match a domain given
        $domain_whitelist = Config::get('system.domain_whitelist');
        if (!empty($domain_whitelist)) {
            if (!in_array($this->getHostName(), $domain_whitelist)) { // @todo: test this
                $this->header('HTTP/1.0 403 Forbidden');
                exit();
            }
        }

        clearstatcache();
    }

    private function getUrlSchema()
    {
        $sHttps = $this->getRequestHeader('HTTPS', 'off');
        $sHttpXproto = $this->getRequestHeader('HTTP_X_FORWARDED_PROTO');
        $sHttpXssl = $this->getRequestHeader('HTTP_X_FORWARDED_SSL');

        // if using IIS then value is "off" for non ssl requests
        if ((strtolower($sHttps) !== "off") || (strtolower($sHttpXproto) == "https") || (strtolower($sHttpXssl) == "on")) {
            return "https://";
        }
        return "http://";
    }

    private function getHostname()
    {
        return $this->getRequestHeader("HTTP_X_FORWARDED_HOST", $this->getRequestHeader('HTTP_HOST'));
    }

    private function getRequestHeader($header, $default = '')
    {
        if (array_key_exists($header, $_SERVER) && !empty($_SERVER[$header])) {
            return $_SERVER[$header];
        }
        return $default;
    }

    private function checkStorageDirectory()
    {
        if (!is_dir(STORAGE_PATH)) {
            mkdir(STORAGE_PATH);
        }
        if (!is_dir(STORAGE_PATH . '/session')) {
            mkdir(STORAGE_PATH . "/session");
        }
    }

    private function modelLoader($className)
    {
        // Build a call trace for the cache file
        $callinfo = debug_backtrace();

        $cause = '';
        foreach ($callinfo ?? [] as $detailed) {
            $cause .= ($cause ? " -> " : '') . $detailed['function'];
        }

        // 1. check if class directory has to be loaded from cache
        $classdirectory_cache_file = ROOT_PATH . "/cache/classdirectory.cache";

        if (empty($this->_classdirectory) && file_exists($classdirectory_cache_file)) {
            require_once $classdirectory_cache_file;
        }

        // 2. if filename is stored in $this->_classdirectory
        if (!empty($this->_classdirectory[$className])) {
            if (file_exists($this->_classdirectory[$className])) {
                require_once $this->_classdirectory[$className];
                return true;
            }
        }

        // 3. class has to be found the hard way
        $modules = $this->modules();

        // create the class cache file
        if (!file_exists($classdirectory_cache_file)) {
            file_put_contents($classdirectory_cache_file, "<?php\n");
        }
        foreach ($modules as $model) {
            // Check if the hosting module is active before we autoload it
            if (Config::get("{$model}.active") !== true) {
                continue;
            }

            // Try a lower case version
            $file = $this->getModuleDir($model) . 'models/' . $className . ".php";
            if (file_exists($file)) {
                require_once $file;
                // add this class file to the cache file
                file_put_contents($classdirectory_cache_file, '// ' . $cause . "\n" . '$this->_classdirectory["' . $className . '"]="' . $file . '";' . "\n\n", FILE_APPEND);
                $this->_classdirectory[$className] = $file;
                return true;
            }

            $namespace_parts = explode('\\', $className);
            $class_file = array_pop($namespace_parts) . '.php';

            $top_directory = new \RecursiveDirectoryIterator($this->getModuleDir($model) . 'models/', \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::SKIP_DOTS);
            $class_filter = new \RecursiveCallbackFilterIterator($top_directory, function ($current, $key, $iterator) {
                return $current->isDir() || $current->getExtension() === "php";
            });

            foreach (new \RecursiveIteratorIterator($class_filter) as $info) {
                if ($info->getFilename() != $class_file) {
                    continue;
                }

                require_once $info->getPathname();
                file_put_contents($classdirectory_cache_file, '// ' . implode("\\", $namespace_parts) . " " . $cause . "\n" . '$this->_classdirectory["' . $className . '"]="' . $info->getPathname() . '";' . "\n\n", FILE_APPEND);
                $this->_classdirectory[$className] = $info->getPathname();
                return true;
            }
        }

        // Also autoload the html namespace
        if (strstr($className, "Html") !== false) {
            $filePath = explode('\\', $className);
            $class = array_pop($filePath);
            $file = 'system' . DS . 'classes' . DS . strtolower(implode("/", $filePath)) . DS . $class . ".php";

            if (file_exists($file)) {
                require_once $file;
                file_put_contents($classdirectory_cache_file, '// ' . $cause . "\n" . '$this->_classdirectory["' . $className . '"]="' . $file . '";' . "\n\n", FILE_APPEND);
                $this->_classdirectory[$className] = $file;
                return true;
            }
        }

        // Last try, recurse in "/lib"
        $toplibpath = 'system' . DS . 'lib';
        $namespaceparts = explode('\\', $className);
        $classfile = array_pop($namespaceparts) . '.php';
        $libmatch = false;

        $topdirectory = new \RecursiveDirectoryIterator($toplibpath, \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::SKIP_DOTS);
        $classfilter = new \RecursiveCallbackFilterIterator($topdirectory, function ($current, $key, $iterator) {
            if (!$current->isDir()) { // Only respond to possible class match files.
                return  $current->getExtension() === "php";
            } else {
                return true;
            }
        });
        $iterator = new \RecursiveIteratorIterator($classfilter);

        foreach ($iterator as $info) {
            if ($info->getFilename() == $classfile) {
                $matchfile = $info->getPathname();
                require_once $matchfile;
                file_put_contents($classdirectory_cache_file, '// ' . implode("\\", $namespaceparts) . " " . $cause . "\n" . '$this->_classdirectory["' . $className . '"]="' . $matchfile . '";' . "\n\n", FILE_APPEND);
                $this->_classdirectory[$className] = $matchfile;
                $libmatch = true;
            }
        }

        return $libmatch;
    }

    private function componentLoader($name)
    {
        $classes_directory = 'system' . DS . 'classes';
        $directory = $classes_directory . DS . 'components';

        if (file_exists($directory . DS . $name . '.php')) {
            require_once $directory . DS . $name . '.php';
            return true;
        }

        if (file_exists($classes_directory . DS . $name . '.php')) {
            require_once $classes_directory . DS . $name . '.php';
            return true;
        }

        return false;
    }

    /**
     * This function returns an array of the $_SERVER['REQUEST_URI'] parts split by /
     * Where the $_SERVER['SCRIPT_NAME'] split by / has parts in common at the beginning, these are removed
     * eg /site/users/do/2 + site/index.php  => [users,do,2]
     * Thanks to:
     * http://www.phpaddiction.com/tags/axial/url-routing-with-php-part-one/
     */
    public function _getCommandPath($url = null)
    {
        $uri = explode('?', empty($url) ? $_SERVER['REQUEST_URI'] : $url); // get rid of parameters
        $uri = $uri[0];
        // get rid of trailing slashes
        if (substr($uri, -1) == "/") {
            $uri = substr($uri, 0, -1);
        }
        $requestURI = explode('/', $uri);
        $scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

        $diff = array_diff($requestURI, $scriptName);
        return array_values($diff);
    }

    /**
     * Enqueue script adds the script entry to the Webs _script var which maintains
     * already registered scripts and helps prevent multiple additions of the same
     * library
     *
     * A script entry should be in the form:
     * [
     *    "name" => "<name>",
     *    "uri" => "<uri>"
     *    "weight" => "<weight>" (used to order the loading of scripts, scripts
     *        are loaded in descending order of weight, e.g a script with a 1000
     *        weight will load before one with 600)
     * ]
     *
     * @param Array $script
     */
    public function enqueueScript($script)
    {
        if (!in_array($script, $this->_scripts)) {
            $this->_scripts[] = $script;
        }
    }

    public function loadVueComponents()
    {
        $components = [];

        foreach ($this->modules() as $module) {
            if (Config::get($module . '.active') === true && Config::get($module . '.vue_components') !== null) {
                $components = array_merge($components, Config::get($module . '.vue_components'));
            }
        }

        if (!empty($components)) {
            foreach ($components as $component => $paths) {
                CmfiveScriptComponentRegister::registerComponent($component, new CmfiveScriptComponent($paths[0], ['weight' => 100]));
                if (!empty($paths[1]) && file_exists(ROOT_PATH . $paths[1])) {
                    CmfiveStyleComponentRegister::registerComponent($component, (new CmfiveStyleComponent($paths[1]))->setProps(['weight' => 100]));
                }
            }
        }

        // Load components loaded in actions
        foreach (VueComponentRegister::getComponents() ?: [] as $name => $vue_component) {
            CmfiveScriptComponentRegister::registerComponent($name, new CmfiveScriptComponent($vue_component->js_path, ['weight' => 100]));
            if (!empty($vue_component->css_path) && file_exists(ROOT_PATH . $vue_component->css_path)) {
                CmfiveStyleComponentRegister::registerComponent($name, (new CmfiveStyleComponent($vue_component->css_path, ['/system/templates/scss/']))->setProps(['weight' => 100]));
            }
        }
    }

    /**
     * Enqueue style adds the style entry to the Webs _style var which maintains
     * already registered styles and helps prevent multiple additions of the same
     * library
     *
     * @param Array $script
     */
    public function enqueueStyle($style)
    {
        if (!in_array($style, $this->_styles)) {
            $this->_styles[] = $style;
        }
    }

    /**
     * Outputs the list of scripts to the buffer in order of weight descending
     */
    public function outputScripts()
    {
        if (!empty($this->_scripts)) {
            usort($this->_scripts, function ($a, $b) {
                $aw = intval($a["weight"]);
                $bw = intval($b["weight"]);
                return ($aw === $bw ? 0 : ($aw < $bw ? 1 : -1));
            });

            foreach ($this->_scripts as $script) {
                try {
                    CmfiveScriptComponentRegister::registerComponent($script['name'], new CmfiveScriptComponent($script['uri'], ['weight' => $script['weight']]));
                } catch (Exception $e) {
                    LogService::getInstance($this)->error($e->getMessage());
                }
            }
        }

        CmfiveScriptComponentRegister::outputScripts();
    }

    /**
     * Outputs the list of styles to the buffer in order of weight descending
     */
    public function outputStyles()
    {
        if (!empty($this->_styles)) {
            usort($this->_styles, function ($a, $b) {
                $aw = intval($a["weight"]);
                $bw = intval($b["weight"]);
                return ($aw === $bw ? 0 : ($aw < $bw ? 1 : -1));
            });

            foreach ($this->_styles as $style) {
                try {
                    CmfiveStyleComponentRegister::registerComponent($style['name'], (new CmfiveStyleComponent($style['uri'], ['/system/templates/scss/']))->setProps(['weight' => $style['weight']]));
                } catch (Exception $e) {
                    LogService::getInstance($this)->error($e->getMessage());
                }
            }
        }

        CmfiveStyleComponentRegister::outputStyles();
    }

    public function initLocale()
    {
        $user = AuthService::getInstance($this)->user();

        // default language
        $language = Config::get('system.language');
        // per user language s
        if (!empty($user)) {
            $lang = $user->language;
            if (!empty($lang)) {
                $language = $lang;
            }
        }

        // Fallback to en_AU if language is not set
        if (empty($language)) {
            $language = 'en_AU';
        }

        LogService::getInstance($this)->info('init locale ' . $language);

        $all_locale = getAllLocaleValues($language);

        putenv("LC_ALL={$language}");
        $results = setlocale(LC_ALL, $all_locale);

        if (empty($results)) {
            LogService::getInstance($this)->info('setlocale failed: locale function is not available on this platform, or the given locale (' . $language . ') does not exist in this environment');
        }
        $langParts = explode(".", $language);
        $this->currentLocale = $langParts[0];
    }


    public function getAvailableLanguages()
    {
        $lang = [];
        foreach ($this->modules() as $module) {
            if (Config::get("{$module}.active") === true && !empty(Config::get("{$module}.available_languages"))) {
                $lang = array_merge($lang, Config::get("{$module}.available_languages"));
            }
        }

        $filtered_lang = array_unique(array_filter($lang));
        $lang = [];
        foreach ($filtered_lang ?: [] as $key => $value) {
            $lang[] = [$value, $key];
        }

        return $lang;
    }

    /**
     * Set the default translation domain (module name)
     * Initialise gettext for this module if not already loaded
     */
    public function setTranslationDomain(string $domain)
    {
        $path = ROOT_PATH . DS . $this->getModuleDir($domain) . "translations";
        $translationFile = $path . DS . $this->currentLocale . DS . "LC_MESSAGES" . DS . $domain . ".mo";
        $translationFileOverride = ROOT_PATH . DS . 'translations' . DS . $domain . DS . $this->currentLocale . DS . 'LC_MESSAGES' . DS . $domain . '.mo';

        if (file_exists($translationFileOverride) && !empty(bindtextdomain($domain, ROOT_PATH . DS . 'translations' . DS . $domain))) {
            // Project language override has been loaded
        } elseif (file_exists($translationFile)) {
            // Fallback to module translation directory
            $results = bindtextdomain($domain, $path);
            if (!$results) {
                // Fallback to main module
                $path = ROOT_PATH . "/" . $this->getModuleDir('main') . "translations";
                $results = bindtextdomain('main', $path);
                if (!$results) {
                    throw new Exception('setlocale bindtextdomain failed on retry with main');
                }
            }
        } else {
            // Fallback to main module
            $path = ROOT_PATH . "/" . $this->getModuleDir('main') . "translations";
            $translationFile = $path . "/" . $this->currentLocale . "/LC_MESSAGES/main.mo";
            $results = bindtextdomain('main', $path);
            if (!$results) {
                throw new Exception('setlocale bindtextdomain failed on retry with main');
            }
        }

        bind_textdomain_codeset($domain ?? '', 'UTF-8');
        textdomain($domain);
    }

    /**
     * start processing of request
     * 1. look at the request parameter if the action parameter was set
     * 2. if not set, look at the pathinfo and use first
     */
    public function start($init_database = true)
    {
        try {
            if (Config::get("system.environment") !== "development") {
                set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                    $logger = empty($this->currentModule()) ? "CMFIVE" : strtoupper($this->currentModule());
                    LogService::getInstance($this)->setLogger($logger)->error("Number: {$errno}, String: {$errstr}, File: {$errfile}, Line: {$errline}");
                    $this->ctx('error', "An error occoured, if this message persists please contact your administrator.");
                });
            }
            

            // Set the timezone from Config
            $timezone = Config::get('system.timezone');
            if (empty($timezone)) {
                $timezone = 'UTC';
            }
            date_default_timezone_set($timezone);

            if ($init_database) {
                $this->initDB();
            }

            //check config for 'gc_maxlifetime' for the session
            $gc_maxlifetime = Config::get('system.gc_maxlifetime');
            //Checks include is greater than 1 hour (3600 sec) is less than 1 month (2628000 sec)
            if (!empty($gc_maxlifetime) && is_numeric($gc_maxlifetime) && $gc_maxlifetime >= 3600 && $gc_maxlifetime <= 2628000) {
                ini_set('session.gc_maxlifetime', $gc_maxlifetime);
            }

            /**
             * Based on request domain we can route everything to a frontend module look into the domain routing and prepend the module.
             * Check for frontend/portal modules first.
             * To enable portal support set portal flag in the module to true.
             * For it to work properly a domain name module must also be set.
             *
             * For example:
             * Config::set('{module}.portal', true);
             * Config::set('{module}.domain_name', '{domain_url}');
             */
            $domainmodule = null;
            foreach ($this->modules() as $module) {
                // Module config must be active and either 'portal' or 'frontend' flag set to true
                if (Config::get($module . '.active') == true && (Config::get($module . '.portal') == true || Config::get($module . '.frontend') == true)) {
                    if (strpos($_SERVER['HTTP_HOST'], Config::get($module . '.domain_name')) === 0) {
                        // Found module
                        $domainmodule = $module;
                        break;
                    }
                }
            }

            if (!empty($domainmodule)) {
                $this->_loginpath = "auth";
                $this->_isFrontend = true;
                $this->_isPortal = !!Config::get($domainmodule . '.portal');

                // now we have to decide whether the path points to
                // a) a single top level action
                // b) an action on a submodule
                // but we need to make sure not to mistake a path paramater for a submodule or an action!
                $domainsubmodules = $this->getSubmodules($domainmodule);
                $action_or_module = !empty($this->_paths[0]) ? $this->_paths[0] : null;
                if (!empty($domainsubmodules) && !empty($action_or_module) && array_search($action_or_module, $domainsubmodules) !== false) {
                    // just add the module to the first path entry, eg. frontend-page/1
                    $this->_paths[0] = $domainmodule . "-" . $this->_paths[0];
                } else {
                    // add the module as an entry to the front of paths, eg. frontend/index
                    array_unshift($this->_paths, $domainmodule);
                }
            }

            // start the session
            // $sess = new SessionManager($this);
            try {
                if ($this->_isPortal === true) {
                    session_name(!empty($domainmodule) ? $domainmodule . '_SID' : 'PORTAL_SID');
                } else {
                    session_name(SESSION_NAME);
                }

                // Store the sessions locally to avoid permission errors between OS's
                // I.e. on Windows by default tries to save to C:\Temp
                session_save_path(STORAGE_PATH . DIRECTORY_SEPARATOR . "session");

                if (Config::get("system.environment") !== "development") {
                    session_set_cookie_params(0, '/', $_SERVER['HTTP_HOST'], true, true);
                }
                session_start();
            } catch (Exception $e) {
                LogService::getInstance($this)->info("Error starting session " . $e->getMessage());
            }

            // Log out if timeout is set
            if (Config::get('auth.logout.logout_after_inactivity', false) === true && AuthService::getInstance($this)->loggedIn()) {
                if (array_key_exists('logout_timestamp', $_SESSION) && time() - $_SESSION['logout_timestamp'] > Config::get('auth.logout.timeout', 900)) {
                    $this->sessionDestroy();
                    $this->error('Your session has timed out, please log in again', '/auth/login');
                    exit;
                } else {
                    $this->session('logout_timestamp', time()); //set new timestamp
                }
            }

            // Initialise the logger (needs to log "info" to include the request data, see LogService __call function)
            LogService::getInstance($this)->info("info");

            // Reset the session when a user is not logged in. This will ensure the CSRF tokens are always "fresh"
            if ($_SERVER['REQUEST_METHOD'] == "GET" && empty(AuthService::getInstance($this)->loggedIn())) {
                CSRF::regenerate();
            }

            // Generate CSRF tokens and store them in the $_SESSION
            if (Config::get('system.csrf.enabled') === true) {
                CSRF::getTokenID();
                CSRF::getTokenValue();
            }

            $_SESSION['last_request'] = time();

            //$this->debug("Start processing: ".$_SERVER['REQUEST_URI']);
            // find out which module to use
            $module_found = false;
            $action_found = false;

            $this->_paths = $this->_getCommandPath();

            // first find the module file
            if ($this->_paths && sizeof($this->_paths) > 0) {
                $this->_module = array_shift($this->_paths);
            }

            // then find the action
            if ($this->_paths && sizeof($this->_paths) > 0) {
                $this->_action = array_shift($this->_paths);
            }

            if (!$this->_module) {
                $this->_module = $this->_defaultHandler;
            }

            // see if the module is a sub module
            // eg. /sales-report/showreport/1..
            $hsplit = explode("-", $this->_module);
            $this->_module = array_shift($hsplit);
            $this->_submodule = array_shift($hsplit);

            // Check to see if module exists, if it doesn't, display not found page.
            if (Config::get("{$this->_module}.active") === null) {
                $this->notFoundPage();
            }

            // Check to see if the module is active (protect against main disabling)
            if (!Config::get("{$this->_module}.active") && $this->_module !== "main") {
                $this->error("The {$this->_module} module is not active", "/");
            }

            // configure translations lookup for this module
            $this->initLocale();

            try {
                $this->setTranslationDomain('admin');
                $this->setTranslationDomain('main');
                $this->setTranslationDomain($this->currentModule());
            } catch (Exception $e) {
                LogService::getInstance($this)->setLogger('I18N')->error($e->getMessage());
            }

            if (!$this->_action) {
                $this->_action = $this->_defaultAction;
            }

            // try to load the action file
            $reqpath = $this->getModuleDir($this->_module) . 'actions/' . ($this->_submodule ? $this->_submodule . '/' : '') . $this->_action . '.php';
            if (!file_exists($reqpath)) {
                $reqpath = $this->getModuleDir($this->_module) . $this->_module . ($this->_submodule ? '.' . $this->_submodule : '') . ".actions.php";
            }

            // try to find action for the request type
            // using <module>_<action>_<type>()
            // or just <action>_<type>()

            $this->_requestMethod = array_key_exists('REQUEST_METHOD', $_SERVER) ? $_SERVER['REQUEST_METHOD'] : '';

            $actionmethods[] = $this->_action . '_' . $this->_requestMethod;

            if ($this->_requestMethod === "HEAD") {
                $this->_is_head_request = true;
                $actionmethods[] = $this->_action . '_GET';
            }

            $actionmethods[] = $this->_action . '_' . $this->_requestMethod;
            $actionmethods[] = $this->_action . '_ALL';

            // Check/validate CSRF token
            if (Config::get('system.csrf.enabled') === true) {
                $allowed = Config::get('system.csrf.protected');
                if (!empty($allowed[$this->_module]) || (!empty($this->_submodule) && !empty($allowed[$this->_module . '-' . $this->_submodule]))) {
                    if (in_array($this->_action, $allowed[$this->_module] ?? []) || (!empty($this->_submodule) && in_array($this->_action, $allowed[$this->_module . '-' . $this->_submodule] ?? []))) {
                        // If we get here then we are configured to enforce CSRF checking
                        LogService::getInstance($this)->debug("Checking CSRF");
                        try {
                            $this->validateCSRF();
                        } catch (Exception $e) {
                            $this->msg('The current session has expired, please resubmit the form', $_SERVER['REQUEST_URI']);
                        }
                    }
                }
            }

            // if a module file for this url exists, then start processing
            if (file_exists($reqpath)) {
                $this->ctx('webroot', $this->_webroot);
                $this->ctx('module', $this->_module);
                $this->ctx('submodule', $this->_module);
                $this->ctx('action', $this->_action);

                // CHECK ACCESS!!
                $this->checkAccess(); // will redirect if access denied!

                // load the module file
                require_once $reqpath;
            } else {
                LogService::getInstance($this)->error("System: No Action found for: " . $reqpath);
                $this->notFoundPage();
            }

            foreach ($actionmethods as $action_method) {
                if (function_exists($action_method)) {
                    $action_found = true;
                    $this->_actionMethod = $action_method;
                    break;
                }
            }

            if ($action_found) {
                $this->ctx("loggedIn", AuthService::getInstance($this)->loggedIn());

                if ($this->session('error') !== null) {
                    $this->ctx("error", $this->session('error'));
                }

                $this->sessionUnset('error');
                $this->ctx("msg", $this->session('msg'));
                $this->sessionUnset('msg');
                $this->ctx("w", $this);

                /*$this->sendHeader("Report-To", json_encode([
                "group" => "log-action",
                "max-age" => "10886400",
                "endpoints" => ["url" => "/main/logCSPReport"],
            ])); */
                // All content must come from the site and disallow flash.
                // report uri is deprecated in chrome 70, but still required for firefox and other browsers (as of jan 2020)
                // see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/report-uri
                /* $this->sendHeader(
                "Content-Security-Policy-Report-Only",
                "default-src 'none'; script-src 'self'; style-src 'self'; img-src 'self'; frame-ancestors 'none'; object-src 'none'; report-uri /main/logCSPReport/;"
            ); */

                // send security headers before actions to avoid actions echoing out content
                // @todo move security header configuration to system config.php

                $this->header("Feature-Policy", "ambient-light-sensor 'none'; autoplay 'none'; accelerometer 'none'; camera 'none'; display-capture 'none'; document-domain 'none'; encrypted-media 'none'; fullscreen 'none'; geolocation 'none'; gyroscope 'none'; magnetometer 'none'; microphone 'none'; midi 'none'; payment 'none'; picture-in-picture 'none'; speaker 'none'; sync-xhr 'self'; usb 'none'; wake-lock 'none'; webauthn 'none'; vr 'none'");

                $this->header("Strict-Transport-Security", "max-age=63072000");
                $this->header("X-Content-Type-Options", "nosniff");
                //  $this->sendHeader("X-Frame-Options", "DENY");
                $this->header("X-XSS-Protection", "1; mode=block");

                try {
                    // call hooks, generic to specific
                    $this->_callWebHooks("before");

                    // Execute the action
                    $method = $this->_actionMethod;
                    $this->_action_executed = true;
                    $method($this);

                    // call hooks, generic to specific
                    $this->_callWebHooks("after");
                } catch (PermissionDeniedException $ex) {
                    $this->error($ex->getMessage());
                }

                // send headers first
                if ($this->_headers) {
                    foreach ($this->_headers as $key => $val) {
                        $this->header($key . ': ' . $val);
                    }
                }

                // If a HEAD request was sent, no body is required but it behaves like a GET
                if ($this->_is_head_request === true) {
                    return;
                }

                $body = null;
                // evaluate template only when buffer is empty
                if (empty($this->_buffer)) {
                    $body = $this->fetchTemplate();
                } else {
                    $body = $this->_buffer;
                }

                // but always check for layout
                // if ajax call don't do the layout
                if ($this->_layout && !$this->isAjax()) {
                    $this->_buffer = '';
                    $this->ctx($this->_layoutContentMarker, $body);
                    $this->templateOut($this->_layout);
                } else {
                    $this->_buffer = $body ?? '';
                }

                echo $this->_buffer;
            } else {
                $this->notFoundPage();
            }
        } catch (Throwable $t) {
            $logger = empty($this->currentModule()) ? "CMFIVE" : strtoupper($this->currentModule());
            LogService::getInstance($this)->setLogger($logger)->error("Throwable caught in Web: {$t->getMessage()} Trace: {$t->getTraceAsString()}");
            echo Html::alertBox("An error occurred, if this message persists please contact your administrator.", "alert");
        } finally {
            $this->_callWebHooks("cleanup");
        }
    }

    /**
     * This creates and calls the following hooks:
     *
     * core_web_before
     * core_web_after
     * core_web_cleanup
     * core_web_before_get
     * core_web_before_get_[module]
     * core_web_before_get_[module]_[action]
     * core_web_before_get_[module]_[submodule]
     * core_web_before_get_[module]_[submodule]_[action]
     * core_web_after_get
     * core_web_after_get_[module]
     * core_web_after_get_[module]_[action]
     * core_web_after_get_[module]_[submodule]
     * core_web_after_get_[module]_[submodule]_[action]
     * core_web_before_post
     * core_web_before_post_[module]
     * core_web_before_post_[module]_[action]
     * core_web_before_post_[module]_[submodule]
     * core_web_before_post_[module]_[submodule]_[action]
     * core_web_after_post
     * core_web_after_post_[module]
     * core_web_after_post_[module]_[action]
     * core_web_after_post_[module]_[submodule]
     * core_web_after_post_[module]_[submodule]_[action]
     *
     * @param string $type eg. before / after
     */
    public function _callWebHooks($type)
    {
        // If there isn't a database connection, this will crash
        if (empty($this->db)) {
            return;
        }

        $request_method = strtolower($this->_requestMethod);

        // call hooks, generic to specific
        $this->callHook("core_web", $type); // anything
        $this->callHook("core_web", $type . "_" . $request_method); // GET /*
        $this->callHook("core_web", $type . "_" . $request_method . "_" . $this->_module); // GET /module
        $this->callHook("core_web", "cleanup"); // Calls cleanup hooks for any action.

        // Only call submodule hooks if a submodule is present, else call the module/action hook
        if (!empty($this->_submodule)) {
            $this->callHook("core_web", $type . "_" . $request_method . "_" . $this->_module . "_" . $this->_submodule); // GET /module-submodule/*
            $this->callHook("core_web", $type . "_" . $request_method . "_" . $this->_module . "_" . $this->_submodule . "_" . $this->_action); // GET /module-submodule/action
        } else {
            $this->callHook("core_web", $type . "_" . $request_method . "_" . $this->_module . "_" . $this->_action); // GET /module/action
        }
    }

    /**
     * Connect to the database
     */
    public function initDB()
    {
        try {
            $this->db = new DbPDO(Config::get("database"), Config::get("search.stopword_override"));
        } catch (Exception $ex) {
            LogService::getInstance($this)->setLogger("CORE")->error("Error: Can't connect to database, $ex");
            echo "Error: Can't connect to database.";
            die();
        }
    }

    /**
     * Read Module configuration values
     *
     * @param string $module
     * @param string $key
     * @return mixed
     */
    public function moduleConf($module, $key)
    {
        return Config::get("{$module}.{$key}");
    }

    public function loadConfigurationFiles()
    {
        $cachefile = ROOT_PATH . "/cache/config.cache";

        // check for config cache file. If exists, then load the config
        // from this file!
        if (file_exists($cachefile)) {
            // load the cache file
            Config::fromJson(file_get_contents($cachefile));
            return;
        }

        // first load the system config file
        require SYSTEM_PATH . "/config.php";

        // Load System modules config first
        $baseDir = SYSTEM_PATH . '/modules';
        $this->scanModuleDirForConfigurationFiles($baseDir);

        // Load project module config second
        $baseDir = ROOT_PATH . '/modules';
        $this->scanModuleDirForConfigurationFiles($baseDir, true);

        // load the root level config file last because it can override everything
        if (file_exists(ROOT_PATH . "/config.php")) {
            require ROOT_PATH . "/config.php";
        }

        // if config cache file doesn't exist, then
        // create it new
        if (!is_dir(ROOT_PATH . '/cache')) {
            mkdir(ROOT_PATH . '/cache');
        }
        file_put_contents($cachefile, Config::toJson());
    }

    // Helper function for the above, scans a directory for config files in child folders
    private function scanModuleDirForConfigurationFiles($dir = "", $loadWithDependencies = false)
    {
        // Check that dir is dir
        if (is_dir($dir)) {
            // Scan directory
            $dirListing = scandir($dir);

            if (!empty($dirListing)) {
                // Loop through listing
                foreach ($dirListing as $item) {
                    $searchingDir = $dir . "/" . $item;

                    if (is_dir($searchingDir) and $item[0] !== '.') {
                        // If is also a directory, look for config.php file
                        if (file_exists($searchingDir . "/config.php")) {
                            // Sandbox config load to check if module active
                            Config::enableSandbox();
                            include($searchingDir . '/config.php');
                            $include_path = $searchingDir . '/config.php';
                            include(ROOT_PATH . '/config.php');

                            if (Config::get("{$item}.active") === true) {
                                // Need to reset sandbox content to remove inclusion of project config
                                Config::clearSandbox();
                                include($searchingDir . '/config.php');

                                // If we are loading with dependencies, register config in the dependency loader
                                // (located in Config.php) instead of putting into base config setup
                                if ($loadWithDependencies === true) {
                                    // Set config on current module
                                    ConfigDependencyLoader::registerModule($item, Config::getSandbox(), $include_path);
                                } else {
                                    Config::disableSandbox();
                                    include($searchingDir . '/config.php');
                                    Config::enableSandbox();
                                }
                            }

                            // Always disable the sandbox to ensure other uses of Config do not get omitted
                            Config::clearSandbox();
                            Config::disableSandbox();
                        }
                    }
                }

                // Load with dependencies if required
                if ($loadWithDependencies === true) {
                    try {
                        ConfigDependencyLoader::load();
                    } catch (Exception $e) {
                        LogService::getInstance($this)->error($e->getMessage());
                        echo "Module config load error: " . $e->getMessage();
                        die;
                    }
                }
            }
        }
    }

    /**
     * Check for CSRF token and that we have a valid request method
     *
     * @throws CSRFException
     *
     * @return void
     */
    public function validateCSRF()
    {
        if (Config::get("system.csrf.enabled") == true && !CSRF::isValid($this->_requestMethod)) {
            if (!CSRF::inHistory()) {
                @LogService::getInstance($this)->error("System: CSRF Detected from " . $this->requestIpAddress());
                throw new CSRFException("Cross site request forgery detected. Your IP has been logged");
            } else {
                $this->msg("Duplicate form submission detected, make sure you only click buttons once");
            }
        }
    }

    /**
     * reads the /actions folder inside a module
     * and returns the submodule names
     *
     * @param string $module
     * @return array|null
     */
    public function getSubmodules($module)
    {
        $dir = $this->getModuleDir($module) . "actions";
        $listing = scandir($dir);
        if (empty($listing)) {
            return null;
        }
        $submodules = [];
        foreach ($listing as $item) {
            if (is_dir($dir . "/" . $item) && $item[0] !== '.') {
                $submodules[] = $item;
            }
        }
        return $submodules;
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            (array_key_exists('CONTENT_TYPE', $_SERVER) && $_SERVER['CONTENT_TYPE'] == "application/json") ||
            $this->_layout === null;
    }

    /**
     * Check if the currently logged in user has access to this path
     * Return true if access is allowed
     * Redirect back a page or logout and show an error if access is denied
     *
     * Save LAST_ALLOWED_URI to session
     *
     * @param <type> $msg
     * @return <type>
     */
    public function checkAccess($msg = "Access Restricted")
    {
        $submodule = $this->_submodule ? "-" . $this->_submodule : "";
        $path = $this->_module . $submodule . "/" . $this->_action;
        $actual_path = $path;
        // Check for frontend modules
        if ($this->_isFrontend || $this->_isPortal) {
            $actual_path = $this->_action;
        }

        if (AuthService::getInstance($this) && AuthService::getInstance($this)->user()) {
            $user = AuthService::getInstance($this)->user();

            if ($user->is_password_invalid && $path !== "auth/update_password") {
                LogService::getInstance($this)->info("Redirecting to reset password page, user password is invalid");
                $this->redirect($this->localUrl("/auth/update_password"));
            }

            $usrmsg = $user ? " for " . $user->login : "";
            if (!AuthService::getInstance($this)->allowed($path)) {
                LogService::getInstance($this)->info("System: Access Denied to " . $path . $usrmsg . " from " . $this->requestIpAddress());
                // redirect to the last allowed page
                $lastAllowed = (is_array($_SESSION) && array_key_exists('LAST_ALLOWED_URI', $_SESSION)) ? $_SESSION['LAST_ALLOWED_URI'] : '';
                if (AuthService::getInstance($this)->allowed($lastAllowed)) {
                    $this->error($msg, $lastAllowed);
                } else {
                    // Logout user
                    $this->sessionDestroy();
                    $this->error($msg, $this->_loginpath);
                }
            }
        } elseif (AuthService::getInstance($this) && !AuthService::getInstance($this)->loggedIn() && ($actual_path != $this->_loginpath && $actual_path != $this->_is_mfa_enabled_path) && !AuthService::getInstance($this)->allowed($path)) {
            $_SESSION['orig_path'] = $_SERVER['REQUEST_URI'];
            LogService::getInstance($this)->info("Redirecting to login, user not logged in or not allowed");
            $this->redirect($this->localUrl($this->_loginpath));
        }
        // Saving the last allowed path so we can
        // redirect to it from a failed call
        if (!$this->isAjax()) {
            $_SESSION['LAST_ALLOWED_URI'] = $actual_path;
        }
        return true;
    }

    /**
     *
     * Return the mimetype for a file path
     * @param $filename (including path)
     * @return string
     */
    public function getMimetype($filename)
    {
        $mime = "application/octet-stream";

        // finfo_open was introduced in 5.3, however some hosts like Crazy Domains make it extra difficult
        // by compiling php without the finfo extension.

        // BEST OPTION
        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filename);
            finfo_close($finfo);
        } elseif (strtolower(substr(PHP_OS, 0, 3)) != "win") {
            // SECOND BEST OPTION BUT ONLY ON *NIX
            ob_start();
            system("file -i -b {$filename}");
            $output = ob_get_clean();
            $output = explode("; ", $output);
            if (is_array($output)) {
                $output = $output[0];
            }
            $mime = $output;
        } else {
            // THIS IS A VERY BAD ALTERNATIVE, BUT MAY BE BETTER THAN NOTHING
            $mime_types = [
                'txt' => 'text/plain',
                'csv' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',
                'doc' => 'application/msword',
                'docx' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.ms-powerpoint',
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            ];

            $ext = strtolower(array_pop(explode('.', $filename)));
            if (array_key_exists($ext, $mime_types)) {
                $mime = $mime_types[$ext];
            }
        }
        return $mime;
    }

    /**
     * Returns the mime type of a binary string, only works with the finfo
     * extension enabled
     *
     * @param <String> resource_string
     * @return <String> mimetype
     */
    public function getMimetypeFromString($resource_string)
    {
        $mime = 'text/plain';

        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_buffer($finfo, $resource_string);
            finfo_close($finfo);
        }

        return $mime;
    }

    /**
     * Convenience Method for creating menu's
     * This will check if $path is allowed
     * and will then return an html link or nothing
     *
     * if $array is set will also add the link to the array
     *
     * @param string $path
     * @param string $title
     * @param array $array
     * @return string
     */
    public function menuLink($path, $title, &$array = null, $confirm = null, $target = null, $class = "")
    {
        if (startsWith($path, $this->currentModule())) {
            $class .= " current active";
        }
        $link = AuthService::getInstance($this)->allowed($path, HtmlBootstrap5::a($this->localUrl($path), $title, $title, $class, $confirm, $target));
        if ($array !== null) {
            $array[] = $link;
        }
        return $link;
    }

    /**
     * Same as menuLink but displays a button instead
     * @param string $path
     * @param string $title
     * @param string $array
     * @return string html code
     */
    public function menuButton($path, $title, &$array = null, $id = '')
    {
        $link = AuthService::getInstance($this)->allowed($path, Html::b($this->localUrl($path), $title, null, $id));
        if ($array !== null) {
            $array[] = $link;
        }
        return $link;
    }

    /**
     * Convenience Method for creating menu's
     * This will check if $path is allowed
     * and will then return an html link or nothing
     *
     * This will create a link which will open a popup box
     *
     * if $array is set will also add the link to the array
     *
     * @param string $path
     * @param string $title
     * @param array $array
     */
    public function menuBox($path, $title, &$array = null)
    {
        $link = AuthService::getInstance($this)->allowed($path, Html::box($this->localUrl($path), $title));
        if ($array !== null) {
            $array[] = $link;
        }
        return $link;
    }

    /**
     * Creates a url prefixed with the webroot
     *
     * @param string $link
     * @return string html code
     */
    public function localUrl($link = "")
    {
        // PHP8: strpos null param is deprecated
        if (!empty($link) && strpos($link, "/") !== 0) {
            $link = "/" . $link;
        }
        return $this->webroot() . $link;
    }

    /**
     * Redirect to $url and display an
     * error message
     *
     * @param <type> $msg
     * @param <type> $url
     */
    public function error($msg, $url = "")
    {
        $_SESSION['error'] = $msg;
        $this->ctx('error', $msg);
        $this->redirect($this->localUrl($url));
    }

    /**
     * This function generates an error message based on whats returned from the DbObject validation method
     * $w is for the error() function
     * $object is the object that one is saving/updating whatever
     * $type is for the message returned, i.e. "Updating this $type failed"
     * $response is the response array from the validation method
     * $isUpdating is a helper for the message i.e. creating/updating
     * $returnUrl is where the redirection in error() will go
     *
     * @param <Object> $object
     * @param <String> $type
     * @param <Array|Boolean> $response
     * @param <Boolean> $isUpdating
     * @param <String> $returnUrl
     * @return null
     */
    public function errorMessage($object, $type = null, $response = true, $isUpdating = false, $returnUrl = "/")
    {
        if ($response === true || empty($type)) {
            return;
        } else {
            if (is_array($response)) {
                $errorMsg = ($isUpdating ? "Updating" : "Creating") . " this $type failed because<br/><br/>\n";

                foreach ($response["invalid"] as $property => $reason) {
                    foreach ($reason as $r) {
                        $errorMsg .= $object->getHumanReadableAttributeName($property) . ": $r <br/>\n";
                    }
                }
                LogService::getInstance($this)->error("System: Saving " . get_class($object) . " error: " . $errorMsg);
                $this->error($errorMsg, $returnUrl);
            } else {
                LogService::getInstance($this)->error("System: " . ($isUpdating ? "Updating" : "Creating") . " this $type failed.");
                $this->error(($isUpdating ? "Updating" : "Creating") . " this $type failed.", $returnUrl);
            }
        }
    }

    /**
     * Redirect to $url and display
     * a message
     *
     * @param <type> $msg
     * @param <type> $url
     */
    public function msg($msg, $url = "")
    {
        $_SESSION['msg'] = $msg;
        $this->ctx('msg', $msg);
        $this->redirect($this->localUrl($url));
    }

    /**
     * Sends 404 header and displays not found message<br/>
     * <b>THIS EXITS the current process</b>
     */
    public function notFoundPage()
    {
        LogService::getInstance($this)->warn("System: Action not found: " . $this->_module . "/" . $this->_action);
        $this->ctx("w", $this);

        // We want to fail gracefully for ajax requests
        if ($this->isAjax()) {
            echo "The page requested could not be found.";
        } else {
            if ($this->templateExists($this->_notFoundTemplate)) {
                $this->header("HTTP/1.0 404 Not Found");
                // $this->ctx("w", $this);

                if (empty(AuthService::getInstance($this)->user())) {
                    echo $this->fetchTemplate($this->_notFoundTemplate);
                } else {
                    $this->ctx($this->_layoutContentMarker, $this->fetchTemplate($this->_notFoundTemplate));
                    $this->templateOut($this->_layout);
                    echo $this->_buffer;
                }
            } else {
                $this->header("HTTP/1.0 404 Not Found");
                echo '<p align="center">Sorry, page not found.</p>';
            }
        }
        exit();
    }

    public function fatalErrorPage()
    {
        http_response_code(500);

        if ($this->isAjax()) {
            return;
        }

        if ($this->templateExists($this->_fatalErrorTemplate)) {
            echo $this->fetchTemplate($this->_fatalErrorTemplate);
            return;
        }

        echo Html::alertBox("An error occurred, if this message persists please contact your administrator.", "alert");
    }

    public function internalLink($title, $module, $action = null, $params = null)
    {
        if (!AuthService::getInstance($this)->allowed($module, $action)) {
            return null;
        } else {
            return "<a href='" . $this->localUrl("/" . $module . "/" . $action . $params) . "'>" . $title . "</a>";
        }
    }

    /**
     * Return all modules currently in the codebase
     */
    public function modules()
    {
        return Config::keys();
    }

    /**
     *
     * Returns the file path for a module if it exists,
     * otherwise returns null
     * @param string $module
     * @return string|null
     */
    public function getModuleDir($module = null): ?string
    {
        if ($module == null) {
            $module = $this->_module;
        }
        // check for explicit module path first
        $basepath = $this->moduleConf($module, 'path');
        if (!empty($basepath)) {
            $path = $basepath . DS . $module . DS;

            // Now that we support injected modules sometimes this function will be called from their perspective
            // We need to check if this is the case and return the module actually responsible for the injected modules
            if (!file_exists($path)) {
                $injected_rule = Config::get($module . '.injected_by');
                if (!empty($injected_rule)) {
                    return file_exists(Config::get("{$injected_rule}.path") . DS . $injected_rule . DS) ? Config::get("{$injected_rule}.path") . DS . $injected_rule . DS : null;
                }
            }
            return $path;
        }

        return null;
    }

    public function moduleUrl($module)
    {
        return $this->webroot() . '/' . $this->getModuleDir($module);
    }

    /**
     * A helper function to return the module name of a file located in its models directory
     *
     * @param string $classname
     * @return mixed $module
     */
    public function getModuleNameForModel($classname)
    {
        // Check for active in here, if above key exists then we know its already been created
        $ref_cname = new ReflectionClass($classname);
        $directory = dirname($ref_cname->getFileName());

        // Don't forget about catering for the elephant in the room
        $exp_directory = explode(DIRECTORY_SEPARATOR, $directory);

        // We know that the last entry is "models", the entry before it is the module name
        // Sanity check
        $module = null;
        if (end($exp_directory) == "models") {
            $module = prev($exp_directory);
        }
        return $module;
    }

    /**
     * Another helper function to quickly determine if a class's host module have been marked inactive
     */
    public function isClassActive($classname)
    {
        if (class_exists($classname)) {
            $modulename = $this->getModuleNameForModel($classname);
            if ($modulename === null || Config::get("$modulename.active") === false) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Call and return code for a partial template.
     *
     * This works like an action/template except that it can't be called directly from a url.
     *
     * Partials don't have access to the global context and do not store anything in the global context!
     *
     * @param string $name
     * @param array $params
     * @param string $module
     * @param string $method
     */
    public function partial($name, $params = null, $module = null, $method = "ALL")
    {
        if ($module === null) {
            $module = $this->_module;
        }

        // set translations to partial module
        $oldModule = $this->currentModule();
        if ($oldModule != $module) {
            try {
                $this->setTranslationDomain($module);
            } catch (Exception $e) {
                LogService::getInstance($this)->setLogger('I18N')->error($e->getMessage());
            }
        }

        // save current output buffer
        $oldbuf = $this->_buffer;
        $this->_buffer = '';

        // save the current context
        $oldctx = $this->_context;
        $this->_context = [];

        // try to find the partial action and execute

        // getModuleDir can return path with trailing '/' but we dont want that
        $moduleDir = $this->getModuleDir($module);

        if ($moduleDir[strlen($moduleDir) - 1] === '/') {
            $moduleDir = substr($moduleDir, 0, strlen($moduleDir) - 1);
        }

        $partial_action_file = implode("/", [$moduleDir, $this->_partialsdir, "actions", $name . ".php"]);

        if (file_exists($partial_action_file)) {
            require_once $partial_action_file;

            // Execute the action, accounting for the use of namespaces
            // Work out the namespace
            $module_path = Config::get($module . '.path');
            $namespace = '';
            if (empty($module_path)) {
                $namespace = '\System\Modules\\' . ucfirst(strtolower($module)) . '\\';
            } else {
                // Path will almost 100% of the time be either 'modules' or 'system/modules'
                // But we want this in the form '\Modules\\$module' or '\System\\Modules\\$module'
                $namespace = '\\' . ucwords(strtolower(str_replace('/', '\\', $module_path))) . '\\' . ucfirst(strtolower($module)) . '\\';
            }

            // The following will call:
            // 1. \System\Modules\$module\$action_ALL()
            // 2. \System\Modules\$module\$action()
            // 3. $action_ALL()
            // 4. $action()

            $partial_action = $name . '_' . $method;
            if (function_exists($namespace . $partial_action)) {
                $function = $namespace . $partial_action;
                $function($this, $params);
            } elseif (function_exists($namespace . $name)) {
                $function = $namespace . $name;
                $function($this, $params);
            } elseif (function_exists($partial_action)) {
                $partial_action($this, $params);
            } elseif (function_exists($name)) {
                $name($this, $params);
            } else {
                LogService::getInstance($this)->error("Required partial action not found, expected {$partial_action}");
            }
        } else {
            LogService::getInstance($this)->error("Could not find partial file at: {$partial_action_file}");
        }

        $currentbuf = $this->_buffer;

        if (empty($currentbuf)) {
            // try to find the partial template and execute if found
            $partial_template_file = implode("/", [$moduleDir, $this->_partialsdir, "templates", $name . $this->_templateExtension]);

            if (file_exists($partial_template_file)) {
                $tpl = new WebTemplate();
                $this->ctx("w", $this);
                $tpl->setVars($this->_context);
                $currentbuf = $tpl->fetch($partial_template_file);
            }
        }

        // restore output buffer and context
        $this->_buffer = $oldbuf ?? '';
        $this->_context = $oldctx;

        // restore translations module
        if ($oldModule != $module) {
            try {
                $this->setTranslationDomain($oldModule);
            } catch (Exception $e) {
                LogService::getInstance($this)->setLogger('I18N')->error($e->getMessage());
            }
        }

        return $currentbuf;
    }

    /**
     * Call hook method to invoke other modules helper functions
     *
     * @param string module
     * @param string $function
     * @param mixed $data
     * @return array|null array of return values from all functions that answer to this hool
     */
    public function callHook($module, $function, $data = null)
    {
        if (empty($module) || empty($function)) {
            return null;
        }

        // set translations to hook module
        $oldModule = $this->currentModule();
        if ($oldModule != $module) {
            try {
                $this->setTranslationDomain($module);
            } catch (Exception $e) {
                LogService::getInstance($this)->setLogger('I18N')->error($e->getMessage());
            }
        }

        // Build _hook registry if empty
        if (empty($this->_hooks)) {
            foreach ($this->modules() as $modulename) {
                // only include active modules!
                if (Config::get("$modulename.active") !== false) {
                    $hooks = Config::get("{$modulename}.hooks");

                    if (!empty($hooks)) {
                        foreach ($hooks as $hook) {
                            $this->_hooks[$hook][] = $modulename;
                        }
                    }
                }
            }
        }
        // Check that the module calling has subscribed to hooks
        if (!array_key_exists($module, $this->_hooks)) {
            return null;
        }

        // If module inactive, continue
        if (Config::get("$module.active") === false) {
            return null;
        }
        // Loop through each registered module to try and invoke the function
        $buffer = [];

        foreach ($this->_hooks[$module] as $toInvoke) {
            // Check that the hook impl module that we are invoking is a module
            if (!in_array($toInvoke, $this->modules())) {
                continue;
            }

            // Wrap the hook call in a try-catch to hide and log exceptions caused by the hook function.
            try {
                $hook_function_name = $toInvoke . "_" . $module . "_" . $function;

                //check if we have already loaded module hooks
                if (!in_array($toInvoke, $this->_module_loaded_hooks)) {
                    // if this function is already loaded from an earlier call, execute now
                    if (function_exists($hook_function_name)) {
                        $buffer[] = $hook_function_name($this, $data);
                    } else {
                        // Check if the file exists and load
                        if (!file_exists($this->getModuleDir($toInvoke) . $toInvoke . ".hooks.php")) {
                            continue;
                        }

                        // Include and check if function exists

                        include_once $this->getModuleDir($toInvoke) . $toInvoke . ".hooks.php";
                        // add module to loaded hooks array
                        $this->_module_loaded_hooks[] = $toInvoke;

                        if (function_exists($hook_function_name)) {
                            // Call function

                            $buffer[] = $hook_function_name($this, $data);
                        }
                    }
                } else {
                    if (function_exists($hook_function_name)) {
                        $buffer[] = $hook_function_name($this, $data);
                    }
                }
            } catch (Throwable $t) {
                LogService::getInstance($this)->setLogger("CMFIVE")->error("Fatal error caught from hook {$t->getTraceAsString()}");
            }
        }

        // restore translations module
        if ($oldModule != $module) {
            try {
                $this->setTranslationDomain($oldModule ?? '');
            } catch (Exception $e) {
                LogService::getInstance($this)->setLogger('I18N')->error($e->getMessage());
            }
        }

        return $buffer;
    }

    /////////////////////////////////// Template stuff /////////////////////////

    public function setLayout($l)
    {
        $this->_layout = $l;
    }

    /** TODO - Fix this to GET value **/
    public function getLayout($l)
    {
        $this->_layout = $l;
    }

    public function setTemplate($t)
    {
        $this->_template = $t;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * set the path where Web looks for template files
     */
    public function setTemplatePath($path)
    {
        $this->_templatePath = $path;
    }

    public function setTemplateExtension($ext)
    {
        $this->_templateExtension = $ext;
    }

    /**
     * check if a template file exists!
     */
    public function templateExists($name)
    {
        $trimmed_module = "";
        if (!is_null($this->getModuleDir($this->_module))) {
            $trimmed_module = rtrim($this->getModuleDir($this->_module), '/');
        }

        if ($this->_submodule) {
            $paths[] = implode("/", [$trimmed_module, $this->_templatePath, $this->_submodule]);
        }

        $paths[] = implode("/", [$trimmed_module, $this->_templatePath]);
        $paths[] = implode("/", [$trimmed_module]);
        $paths[] = implode("/", [$this->_templatePath, $this->_module]);
        $paths[] = $this->_templatePath;
        // Add system fallback
        $paths[] = SYSTEM_PATH . "/" . $this->_templatePath;
        // Allow specifying the full path
        $paths[] = ROOT_PATH;

        $names = [];
        if ($name) {
            $names[] = $name;
        } else {
            $names[] = $this->_actionMethod;
            $names[] = $this->_action;
            if ($this->_submodule) {
                $names[] = $this->_submodule;
            } else {
                $names[] = $this->_module;
            }
        }

        // we need to find a template from a combination of paths and names
        // in the above arrays from the most specific to the most broad
        $template = null;
        foreach ($paths as $path) {
            foreach ($names as $nam) {
                $name = $this->getTemplateRealFilename($nam);
                if ($name && file_exists($path . '/' . $name)) {
                    $template = $path . '/' . $nam;
                    break 2; // break out of both loops
                }
            }
        }

        return $template;
    }

    public function getTemplateRealFilename($tmpl)
    {
        return $tmpl . $this->_templateExtension;
    }

    /**
     * Evaluates a template in the web context and
     * returns it as string. The template is searched for
     * in the following order: <br/>
     * <pre>
     * /<moduledir>/<module>/templates/<submodule>/<action>_<httpmethod>.tpl.php
     * /<moduledir>/<module>/templates/<submodule>/<action>.tpl.php
     * /<moduledir>/<module>/templates/<submodule>/<submodule>.tpl.php
     * /<moduledir>/<module>/templates/<action>_<httpmethod>.tpl.php
     * /<moduledir>/<module>/templates/<action>.tpl.php
     * /<moduledir>/<module>/templates/<module>.tpl.php
     * /<moduledir>/<module>/<action>_<httpmethod>.tpl.php
     * /<moduledir>/<module>/<action>.tpl.php
     * /<moduledir>/<module>/<module>.tpl.php
     * /<templatedir>/<action>_<httpmethod>.tpl.php
     * /<templatedir>/<action>.tpl.php
     * /<templatedir>/<module>.tpl.php
     * </pre>
     */
    public function fetchTemplate($name = null)
    {
        $template = $this->templateExists($name);

        if (!$template) {
            return null;
        }
        $tpl = new WebTemplate();
        $tpl->setVars($this->_context);
        return $tpl->fetch($this->getTemplateRealFilename($template));
    }

    /**
     * evaluate template and put the string into
     * the web context for inclusion in other
     * templates
     */
    public function putTemplate($key, $template)
    {
        $this->ctx($key, $this->fetchTemplate($template));
    }

    /**
     * This will execute the passed in template
     * instead of the default one. The layout will
     * still be used!
     */
    public function templateOut($template)
    {
        $this->out($this->fetchTemplate($template));
    }

    /**
     * prints to the page
     * if this is used, then the template will NOT be called
     * automatically! But the layout will still be used.
     */
    public function out($txt)
    {
        $this->_buffer .= $txt ?? '';
    }

    public function webroot()
    {
        return $this->_webroot;
    }

    /**
     * Turns a variable list of string arguments into
     * context entries loaded with the values of the url segments.
     *
     * eg: Given a URL with /one/two/three, calling
     *     pathMatch("eins","zwei","drei") will insert into the context
     *     ("eins" => "one", "zwei" => "two", "drei" => "three")
     *
     * @param mixed string params, which will be turned into ctx entries
     * @return array array of key, value pairs
     */
    public function pathMatch()
    {
        $match = [];

        $func_num_args = func_num_args();
        if ($func_num_args > 0) {
            for ($i = 0; $i < $func_num_args; $i++) {
                $param = func_get_arg($i);

                $val = !empty($this->_paths[$i]) ? urldecode($this->_paths[$i]) : null;

                if (is_array($param)) {
                    $key = $param[0];
                    if (is_null($val) && isset($param[1])) {
                        $val = $param[1]; // use default parameter
                    }
                } else {
                    $key = $param;
                }
                $this->ctx($key, $val);
                $match[$key] = $val;
                $match[$i] = $val;
            }
        } else {
            return $this->_paths;
        }
        return $match;
    }

    public function requestIpAddress()
    {
        return array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * Return the current module
     * @return <type>
     */
    public function currentModule()
    {
        return $this->_module;
    }

    /**
     * Return the current module
     * @return <type>
     */
    public function currentSubModule()
    {
        return $this->_submodule;
    }

    /**
     * Return the current Action
     */
    public function currentAction()
    {
        return $this->_action;
    }

    /**
     * validates the request parameters according to
     * the rules passed in $valarray. It must be of the
     * following form:
     *
     * array(
     *   array("<param-name>","<regexp>","<error message>"),
     *   array("<param-name>","<regexp>","<error message>"),
     *   ...
     * )
     *
     * returns an array which contains all produced error
     * messages
     */
    public function validate($valarray)
    {
        if (!$valarray || !sizeof($valarray)) {
            return null;
        }

        $error = [];
        foreach ($valarray as $rule) {
            $param = $rule[0];
            $regex = $rule[1];
            $message = $rule[2];
            $val = $_REQUEST[$param];
            if (!preg_match("/" . $regex . "/", $val)) {
                $error[] = $message;
            }
        }
        return $error;
    }

    /**
     * Return current request method
     * @return <type>
     */
    public function currentRequestMethod()
    {
        return $this->_requestMethod;
    }

    public function getPath()
    {
        return implode("/", $this->_paths ?? []);
    }

    /**
     * Get or Set a value in the current context.
     *
     * If $append is true, append the value to the existing value.
     *
     * If $value is null, the current value will be returned.
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $append
     */
    public function ctx($key, $value = null, $append = false)
    {
        if (!is_numeric($key) && !is_scalar($key)) {
            LogService::getInstance($this)->error("Key given to ctx() was not numeric or scalar");
            return;
        }

        // There was a massive bug here, using == over === is BAD as $x == null
        // will be true for 0, "", null, false, etc. keep this in mind
        if ($value === null && func_num_args() === 1) {
            return !empty($this->_context[$key]) ? $this->_context[$key] : null;
        } else {
            if ($append) {
                $this->_context[$key] .= $value;
            } else {
                $this->_context[$key] = $value;
            }
        }
    }

    /**
     * get/put a session value
     */
    public function session($key, $value = null)
    {
        if ($value !== null) {
            $_SESSION[$key] = $value;
        }

        return !empty($_SESSION) && array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
    }

    /**
     * This function will retrieve data from session, but will also try to
     * update the value from the request function first, if both are null then
     * it will assign $default to the session $key
     *
     * @param string $key
     * @param mixed $default
     */
    public function sessionOrRequest($key, $default = null)
    {
        return $this->session($key, Request::mixed($key, !is_null($this->session($key)) ? $this->session($key) : $default));
    }

    public function sessionUnset($key)
    {
        unset($_SESSION[$key]);
    }

    public function sessionDestroy()
    {
        $_SESSION = [];


        // Check that we have an active session
        if (!session_id()) {
            return;
        }

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        // Finally, destroy the session.
        session_destroy();
    }

    /**
     * Send a browser redirect
     */
    public function redirect($url)
    {
        // stop endless loops!!
        if ($this->_action_redirected) {
            return;
        }
        $this->_action_redirected = true;

        // although we are redirecting we should
        // still call the POST modules and listeners
        // but only if we got redirected from a real action
        // we don't want to call these if redirected from
        // a role check or pre module/listener
        if ($this->_action_executed) {
            $this->_callWebHooks("after");
        }

        $this->header("Location: " . trim($url));
        exit();
    }

    /**
     * set http header values
     */
    public function sendHeader($key, $value)
    {
        $this->_headers[$key] = $value;
    }

    /**
     * Wrapper for PHP header function
     *
     * @param string $string
     *
     * @return null
     */
    public function header($string)
    {
        if (!headers_sent($file, $line)) {
            header($string);
        } else {
            LogService::getInstance($this)->error("Attempted to resend header {$string}, output started in {$file} on line {$line}");
        }
    }

    /**
     * returns a string representation of everything
     * session. request, url, headers, modules,
     * template contexts. This can then be displayed on the page
     * or written to the log.
     */
    public function dump()
    {
        echo "<pre>";
        echo "<b>========= WEB =========</b>";
        print_r($this);
        echo "<b>========= REQUEST =========</b>";
        print_r($_REQUEST);
        echo "<b>========= SESSION =========</b>";
        print_r($_SESSION);
        echo "</pre>";
    }

    /**
     *
     * Shortcut for setting the title of a page
     *
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->ctx("title", $title);
    }

    /**
     * parse a url and return and array like:
     *
     * array['module'] = <module>
     * array['submodule'] = <submodule> (or null)
     * array['action'] = <action>
     * array['tail'] = (the rest of the url)
     *
     * @param array
     */
    public function parseUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $split = $this->_getCommandPath($url);

        // Check for frontend/portal modules first
        $frontend_module = null;
        foreach ($this->modules() as $module) {
            // Module config must be active and either 'portal' or 'frontend' flag set to true
            if (Config::get($module . '.active') == true && (Config::get($module . '.portal') == true || Config::get($module . '.frontend') == true)) {
                if (strpos($_SERVER['HTTP_HOST'], Config::get($module . '.domain_name')) === 0) {
                    // Found module
                    $frontend_module = $module;
                    break;
                }
            }
        }

        $paths['module'] = null;
        $paths['submodule'] = null;

        if (!empty($frontend_module)) {
            $this->_loginpath = "auth";
            $this->_isFrontend = true;
            $this->_isPortal = true;

            $paths['module'] = $frontend_module;

            // now we have to decide whether the path points to
            // a) a single top level action
            // b) an action on a submodule
            // but we need to make sure not to mistake a path paramater for a submodule or an action!
            $domainsubmodules = $this->getSubmodules($frontend_module);
            if (!empty($domainsubmodules)) {
                $paths['submodule'] = $domainsubmodules;
            }

            if (!AuthService::getInstance($this)->loggedIn()) {
                $paths['action'] = 'login';
                return $paths;
            }
        } elseif (!empty($split)) {
            $paths['module'] = array_shift($split);
            // see if the module is a sub module
            // eg. /sales-report/showreport/1..
            $hsplit = explode("-", $paths['module']);
            if (sizeof($hsplit) == 2) {
                $paths['module'] = array_shift($hsplit);
                $paths['submodule'] = array_shift($hsplit);
            }
        }

        // then find the action
        $paths['action'] = null;
        if (AuthService::getInstance($this)->loggedIn() && AuthService::getInstance($this)->user()->redirect_url == $url) {
            $paths['action'] = 'index';
        }
        if (!empty($split)) {
            $paths['action'] = array_shift($split);
        }

        if (empty($paths['action'])) {
            $paths['action'] = $this->_defaultAction;
        }

        $paths['tail'] = $split;

        // var_dump($paths);
        return $paths;
    }

    /**
     * Test whether a url contains the passed values for
     * module, submodule and action
     *
     * Passing "*" to module, submodule or action allows any.
     *
     * To skip a submodule, pass in "" as parameter
     *
     * $action can be an array of actions
     *
     * @param string $url
     * @param string $module
     * @param string $submodule
     * @param string $action
     */
    public function checkUrl($url, $module, $submodule, $action)
    {
        $p = $this->parseUrl($url);

        if (empty($p) || empty($module)) {
            return false;
        }
        // first check the module
        if ($module != $p['module'] && $module != "*") {
            return false;
        }

        // then check the submodule
        if ($submodule != $p['submodule'] && $submodule != "*") {
            return false;
        }

        // now check actions
        if (empty($action)) {
            // if no action has been passed in for checking then assume anything is allowed
            return true;
        }
        if (is_array($action)) {
            // loop through all actions until we find one that matches
            $action_gate = false;
            foreach ($action as $ac) {
                if ($ac == $p['action']) {
                    $action_gate = true;
                }
            }
            return $action_gate;
        } elseif ($action != $p['action'] && $action != "*") {
            return false;
        }
        return true;
    }
}
///////////////////////////////////////////////////////////////////////////////
//                                                                           //
//                           Page Template System                            //
//                                                                           //
///////////////////////////////////////////////////////////////////////////////

class WebTemplate
{
    public $vars; /// Holds all the template variables

    /**
     * Constructor
     *
     * @param string $path the path to the templates
     *
     * @return void
     */

    public function __construct()
    {
        $this->vars = [];
    }

    /**
     * Set a template variable.
     *
     * @param string $name name of the variable to set
     * @param mixed $value the value of the variable
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * Set a bunch of variables at once using an associative array.
     *
     * @param array $vars array of vars to set
     * @param bool $clear whether to completely overwrite the existing vars
     *
     * @return void
     */
    public function setVars($vars, $clear = false)
    {
        if ($clear) {
            $this->vars = $vars;
        } else {
            if (is_array($vars)) {
                $this->vars = array_merge($this->vars, $vars);
            }
        }
    }

    /**
     * Open, parse, and return the template file.
     *
     * @param string string the template file name
     *
     * @return string
     */
    public function fetch($file)
    {
        extract($this->vars); // Extract the vars to local namespace
        ob_start(); // Start output buffering
        include $file; // Include the file
        $contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean(); // End buffering and discard
        return $contents; // Return the contents
    }
}

/**
 * An extension to Template that provides automatic caching of
 * template contents.
 */
// class CachedTemplate extends WebTemplate
// {
//     public $cache_id;
//     public $expire;
//     public $cached;

//     /**
//      * Constructor.
//      *
//      * @param string $path path to template files
//      * @param string $cache_id unique cache identifier
//      * @param int $expire number of seconds the cache will live
//      *
//      * @return void
//      */
//     public function __construct($path, $cache_id = null, $expire = 900)
//     {
//         $this->WebTemplate($path);
//         $this->cache_id = $cache_id ? 'cache/' . md5($cache_id) : $cache_id;
//         $this->expire = $expire;
//     }

//     /**
//      * Test to see whether the currently loaded cache_id has a valid
//      * corrosponding cache file.
//      *
//      * @return bool
//      */
//     public function is_cached()
//     {
//         if ($this->cached) {
//             return true;
//         }

//         // Passed a cache_id?
//         if (!$this->cache_id) {
//             return false;
//         }

//         // Cache file exists?
//         if (!file_exists($this->cache_id)) {
//             return false;
//         }

//         // Can get the time of the file?
//         if (!($mtime = filemtime($this->cache_id))) {
//             return false;
//         }

//         // Cache expired?
//         if (($mtime + $this->expire) < time()) {
//             @unlink($this->cache_id);
//             return false;
//         } else {
//             /**
//              * Cache the results of this is_cached() call.  Why?  So
//              * we don't have to double the overhead for each template.
//              * If we didn't cache, it would be hitting the file system
//              * twice as much (file_exists() & filemtime() [twice each]).
//              */
//             $this->cached = true;
//             return true;
//         }
//     }

//     /**
//      * This function returns a cached copy of a template (if it exists),
//      * otherwise, it parses it as normal and caches the content.
//      *
//      * @param $file string the template file
//      *
//      * @return string
//      */
//     public function fetch_cache($file)
//     {
//         if ($this->is_cached()) {
//             $fp = @fopen($this->cache_id, 'r');
//             $contents = fread($fp, filesize($this->cache_id));
//             fclose($fp);
//             return $contents;
//         } else {
//             $contents = $this->fetch($file);

//             // Write the cache
//             if ($fp = @fopen($this->cache_id, 'w')) {
//                 fwrite($fp, $contents);
//                 fclose($fp);
//             } else {
//                 die('Unable to write cache.');
//             }

//             return $contents;
//         }
//     }
// }

/**
 * License for Template and CachedTemplate classes:
 *
 * Copyright (c) 2003 Brian E. Lozier (brian@massassi.net)
 *
 * setVars() method contributed by Ricardo Garcia (Thanks!)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
