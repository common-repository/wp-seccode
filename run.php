<?php
/*
Plugin Name: wp-seccode
Plugin URI: http://www.youjisuan.com/project/wp-seccode
Description: wp-seccode is Wrodpress Security Code,it will protect your blog away from spam.
Version: 0.160
Author: wangwei
Author URI: http://www.walleve.com
*/

if(!class_exists('wp_seccode')) {
    class wp_seccode {
        function __construct() {
            add_action('comment_form', array(& $this, 'comment_seccode'));
            add_filter('preprocess_comment', array(& $this, 'preprocess_comment'));
        }
        
        function plugin_url(){
            return get_option('home').'/wp-content/plugins/wp-seccode';
        }

        function is_loggin(){
            return is_user_logged_in();
        }
                
        function comment_seccode() {
            if(!$this->is_loggin()) {
                $post_rand = substr(md5(mt_rand(0,99999)),0,4);
                
                $str = '<div id="wp_seccode">';
                $str .= '<script>function update_wpseccode(){var secc = document.getElementById("seccode-img");
                        var imgurl = "'.$this->plugin_url().'/wp-seccode.php?id='.get_the_ID().'&p='.$post_rand.'";
                        secc.style.display="";
                        secc.src=imgurl;}</script>'
                    ;
                $str .= '<label for="seccode">Sec-code</label>';
                $str .= '<input type="hidden" value="'.$post_rand.'" name="sechash"/>';
                $str .= '<input type="text" name="seccodes" id="seccode"  size="5" maxlength="4" tabindex="4" onfocus="update_wpseccode();this.onfocus = null;" />';
                $str .= '<img id="seccode-img" style="display:none" src="ss" alt="click to change" border="0" onclick="this.src=\''.$this->plugin_url().'/wp-seccode.php?id='.get_the_ID().'&p='.$post_rand.'&update=\' + Math.random()" />';
                $str .= '</div>';
                
                echo $str;
            }
        }
        
        function preprocess_comment($commentdata) {
            if(!$this->is_loggin()){
                session_start();
                $_POST['comment_post_ID']?'':$_POST['comment_post_ID']=0;
                $post_rand = $_POST['sechash'];
                
                if (strtolower($_POST['seccodes']) != $_SESSION['seccode_'.$_POST['comment_post_ID']][$post_rand] || empty($_SESSION['seccode_'.$_POST['comment_post_ID']][$post_rand]) || empty($_POST['seccodes'])) {
                    wp_die( __('Error: Please enter a valid seccode.') );
                }
                unset($_SESSION['seccode_'.$_POST['comment_post_ID']][$post_rand]);
            }
            return $commentdata;
        }
        
        function sign_id($id){
            return md5(substr(md5($id),10,20));
        }
    }

}

if( !isset($wp_seccode) ) {
	$wp_seccode =& new wp_seccode();
}
?>