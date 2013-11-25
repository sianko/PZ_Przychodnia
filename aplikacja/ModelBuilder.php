<?php

use Zend\Loader\StandardAutoloader as Autoloader;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Adapter\Adapter as DbAdapter;

/////////////////////////////////////
//     START CONFIGURATION
/////////////////////////////////////
//
define('ZF2_PATH', '..');
define('TAB', '    ');
//define('TAB', "\t");

$moduleName = 'Application';

$dbconfig = array(
    'driver' => 'Mysqli',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '1234',
    'database' => 'przychodnia',
    'table_type' => 'InnoDB'
);

/////////////////////////////////////
//       END CONFIGURATION
/////////////////////////////////////

require_once ZF2_PATH . '/library/Zend/Loader/StandardAutoloader.php';

require_once ZF2_PATH . '/library/Zend/ServiceManager/FactoryInterface.php';
require_once ZF2_PATH . '/library/Zend/ServiceManager/AbstractFactoryInterface.php';

require_once ZF2_PATH . '/library/Zend/Db/Sql/ExpressionInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Sql/Predicate/PredicateInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Sql/Predicate/PredicateSet.php';
require_once ZF2_PATH . '/library/Zend/Db/Sql/Predicate/Predicate.php';

echo '===================================REQ================='."\n\r";
foreach (glob(ZF2_PATH . "/library/Zend/Db/*/*Interface.php") as $filename)
{
    echo $filename . "\n\r";
    require_once $filename;
}

foreach (glob(ZF2_PATH . "/library/Zend/Db/*/*/*Interface.php") as $filename)
{
    echo $filename . "\n\r";
    require_once $filename;
}

foreach (glob(ZF2_PATH . "/library/Zend/Db/*/*.php") as $filename)
{
    echo $filename . "\n\r";
    require_once $filename;
}

foreach (glob(ZF2_PATH . "/library/Zend/Db/*/*/*.php") as $filename)
{
    echo $filename . "\n\r";
    require_once $filename;
}



echo '=================================ENDREQ================='."\n\r";


/*
require_once ZF2_PATH . '/library/Zend/Db/Adapter/AdapterInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Profiler/ProfilerAwareInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/DriverInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/ConnectionInterface.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/StatementContainerInterface.php';

require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/StatementInterface.php';

require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/Mysqli/Statement.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/Mysqli/Connection.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Driver/Mysqli/Mysqli.php';
require_once ZF2_PATH . '/library/Zend/Db/Adapter/Adapter.php';
*/



$autoloader = new Autoloader;
$autoloader->register();

class ModelBuilder {
    protected $db;
    protected $stdin;
    protected $module;

    public function __construct($dbParams, $module) {
        $this->db = new DbAdapter($dbParams);
        $this->module = $module;
        $this->stdin = fopen('php://stdin', 'r');
    }

    public function run() {
        $tables = $this->getTablesToModel();
        $files = $this->getFiles($tables);
        $this->writeFiles($files);
    }

    public function getTablesToModel() {
        $metadata = new Metadata($this->db);

        foreach ($metadata->getTables() as $t) {
            if ($this->prompt($t->getName())) {
                $tables[] = $t;
            }
        }

        return $tables;
    }

    public function writeFiles($files) {
        foreach ($files as $dirname => $dir) {
            foreach ($dir as $file => $content) {
                $filename = $dirname . '/' . $file;
                @mkdir(dirname($filename), 0777, true);
                file_put_contents($filename, $content);
            }
        }
    }

    public function getFiles($tables) {
        foreach ($tables as $t) {
            $modelName = $this->toCamelCase($t->getName());
            $modelInterfaceName = $modelName . 'Interface';
            $mapperName = $modelName . 'Mapper';
            $mapperInterfaceName = $modelName . 'MapperInterface';

            $modelFile = <<<EOF
<?php

namespace {$this->module}\Model\\$modelName;

use ZfcBase\Model\ModelAbstract;

class $modelName extends ModelAbstract implements $modelInterfaceName
{

EOF;

            // fields
            foreach ($t->getColumns() as $col) {
                $colName = $this->toCamelCase($col->getName());
                $colName[0] = strtolower($colName[0]);
                $modelFile .= TAB . "protected \${$colName};\n";
            }

            $modelFile .= "\n";

            // getters and setters
            foreach ($t->getColumns() as $col) {
                $colName = $this->toCamelCase($col->getName());
                $colFuncName = $colName;
                $colName[0] = strtolower($colName[0]);
                $modelFile .= TAB . "public function get{$colFuncName}()\n";
                $modelFile .= TAB . "{\n";
                $modelFile .= TAB . TAB . "return \$this->$colName;\n";
                $modelFile .= TAB . "}\n\n";

                $modelFile .= TAB . "public function set{$colFuncName}(\$$colName)\n";
                $modelFile .= TAB . "{\n";
                $modelFile .= TAB . TAB . "\$this->$colName = \$$colName;\n";
                $modelFile .= TAB . TAB . "return \$this;\n";
                $modelFile .= TAB . "}\n\n";
            }

            $modelFile .= "}";

            $files[$modelName][$modelName . '.php'] = $modelFile;

            /**
             * MODEL INTERFACE START
             */
            $iModelFile = <<<EOF
<?php

namespace {$this->module}\Model\\$modelName;

interface $modelInterfaceName
{

EOF;
            
            foreach ($t->getColumns() as $col) {
                $colName = $this->toCamelCase($col->getName());
                $colFuncName = $colName;
                $colName[0] = strtolower($colName[0]);
                $iModelFile .= TAB . "public function get{$colFuncName}();\n";
                $iModelFile .= TAB . "public function set{$colFuncName}(\$$colName);\n";
            }

            $iModelFile .= "}";

            $files[$modelName][$modelInterfaceName . '.php'] = $iModelFile;

            /**
             * MAPPER INTERFACE START
             */

            $iMapperFile = <<<EOF
<?php

namespace {$this->module}\Model\\$modelName;

interface $mapperInterfaceName
{
}
EOF;

            $files[$modelName][$mapperInterfaceName . '.php'] = $iMapperFile;

            /**
             * MAPPER START
             */

            $mapperFile = <<<EOF
<?php

namespace {$this->module}\Model\\$modelName;

use ZfcBase\Mapper\DbMapperAbstract;

class $mapperName extends DbMapperAbstract implements $mapperInterfaceName
{
    protected \$tableName = '{$t->getName()}';
}
EOF;

            
            $files[$modelName][$mapperName . '.php'] = $mapperFile;
        }

        return $files;
    }

    protected function prompt($tableName) {
        echo "Do you want to model this table ($tableName)? (y/N) ";
        $answer = fgets($this->stdin, 256);
        if (strtolower($answer[0]) === 'y') {
            return true;
        } else {
            return false;
        }
    }
    
    protected function toCamelCase($name)
    {
        return implode('',array_map('ucfirst', explode('_',$name)));
    }

    protected static function fromCamelCase($name)
    {
        return trim(preg_replace_callback('/([A-Z])/', function($c){ return '_'.strtolower($c[1]); }, $name),'_');
    }
}

$builder = new ModelBuilder($dbconfig, $moduleName);
$builder->run();
