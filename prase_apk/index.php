<?
$filepath='xxx.apk';
print_r($filepath);
print_r(_getApkInfos($filepath));
	function _getApkInfos($filepath){
		$result = array();
		if(!is_file($filepath))
		{
			return $result;
		}

		$zip_dir = 'source/';
		exec('unzip -oq "' . $filepath . '" AndroidManifest.xml -d ' . $zip_dir);
		if(!file_exists($zip_dir . 'AndroidManifest.xml'))
		{
			return $result;
		}
		//执行jar命令
		exec('java -jar ' . 'lib/AXMLPrinter2.jar ' . $zip_dir . 'AndroidManifest.xml > ' . $zip_dir . 'temp.xml');
		if(!file_exists($zip_dir . 'temp.xml')){
			return $result;
		}
		//读取xml文件
		include_once 'lib/XmlUtil.php';
			
		$xml = new XmlUtil();
		$xml->parseFile($zip_dir . 'temp.xml');
		$tree	=	$xml->getTree();
		if(!$tree){
			return $result;
		}
		$packname 	=	$tree['manifest']['package'];
		$codeid 	=	$tree['manifest']['android:versionCode'];
		$codename 	=	$tree['manifest']['android:versionName'];
			
		$result = array(
				'softwarebag' => $packname,
				'versioncode' => $codeid,
				'versionname' => $codename,
		);
		unset($tree);
		unset($xml);
			
		unlink($zip_dir . 'temp.xml');
		unlink($zip_dir. 'AndroidManifest.xml');
		return $result;
}
?>