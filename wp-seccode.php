<?PHP
/**
 * -------
 * @author wangwei <bengshuo@gmail.com>
 * -------
 */

error_reporting(0);

//+------配置文件-----------------
$width='50';	                        //图片宽
$height='25';	                        //图片高
$bgcolor = array('255','255','255');	//背景色
$borders = array('220','215','215');	//边框色
$disturbcolor = array(220,200,200);	    //干扰色
//+-----------------------------

session_start();

$post_rand = $_GET['p']?$_GET['p']:'';
$post_id = $_GET['id']?$_GET['id']:0;

$_SESSION['seccode_'.$post_id][$post_rand] = array();
$start_i = mt_rand(0,20);
$code = substr(md5(mkseccode()|$post_id),$start_i,4);
$_SESSION['seccode_'.$post_id][$post_rand] = strtolower($code);

@header("Expires: -1");
@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");

if(function_exists('imagecreate') && function_exists('imagecolorset') && function_exists('imagecopyresized') && function_exists('imagecolorallocate') && function_exists('imagesetpixel') && function_exists('imagechar') && function_exists('imagecreatefromgif') && function_exists('imagepng')) {
    //创建图像
    $im = ((function_exists('imagecreatetruecolor')) && PHP_VERSION >= '4.3')?imagecreatetruecolor($width, $height):imagecreate($width, $height);

    //填充背景
	$backgroundcolor = imagecolorallocate ($im, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
    imagefill($im,0,0,$backgroundcolor);

    //干扰线条
    $im = img_disturb($im,$width,$height,$disturbcolor);
    
    //填充文字
	$numorder = array(1, 2, 3, 4);
	shuffle($numorder);
	$numorder = array_flip($numorder);
	for($i = 1; $i <= 4; $i++) {
		$x = $numorder[$i] * 10 + mt_rand(0, 4) - 2;
		$y = mt_rand(0, 5);
        
		$text_color = imagecolorallocate($im, mt_rand(50, 200), mt_rand(50, 128), mt_rand(50, 200));
		imagestring($im, 5, $x + 5, ($y + 3)%360, $code[$numorder[$i]], $text_color);
	}
    
	//画边框
	$im = img_border($im,$width,$height,$borders);

    //输出图像
	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);

}

/**
 * @生成种子
 */
function mkseccode() {
	$seccode = random(6);
	$s = sprintf('%04s', base_convert($seccode,10,24));
	$seccode = array();
	$seccodeunits = 'BCEFGHKMNPQRVWXY2346789';
	for($i = 0; $i < 4; $i++) {
		$unit = ord($s{$i});
		$seccode[$i]= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
	}
    shuffle($seccode);
    $secstr = implode('',$seccode);
    if(strlen($secstr)<4){
        $secstr.=mt_rand(0,9);
    }
	return $secstr;
}

/**
 * @随机函数
 */
function random($length) {
	PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
	$seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, 10);
	$seed = str_replace('0', '', $seed).'012340567890';
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 * @图像干扰
 */
function img_disturb($im,$width,$height,$disturbcolor) {
	$linenums = $height/10;
	for($i=0; $i <= $linenums; $i++) {
		$color = imagecolorallocate($im, $disturbcolor[0], $disturbcolor[1], $disturbcolor[2]);
		$x = mt_rand(0, $width);
		$y = mt_rand(0, $height);
		if(mt_rand(0, 1)) {
			imagearc($im, $x, $y, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, 360), mt_rand(0, 360), $color);
		} else {
			imageline($im, $x, $y, mt_rand(0, 20), mt_rand(0, mt_rand($height, $width)), $color);
		}
	}
	return $im;
}

/**
 * @图像边框
 */
function img_border($im,$width,$height,$borders){
    $bordercolor = imagecolorallocate($im , $borders[0], $borders[1], $borders[2]);//取消边框是不是更爽
	imagerectangle($im, 0, 0, $width-1, $height-1, $bordercolor);
    return $im;
}
?>