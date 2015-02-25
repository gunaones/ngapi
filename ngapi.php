<?php
/*
(___)(___)  ( \( )/ __)  /__\  (  _ \(_  _)  (___)(___)
 ___  ___    )  (( (_-. /(__)\  )___/ _)(_    ___  ___ 
(___)(___)  (_)\_)\___/(__)(__)(__)  (____)  (___)(___)
*/
/**
 * class Ngapi v 1.0
 * 
 * start 20150224 ^_^
 * 
 * @author 		Gunaones
 * @copyright	2015 Gunaones
 * @link 		https://github.com/gunaones/ngapi
 * @license 	https://github.com/gunaones/ngapi
 * @version 	1.0
 * @package 	Ngapi 
 */
class Ngapi
{
	var $loadType = 'class';

	function __construct(){
		global $app, $autoload, $ngroutes;
		$this->app = $app;
		$this->autoload = $autoload;
		$this->ngroutes = $ngroutes;
	}

	/**
	* welcome msg
	*
	* @return view
	*/
	function welcome() {
		echo json_encode('Ngapi is active');
	}


	/**
	* autoload classes (no need to include them one by one)
	*
	* @uses classFolder()
	* @param $className string
	*/
	function __autoload($className) {
	    $folder = $this->classFolder($className);
	    if($folder)
	        require_once($folder.$className.'_'.$this->loadType.".php");
	}

	/**
	* search for folders and subfolders with classes
	*
	* @param $className string
	* @param $sub string[optional]
	* @return string
	*/
	function classFolder($className, $sub = "/") {
	    try{
			$dir = dir(CLASS_DIR.$sub);

			if(file_exists(CLASS_DIR.$sub.$className.'_'.$this->loadType.".php"))
			    return CLASS_DIR.$sub;

			while(false !== ($folder = $dir->read())) {
			    if($folder != "." && $folder != "..") {
			        if(is_dir(CLASS_DIR.$sub.$folder)) {
			            $subFolder = $this->classFolder($className, $sub.$folder."/");
			           
			            if($subFolder)
			                return $subFolder;
			        }
			    }
			}
			$dir->close();
		}catch(Exception $e){
			echo $e->getMessage();
		}
	    return false;
	}
	
	/**
	* Load All model
	*
	* 
	* @return looping autoload model
	*/	
	function loadAll($autoload){
		foreach ($autoload as $loadType => $files) {
			$this->loadType = $loadType;
			foreach ($files as $file) {
				$this->__autoload($file);
			}
		}
	}
	
	/**
	* Run Ngapi
	*
	* 
	* @return run ngapi with slimframework
	*/
	function run(){
		$this->loadAll($this->autoload);

		foreach ($this->ngroutes as $routename => $routelist) {
			foreach ($routelist as $route) {
				eval("\$this->app->$route[0](\"$route[1]\",\"$route[2]\");");
			}
		}
		$this->app->run();
	}

	/**
	* getConnection
	*
	* @param $config array global
	* @return connection
	*/
	function getConnection() {
		global $config;
		$dbhost=isset($config['db']['hostname'])?$config['db']['hostname']:'localhost';
		$dbuser=isset($config['db']['username'])?$config['db']['username']:'';
		$dbpass=isset($config['db']['password'])?$config['db']['password']:'';
		$dbname=$config['db']['database'];
		$dbdriver=$config['db']['dbdriver'];
		switch ($dbdriver) {
			case 'mysql':
				$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
				break;
			case 'sqlite':
				$dbh = new PDO("sqlite:".$dbname, $dbuser, $dbpass,array(PDO::ATTR_PERSISTENT => true));
				break;
			
			default:
				# code...
				break;
		}
		// $dbh = new PDO("sqlite:simkari_diklat.sqlite3", $dbuser, $dbpass,array(PDO::ATTR_PERSISTENT => true));
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}
}

/**
* load slimframework
* 
*/	
require dirname(dirname(__FILE__)).'/vendor/autoload.php';
$app = new \Slim\Slim();
?>
