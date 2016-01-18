<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "mecreativedesigner@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "6ad8bd" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'01F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA6Y6IImxBjAGsDYwBAQgiYlMYQWKMTqIIIkFtDIgi4GdFLUUiEJXRYUhuQ+ijmEqpl6guSh2gMVQ7GANYMBwC6MDayjIPGQ3D1T4URFicR8AfX7IdG2lVjAAAAAASUVORK5CYII=',
			'0F62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxBog0MDo6BAQgiYlMEWlgbXB0EEESC2gFiQHlkNwXtXRq2NKpQBrJfWB1jg6NDhh6A1oZMOwImMKAxS2obgbaGMoYGjIIwo+KEIv7AKrFy7x1G115AAAAAElFTkSuQmCC',
			'C9A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQximMEx1QBITaWVtZQhlCAhAEgtoFGl0dHR0EEEWaxBpdG0IgKkDOylq1dKlqauipmYhuS+ggTEQSR1UjKHRNTQQ1bxGFqB5qGIgt7Ci6QW5GSiG4uaBCj8qQizuAwAHjc3ePB9sAwAAAABJRU5ErkJggg==',
			'BCE0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDHVqRxQKmsDa6NjBMdUAWaxVpAIoFBKCoE2lgbWB0EEFyX2jUtFVLQ1dmTUNyH5o6uHnYxDDtwHQLNjcPVPhREWJxHwD55M2c2mMYYAAAAABJRU5ErkJggg==',
			'A2B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgDWFtZGx2mIouJTBFpdG0ICEUWC2hlaHRtdIDpBTspaumqpUtDVy1Fdh9Q3RRWhDowDA1lCGAFyaCYx+iAKcbagK43oFU01DWUITRgEIQfFSEW9wEASAfNXuAZaXoAAAAASUVORK5CYII=',
			'E249' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHaY6IIkFNLC2MrQ6BASgiIkAVTk6iKCIAXUGwsXATgqNWrV0ZWZWVBiS+4DqprACdaPpDWANBZqKIsboADQRzQ5WkC0obgkNEQ11QHPzQIUfFSEW9wEAdQvOBGuYk+IAAAAASUVORK5CYII=',
			'E1D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUIdkMQCGhgDWBsdHQJQxFgDWIGkCIoYA1gsAMl9oVGropYCURaS+9DUoYhhMw9DDM0toSGsoehuHqjwoyLE4j4AvivMqSh6JxAAAAAASUVORK5CYII=',
			'BE5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHUNDkMQCpog0sDYwOiCrC2jFIgZSNxUuBnZSaNTUsKWZmaFZSO4DqWNoCMQwD5sYK7oYUC+joyOKGMjNDKGobhmo8KMixOI+AFCxypBoFyuWAAAAAElFTkSuQmCC',
			'43AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpI37prCGMExhmBqALBYi0soQyhAggiTGGMLQ6Ojo6MCCJMY6haGVtSHQAdl906atClu6KjIL2X0BqOrAMDSUodE1FFUM6I5GV6A6FhQxEaDeABS3gNwMFEN180CFH/UgFvcBAAz9y6H5nyGJAAAAAElFTkSuQmCC',
			'80DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMDkMREpjCGsDY6OiCrC2hlbWVtCEQRE5ki0uiKEAM7aWnUtJWpqyJDs5Dch6YOah42MWx2YLoFm5sHKvyoCLG4DwBwtcrSj5JwlQAAAABJRU5ErkJggg==',
			'AB4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUMdkMRYA0RaGVodHQKQxESmiDQ6THV0EEESC2gFqguEi4GdFLV0atjKzMysaUjuA6ljbUTVGxoq0ugaGohuXqNDIxY7GlHdEtCK6eaBCj8qQizuAwDce80MphJB8QAAAABJRU5ErkJggg==',
			'A004' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMDQEIImxBjCGMIQyNCKLiUxhbWV0dGhFFgtoFWl0bQiYEoDkvqil01amroqKikJyH0RdoAOy3tBQsFhoCIp5YDsaUO0AuwVNDNPNAxV+VIRY3AcA9h7N7sy+V4gAAAAASUVORK5CYII=',
			'A3B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGRoCkMRYA0RaWRsdGpHFRKYwNLo2BLQiiwW0MoDUTQlAcl/U0lVhS0NXRUUhuQ+iztEBWW9oKMi8wNAQVPNAdjSg2gF2C5oYppsHKvyoCLG4DwCG7s808lygIAAAAABJRU5ErkJggg==',
			'43C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37prCGMIQ6hgYgi4WItDI6BDogq2MMYWh0bRBEEWOdwtDK2sDo6oDkvmnTVoUtXbUyKgrJfQFgdQwNIkh6Q0NB5qGKMUyB2IEqBnJLQACK+8BudpjqMBjCj3oQi/sAd0DLGTbDCPUAAAAASUVORK5CYII=',
			'2AA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQytrK6OjoIIKsu1Wk0bUhAKYO4qZp01amroqamoXsvgAUdWDI6CAa6hoaiGIeawNIHaqYSAOm3tBQsBiKmwcq/KgIsbgPAOi6zUZSHscvAAAAAElFTkSuQmCC',
			'D30F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNYQximMIaGIIkFTBFpZQhldEBWF9DK0Ojo6Igu1sraEAgTAzspaumqsKWrIkOzkNyHpg5unisWMQw7sLgF6mYUsYEKPypCLO4DAASJyyR68A5FAAAAAElFTkSuQmCC',
			'BE44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHRoCkMQCpog0MLQ6NKKItQLFpjq0YqgLdJgSgOS+0KipYSszs6KikNwHUsfa6OiAbh5raGBoCLod2NyCJobNzQMVflSEWNwHANlRz/LUjqTBAAAAAElFTkSuQmCC',
			'7870' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1pRRFtZgfyAqQ4oYiKNDg0BAQHIYlOA6hodHUSQ3Re1MmzV0pVZ05Dcx+gAVDeFEaYODFkbgOYFoIqJAMUcHRhQ7AhoYG1lbWBAcUtAA9DNQBcNhvCjIsTiPgAdIswGds3VfQAAAABJRU5ErkJggg==',
			'6A4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHUNDkMREpjCGMLQ6OiCrC2hhbWWYiibWINLoEAgXAzspMmrayszMzNAsJPeFTBFpdG1E09sqGuoaGogmBjQPTZ3IFEwx1gBMsYEKPypCLO4DAF5Qy7pWu4sCAAAAAElFTkSuQmCC',
			'FEF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAS0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA1qRxQIaRBpYGximYhELxSIG0wt2UmjU1LCloauWIrsPTR0VxIBuBrolYBCEHxUhFvcBAF0ezGGkRBr0AAAAAElFTkSuQmCC',
			'42E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGsIY6THVAFgthbWVtYAgIQBJjDBFpdG1gdBBBEmOdwgAUg6sDO2natFVLl4aumpqF5L6AKQxT0M0LDWUIYEUzD+gWB0wx1gZ0vQxTRENd0d08UOFHPYjFfQALzMtSVXvu4wAAAABJRU5ErkJggg==',
			'E2CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHUMdkMQCGlhbGR0CHQJQxEQaXRsEHURQxBiAYowwdWAnhUatWrp01crQLCT3AeWnsCLUwcQCQGKo5jE6sGLYAVKF6pbQENFQBzQ3D1T4URFicR8AVyfMP05JoB4AAAAASUVORK5CYII=',
			'01F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0NDkMRYAxgDWIG0CJKYyBRWDLGAVgawWACS+6KWAlHoqpVZSO6DqmtlwNQ7hQHFDrBYALIYawBIDOhKFDezhqKLDVT4URFicR8AmOTIPwfsu8AAAAAASUVORK5CYII=',
			'301B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7RAMYAhimMIY6IIkFTGEMYQhhdAhAVtnK2soIFBNBFpsi0ugwBa4O7KSVUdNWZk1bGZqF7D5UdVDzIGIiaHYwoImB3YKmF+RmxlBHFDcPVPhREWJxHwCpQ8pBm/+XywAAAABJRU5ErkJggg==',
			'9418' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMEx1QBITAfIZQhgCApDEAloZQhlDGB1EUMQYXYF6YerATpo2denSVdNWTc1Cch+rq0grkjoIbBUNdZiCap5AK8gtqGJAt2DoBbmZMdQBxc0DFX5UhFjcBwDD18sYilzw0QAAAABJRU5ErkJggg==',
			'6731' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQx1DGVqRxUSmMDS6NjpMRRYLaGFodGgICEURawDqa3SA6QU7KTJq1bRVU1ctRXZfyBSGACR1EL2tjA4gElWMtQFdTGSKSAMrml7WAJEGxlCG0IBBEH5UhFjcBwDOA81sSZWyuwAAAABJRU5ErkJggg==',
			'B367' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUNDkMQCpoi0Mjo6NIggi7UyNLo2oIlNYWhlBdFI7guNWhW2dOqqlVlI7gOrc3RoZcAwDyiDKRbAgOEWRwcsbkYRG6jwoyLE4j4ArhfNMDRDGdkAAAAASUVORK5CYII=',
			'BD62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtcHQQQVUHFGNoEEFyX2jUtJWpU1etikJyH1ido0OjA4Z5Aa0MmGJTGLC4BdPNjKEhgyD8qAixuA8AbePOwcJaSRcAAAAASUVORK5CYII=',
			'2FA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIaGIImJTBFpYAgF0khiAa0iDYyODihiDEAx1oYAIERy37SpYUtXRa3MQnZfAFhdK7K9jA5AsdCAKShuaQCrC0AWEwGLBTogi4WGYooNVPhREWJxHwDwsMvySaZwWQAAAABJRU5ErkJggg==',
			'06FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MdkMRYA1hbWYEyAUhiIlNEGkFiIkhiAa0iDUjqwE6KWjotbGnoytAsJPcFtIpimAfU2+iKZh7IDnQxbG4Bu7mBEcXNAxV+VIRY3AcAaNnJ/xxu+64AAAAASUVORK5CYII=',
			'C447' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WEMYWhkaHUNDkMREWhmmMrQ6NIggiQU0MoQyTEUTa2B0ZQh0ANII90WtWrp0ZWbWyiwk9wUATWRtdGhlQNErGuoaGjCFAdUOoFscAhhQ3QJynwMWN6OIDVT4URFicR8ANGfM0UnL+E4AAAAASUVORK5CYII=',
			'4C3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37pjCGMoYytKKIhbA2ujY6THVAEmMMEWlwaAgICEASY50i0sDQ6OggguS+adOmrVo1dWXWNCT3BaCqA8PQUBAvMDQExS0gOwJR1DFMAbnFEU0M5GZGVLGBCj/qQSzuAwAPo8zSeWSeZAAAAABJRU5ErkJggg==',
			'F0F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDAxoCkMQCGhhDWBsYGlHFWFuBYq2oYiKNrg0MUwKQ3BcaNW1lauiqqCgk90HUMTpg6mUMDcG0A5tb0MSAbkYTG6jwoyLE4j4ApnvOL9vpi3kAAAAASUVORK5CYII=',
			'7C65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQxmAOABZtJW10dHR0QFFZatIg2sDmtgUkQbWBkZXB2T3RU1btXTqyqgoJPcxOgDVOTo0iCDpZW0A6Q1AERNpANkR6IAsFtAAcotDQACKGMjNDFMdBkH4URFicR8AVMLLzI6nQ5gAAAAASUVORK5CYII=',
			'959A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAfElEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQBULYW0IdBBBct+0qVOXrsyMzJqG5D5WV4ZGhxC4OghsBYo1BIaGIIkJtIo0OjagqhOZwtrK6OiIIsYawBjCEMqIat4AhR8VIRb3AQBCPss9SFdOYwAAAABJRU5ErkJggg==',
			'4E1E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37poiGMkxhDA1AFgsRAWJGB2R1jEAxRjQx1ilAdVPgYmAnTZs2NWzVtJWhWUjuC0BVB4ahoZhiDFjUYRcTDWUMdUR180CFH/UgFvcBAMHFyNS7JYwGAAAAAElFTkSuQmCC',
			'456C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpI37poiGMoQyTA1AFgsRaWB0dAgQQRJjBIqxNjg6sCCJsU4RCWFtYHRAdt+0aVOXLp26MgvZfQFTGBpdHR0dkO0NDQWKNQQ6oLpFBCzGgiLG2oruFoYpjCEYbh6o8KMexOI+AHuuyv/tvhp8AAAAAElFTkSuQmCC',
			'B785' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsRGAMAhFocgGcR8o0mNBk2lI4QbRHcyUpiRqqXeB7t0H3gHtUQYz9S9+KouSoopjUqEwM/mcbFCSrSOrsCFzIuenuR1Nz5ydX88JMlkc9iEFkxsLFvqNgdVofVa8n0pPKOw0wf8+7Be/C7zEzK9pQu43AAAAAElFTkSuQmCC',
			'1FA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEx1QBJjdRBpYAhlCAhAEhMFijE6OoJkkPSKNLA2BDSIILlvZdbUsKWrooAQ4T6oukYHdL2hAa0MmOZNwSIWgCwmGgISCwwNGQThR0WIxX0AfbzKOYSXVscAAAAASUVORK5CYII=',
			'D3F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDAxoCkMQCpoi0sjYwNKKItTI0ujYwtKKJgdRNCUByX9TSVWFLQ1dFRSG5D6KO0QHTPMbQEEw7sLkFRQzsZjSxgQo/KkIs7gMAl7bO5Tr4gP8AAAAASUVORK5CYII=',
			'2716' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsRGAQAgEITA3wH6wA4L/Ekys4g2+A7QHrVLMQA11lM1u5m52gO1yBf7EK36NdJkVZnYZKUycQMRlUmHqE3Lr29VQ5OC3HKzD6P3EUAx7yNazLnkX45yRARpdcqaCmYPzV/97kBu/HT+8yptMnv+EAAAAAElFTkSuQmCC',
			'F7B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DGVqRxQIaGBpdGx2mOqCLNQQEBKCKtbI2OjqIILkvNGrVtKWhK7OmIbkPqC4ASR1UjNGBtSEQTYwVCNHtEGlgxXALUAzNzQMVflSEWNwHAMu3zjRNhF8JAAAAAElFTkSuQmCC',
			'5C48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQxkaHaY6IIkFNLA2OrQ6BASgiIk0OEx1dBBBEgsMAPIC4erATgqbNm3VysysqVnI7msVAZmIYh5YLDQQxbwAoJhDI6odIlOAOtH0sgZgunmgwo+KEIv7AMNTzhz/LsR4AAAAAElFTkSuQmCC',
			'421E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjCGAHFoALJYCGsrQwijA7I6xhCRRkc0MdYpDI0OU+BiYCdNm7Zq6appK0OzkNwXMIUBCFH1hoYyBKCLgfkYYqwNmGKioY5AiOLmgQo/6kEs7gMAMr/JIcff8AoAAAAASUVORK5CYII=',
			'C8A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WEMYQximMLQii4m0srYyhDJMRRYLaBRpdHR0CEURa2BtZW0IgOkFOylq1cqwpauiliK7D00dVEyk0TUUTQxohyuaOpBb0PWC3AwUCw0YBOFHRYjFfQBm0s142KBVigAAAABJRU5ErkJggg==',
			'6858' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoEWl0bWB0EEEWawCqmwpXB3ZSZNTKsKWZWVOzkNwXAjQPqBrVvFaRRoeGQFTzWkF2oIqB3MLo6ICiF+RmhlAGFDcPVPhREWJxHwC4fMyo7V5dVgAAAABJRU5ErkJggg==',
			'670D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTGFodAhldAhAEgtoYWh0dHR0EEEWa2BoZW0IhImBnRQZtWra0lWRWdOQ3BcyhSEASR1EbyujA6YYawMjmh0iU4A8NLewBgDF0Nw8UOFHRYjFfQA9fstRFsKr0QAAAABJRU5ErkJggg==',
			'B431' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGVqRxQKmMExlbXSYiiLWyhAKJENR1TG6MjQ6wPSCnRQatXTpqqmrliK7L2CKSCuSOqh5oqEOIFNR7WhlQBebwtDKiqYX6ubQgEEQflSEWNwHAJL3zjNPS6CVAAAAAElFTkSuQmCC',
			'12A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ3AIAwETeENYB9T0DsSNBkhU5iCDcgIFGHK0AWSlImEvzv9SydDfZzATPnFT5HykGGnjiFhggDMHTOko7WW9LCF6IRFd37HVkupa8vl13oZhSONW8bA6eZCrZdHhtIY98x4E5wswU/wvw/z4ncCYkLKHIpHU7AAAAAASUVORK5CYII=',
			'C585' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ3AIAwETcEGzj6moAfJbpjGFIzACCnClCGdUVImUvzd6/0+GcZtFP6kT/g8bwLiJBkPG6oLgWwuVVSvefUUeeYiGb4y+j7kKMXwze466xSXXajxalhvTC8TLiy+uUDJ8nl2DAKdfvC/F/XAdwJ5/MvcfDxtQQAAAABJRU5ErkJggg==',
			'820D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoFWl0dHR0EEFRx9Do2hAIEwM7aWnUqqVLV0VmTUNyH1DdFFaEOqh5DAGYYowOjBh2sDagu4U1QDTUAc3NAxV+VIRY3AcA4xnLH+15cQ0AAAAASUVORK5CYII=',
			'6EBF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUNDkMREpog0sDY6OiCrC2gBijUEooo1oKgDOykyamrY0tCVoVlI7gvBZl4rFvOwiGFzC9TNKGIDFX5UhFjcBwD/OcpYp0Pb3AAAAABJRU5ErkJggg==',
			'25F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6Y6IImJTBFpYG1gCAhAEgtoBYkxOogg624VCUFSB3HTtKlLl4aumpqF7L4AhkZXNPMYHUBiqOaxNohgiAFtbUV3S2goI8heFDcPVPhREWJxHwAH5ss+NmC6QwAAAABJRU5ErkJggg==',
			'47BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37poiGuoYytKKIhTA0ujY6THVAEmMEiTUEBAQgibFOYWhlbXR0EEFy37Rpq6YtDV2ZNQ3JfQFTGAKQ1IFhaCijA2tDYGgIiltYG4BiKOoYpog0oOsFiwH1o4gNVPhRD2JxHwAxjMwB9RnTuAAAAABJRU5ErkJggg==',
			'9779' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM3QMQ6AIAxA0Tp0d6j3wYG9Djh4mjpwA+AGLpxSJDEp0VGj7fYSwk8hX0bgT/tKH/LgrONolFGA1QgzK2N/2GSotaLjaTUpxZzylpdZ9aEFhgBRvwXfmaKirfcoRZs/KJCgQNOCXK1p/up+D+5N3w4Zh8u1qpmzWQAAAABJRU5ErkJggg==',
			'5A0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIaGIIkFNDCGMIQyOjCgiLG2Mjo6oogFBog0ujYEwsTATgqbNm1l6qrI0Cxk97WiqIOKiYaiiwUA1Tmi2SEyRaTRAc0trEB7Haagig1U+FERYnEfAO7VymzTDFcoAAAAAElFTkSuQmCC',
			'D927' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsQ2AIBBF/yWyAQOdhf2RQOMITgHFbcAKFDqllICWGr3XveQnL4fjchF/4pW+IOQRKPjGSTZKM0fbOrVpiXJxXJ00fWspZdsrTZ8oOVYoui0SZ+TeTYkFgrGFicdmE1znvvrfg9z0ne0yzTycLX7JAAAAAElFTkSuQmCC',
			'3A01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYAhimMLQiiwVMYQxhCGWYiqKylbWV0dEhFEVsikija0MATC/YSSujpq1MXRW1FMV9qOqg5omGYoqJNDo6OqC5RaQRaCuKmGgAUGwKQ2jAIAg/KkIs7gMAOfrMkq0CYzsAAAAASUVORK5CYII=',
			'7761' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGVpRRFsZGh0dHaaii7k2AFUii01haGVtgOuFuClq1bSlU1ctRXYfowNDAKujA4odrEBR1oYAFDERoCi6WABQlBFNL0iMIZQhNGAQhB8VIRb3AQCe1cvI3fjNMwAAAABJRU5ErkJggg==',
			'A50D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxBog0MIQyOgQgiYlMEWlgdHR0EEESC2gVCWFtCISJgZ0UtXTq0qWrIrOmIbkvoJWh0RWhDgxDQzHFgOY1OmLYwdqK7paAVsYQdDcPVPhREWJxHwAw4cu5+jWRwgAAAABJRU5ErkJggg==',
			'2308' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANYQximMEx1QBITmSLSyhDKEBCAJBbQytDo6OjoIIKsu5WhlbUhAKYO4qZpq8KWroqamoXsvgAUdWDI6MDQ6NoQiGIeawOmHSINmG4JDcV080CFHxUhFvcBAF7zy42PcZjpAAAAAElFTkSuQmCC',
			'C7EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WENEQ11DHaYGIImJtDI0ujYwBIggiQU0gsQYHViQxRoYWlmBYsjui1q1atrS0JVZyO4DqgtAUgcVY3TAEGtkbWBFs0OkVQQohuoW1hCgGJqbByr8qAixuA8AvPjK4ZSvaCwAAAAASUVORK5CYII=',
			'F255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMDkMQCGlhbWRsYHRhQxEQaXTHEGBpdpzK6OiC5LzRq1dKlmZlRUUjuA6qbAjYBVW8AphijA2tDoAOqGNAljg4BqO4TDXUIZZjqMAjCj4oQi/sAdIvMkZe6dLgAAAAASUVORK5CYII=',
			'09BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUMDkMRYA1hbWRsdHZDViUwRaXRtCEQRC2gFiiHUgZ0UtXTp0tTQlaFZSO4LaGUMdEUzL6CVAcM8kSksGGLY3ILNzQMVflSEWNwHACTlyo/7/l9jAAAAAElFTkSuQmCC',
			'D038' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhDGaY6IIkFTGEMYW10CAhAFmtlbWVoCHQQQRETaXRAqAM7KWrptJVZU1dNzUJyH5o6hBiGeVjswOIWbG4eqPCjIsTiPgA3EM6cpu67twAAAABJRU5ErkJggg==',
			'9948' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoFQGqcnQQQRcLhKsDO2na1KVLMzOzpmYhuY/VlTHQtRHVPIZWhkbX0EAU8wRaWRodGlHtALsFTS82Nw9U+FERYnEfAK7TzUvntW9xAAAAAElFTkSuQmCC',
			'2496' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggKy7ldEVJIbivmlLl67MjEzNQnZfgEgrQ0gginmMDqKhDkC9IshuAZmIJiYCEkNzS2goppsHKvyoCLG4DwApzsqlidR1dwAAAABJRU5ErkJggg==',
			'2E29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEHW3QriwcUgbpo2NWzVyqyoMGT3BQBVtDJMRdbLCNI1BWgXsltAvAAGFDtEgJDRgQHFLaGhoqGsoQEobh6o8KMixOI+APhHymOGNbu/AAAAAElFTkSuQmCC',
			'042C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwE3xLZIOxjCnpTpGEDmILGG4QRUsCUhM4WlCDwdye9/mTsl1vwp7ziRwxFwiqGBcFKHUs0LGaksAzcGCZKPSqzfmMpZd+m2fqJRoUSw3XbxNmzuqEQchvV5Ww6l9M5JHHOX/3vwdz4HfQ2yZVQUgxqAAAAAElFTkSuQmCC',
			'4BAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMExhDHVAFgsRaWUIZXQIQBJjDBFpdHR0dBBBEmOdItLK2hAIUwd20rRpU8OWrooMzUJyXwCqOjAMDRVpdA0NRDGPYQpQrAFDDEMvyM1AMVQ3D1T4UQ9icR8A377MS/Y+jOMAAAAASUVORK5CYII=',
			'CACA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCHVqRxURaGUMYHQKmOiCJBTSytrI2CAQEIIs1iDS6NjA6iCC5L2rVtJWpq1ZmTUNyH5o6qJhoKFAsNATFDpA6QRR1Iq0ijY4OgShirCEijQ6hjihiAxV+VIRY3AcAhQjMkIxD6LMAAAAASUVORK5CYII=',
			'5237' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUNDkMQCGlhbWRsdGkRQxESAIgEoYoEBDI0OYFGE+8KmrVq6auqqlVnI7mtlmAJU2YpicytDANDUKchiAa2MDkAyAFlMZAprA2ujowOyGGuAaKhjKCOK2ECFHxUhFvcBAHRczMjxdmrnAAAAAElFTkSuQmCC',
			'8FF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0IdkMREpog0sDYwOgQgiQW0gsSAchjqgHJI7lsaNTVsaeiqpVlI7kNTh9M83HaguoU1AKwOxc0DFX5UhFjcBwCUmsx1KB4UNgAAAABJRU5ErkJggg==',
			'E1E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHaY6IIkFNDAGsDYwBASgiLECxRgdRFDEGJDVgZ0UGrUqamnoqqlZSO5DU4ckhs08vHZA3cwaiu7mgQo/KkIs7gMAGQLKgzFI+F4AAAAASUVORK5CYII=',
			'CE11' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WENEQxmmMLQii4m0ijQwhDBMRRYLaBRpYAxhCEURawCqQ+gFOylq1dSwVdNWLUV2H5o63GKNmGJgt6CJgdzMGOoQGjAIwo+KEIv7AF9zy617kUKsAAAAAElFTkSuQmCC',
			'3C7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDA6YGIIkFTGFtdGgICBBBVtkq0uDQEOjAgiw2Baii0dEB2X0ro6atWrV0ZRaK+0DqpjA6MKCZxxCAKebowIhiB8gtrkCVyG4Bu7mBAcXNAxV+VIRY3AcAxTTLmHtkBF8AAAAASUVORK5CYII=',
			'968C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaYGIImJTGFtZXR0CBBBEgtoFWlkbQh0YEEVa2B0dHRAdt+0qdPCVoWuzEJ2H6uraCuSOggEmucKNA9ZTAAqhmwHNrdgc/NAhR8VIRb3AQDph8phbbvXpQAAAABJRU5ErkJggg==',
			'37F0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQ11DA1qRxQKmMDS6NjBMdUBW2QoWCwhAFpvC0MrawOggguS+lVGrpi0NXZk1Ddl9UxgCkNRBzWN0wBRjbWBFsyNgighIDMUtogFgMRQ3D1T4URFicR8AJtHLLt0KGBsAAAAASUVORK5CYII=',
			'DECE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCHUMDkMQCpog0MDoEOiCrC2gVaWBtEMQixggTAzspaunUsKWrVoZmIbkPTR0BMTQ7sLgFm5sHKvyoCLG4DwDkdMsfKHsTcAAAAABJRU5ErkJggg==',
			'B36D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMdkMQCpoi0Mjo6OgQgi7UyNLo2ODqIoKhjaGVtYISJgZ0UGrUqbOnUlVnTkNwHVueIphdsXiBhMSxuwebmgQo/KkIs7gMAi9PMhu4J8ukAAAAASUVORK5CYII=',
			'645E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYWllDHUMDkMREpjBMZW1gdEBWF9DCEIoh1sDoyjoVLgZ2UmTU0qVLMzNDs5DcFzJFpJWhIRBVb6toqAOGGNAtaGJAt7QyOjqiiIHczBDKiOLmgQo/KkIs7gMAJ9bJ0iZgtS4AAAAASUVORK5CYII=',
			'C30A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WENYQximMLQii4m0irQyhDJMdUASC2hkaHR0dAgIQBZrYGhlbQh0EEFyX9SqVWFLV0VmTUNyH5o6mFija0NgaAiGHY4o6iBuYUQRg7gZVWygwo+KEIv7ANHGy8CQ9edgAAAAAElFTkSuQmCC',
			'164C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHaYGIImxOrC2MrQ6BIggiYk6iDQyTHV0YEHRC1QR6OiA7L6VWdPCVmZmZiG7j9FBtJW1Ea4OprfRNTQQQ8yhEd0OoFsa0dwSgunmgQo/KkIs7gMAHAXJC4lQptcAAAAASUVORK5CYII=',
			'9E3D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQxmB0AFJTGSKSANro6NDAJJYQKsIkAx0EEEXA6oTQXLftKlTw1ZNXZk1Dcl9rK4o6iAQi3kCWMSwuQWbmwcq/KgIsbgPADA3y1LYOrKJAAAAAElFTkSuQmCC',
			'113B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUMdkMRYHRgDWBsdHQKQxEQdWAMYGgIdRND0MiDUgZ20MmtV1KqpK0OzkNyHpg4hhs08LGIYbglhDUV380CFHxUhFvcBAK9Jxwv2pnJYAAAAAElFTkSuQmCC',
			'A4AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIYGIImxBjBMZQgFyiCJiUwBijg6oogFtDK6sjYEwsTATopaCgSrIkOzkNwX0CrSiqQODENDRUNdQwPRzGPAUIdHDMXNAxV+VIRY3AcA4N/K5G6ia9YAAAAASUVORK5CYII=',
			'D508' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMEx1QBILmCLSwBDKEBCALNYq0sDo6OgggioWwtoQAFMHdlLU0qlLl66KmpqF5L6AVoZGV4Q6JLFAdPMaHdHtmMLaiu6W0ADGEHQ3D1T4URFicR8AhSTOFaJVvf8AAAAASUVORK5CYII=',
			'C7D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WENEQ11DGaY6IImJtDI0ujY6BAQgiQU0AsUaAh1EkMUaGFpZEWJgJ0WtWjVt6aqoqDAk9wHVBbA2BExF1cvoABRrQBFrZG0AiqHYIdIq0sCK5hbWEKAYmpsHKvyoCLG4DwC9t81bUzWHwgAAAABJRU5ErkJggg==',
			'058B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUMdkMRYA0QaGB0dHQKQxESmiDSwNgQ6iCCJBbSKhCCpAzspaunUpatCV4ZmIbkvoJWh0RHNPJCYK5p5QDswxFgDWFvR3cLowBiC7uaBCj8qQizuAwA90cq2uBC0gwAAAABJRU5ErkJggg==',
			'73A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNZQximMLSiiLaKtDKEMkx1QBFjaHR0dAgIQBYD6mNtCHQQQXZf1Kqwpasis6YhuY/RAUUdGLI2MDS6hqKKiYDEGgJQ7AhoEAHqDUBxS0ADawhQDNXNAxR+VIRY3AcAQnjMmyiJcfsAAAAASUVORK5CYII=',
			'068B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSKNCCpAzspaum0sFWhK0OzkNwX0CqKYR5Qb6MrmnkgO9DFsLkFm5sHKvyoCLG4DwDNVMpj5xq2PAAAAABJRU5ErkJggg==',
			'8AE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYW1gCAhAEgtoZW1lbWB0EEBRJ9LoChRDdt/SqGkrU0NXpmYhuQ+qDs080VCQXhEUMYh5Ihh2oLqFNQAohubmgQo/KkIs7gMAta/MJB9dgToAAAAASUVORK5CYII=',
			'FCD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDGaY6IIkFNLA2ujY6BASgiIk0uDYEOoigibE2BMDUgZ0UGjVt1dJVUVOzkNyHpg5JDNM8TDuwuQXTzQMVflSEWNwHAO+czzobNGt6AAAAAElFTkSuQmCC',
			'65C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQxlCHaY6IImJTBFpYHQICAhAEgtoEWlgbRB0EEEWaxAJYW1ggKkDOykyaurSpatWTc1Ccl/IFIZGV4Q6iN5WkBgjqnmtIkAxVDtEprC2oruFNYAxBN3NAxV+VIRY3AcA1NjMvuO2/EcAAAAASUVORK5CYII=',
			'FDA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEETc20IhImBnRQaNW1l6qqoqDAk90HUBUzF0BsKJDHMC0C3o5W1IQDNLaIhQDEUNw9U+FERYnEfAFq/zwxC+4eDAAAAAElFTkSuQmCC',
			'0870' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgDWIH8gKkOSGIiU0QaHRoCAgKQxAJageoaHR1EkNwXtXRl2KqlK7OmIbkPrG4KI0wdVAxoXgCqGMgORwcGFDtAbmFtYEBxC9jNDQwobh6o8KMixOI+ANoEy7oFkO9hAAAAAElFTkSuQmCC',
			'3A22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nM3QsQ2AMAwEwE/BBs4+pqA3UtIwAlMkhTcI7JBMCXSOoAQJf2e9rJPRbpPwp3zi8wJBxMZmJ8UFN7KIbeqgQ5qZ7K5Q5iSJjK8ue13r2hbru3qKzN09H7lAO42ePUFBZ6E88mnszJSnOMfwg/+9mAffATK9zFsV9JCSAAAAAElFTkSuQmCC',
			'B4A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYWhmmMDQEIIkFTGGYyhDK0Igi1soQyujo0IqqjtGVFUgGILkvNGrp0qWroqKikNwXMEWklbUh0AHVPNFQ19DA0BBUO4DqAtDdgiEGcjO62ECFHxUhFvcBAEyAz8Q3JqI+AAAAAElFTkSuQmCC',
			'8558' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoBYkxOoigqgthnQpXB3bS0qipS5dmZk3NQnKfyBSGRoeGADTzQGKBKOYB7Wh0RRMTmcLayujogKKXNYAxhCGUAcXNAxV+VIRY3AcAs8TMp5MDC3UAAAAASUVORK5CYII=',
			'B9DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUNDkMQCprC2sjY6OiCrC2gVaXRtCEQVm4IiBnZSaNTSpamrIkOzkNwXMIUxEENvKwOmea0sWOzAdAvUzShiAxV+VIRY3AcAlq7MXwMcXM4AAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>