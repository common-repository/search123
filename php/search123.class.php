<?php
/**
 * @package search123
 * @author Stefanie Brandstätter & Tim Zylinski
 * @version 2.1
 */

class search123 {
	/**
	 * constructor
	 *
	 * @package search123
	 */
	public function __construct() {
		add_action('init', array(&$this, 'on_init'), 1 );
	}

	/**
	 * init - setup the plugin
	 *
	 * @package search123
	 */
	public function on_init() {
		global $wp_version;
		
		if ( is_admin() ) {

			//############################## defines ###############################
			define("COLOR_BORDER", 0);
			define("COLOR_BG", 1);
			define("COLOR_TITLE", 2);
			define("COLOR_DESC", 3);
			define("COLOR_LINK", 4);
			define("COUNT_COLOR", 5);
			//############################## END defines ###############################

			/**
			 * add action for admin menu
			 */
			add_action('admin_menu', array(&$this, 'search123_add_menu'), 1);
		}

		/**
		 * load translations
		 */
		add_action( 'init', array(&$this, 'textdomain') );
			
		/**
		 * Hook wp_head to add css
		 */
		add_action('wp_head', array(&$this, 'search123_wp_head'));

		/**
		 * Add edit link to plugin info
		 */
		add_filter( 'plugin_action_links', array(&$this, 'filter_plugin_meta'), 10, 2 );
			
		/**
		 * Add search123 widget
		 */
		add_action('widgets_init', create_function('', 'return register_widget("Search123Widget");'));
	}

	/**
	 * language support
	 *
	 * @package search123
	 */
	public function textdomain() {
		if ( function_exists('load_plugin_textdomain') ) {
			if ( !defined('WP_PLUGIN_DIR') ) {
				load_plugin_textdomain(S123_TEXTDOMAIN, str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');
			} else {
				load_plugin_textdomain(S123_TEXTDOMAIN, false, str_replace('/php', '', dirname( plugin_basename(__FILE__) ) ) . '/languages');
			}
		}
	}

	/**
	 * register admin Menu
	 *
	 * @package search123
	 */
	public function search123_add_menu()
	{
		$menutext = '<img src="' . $this->get_resource_url('search123.gif') . '" alt="" />' . ' ';
		add_options_page('Search123', $menutext.'Search123', 9, __FILE__, array(&$this, 'search123AdminPage'));
	}

	/**
	 * register search123 css in header
	 *
	 * @package search123
	 */
	public function search123_wp_head()
	{
		if ( !is_admin() ) {
			if (get_option('s123_usecss')) {
				echo '<link rel="stylesheet" type="text/css" media="screen" href="' . S123_PLUGIN_URL .'/'. 'css/s123.css" />';
			}
			else
			{
				echo '<style type="text/css" media="screen">';
				echo " .s123 {background-color:#".get_option("bgColor")."; padding: 10px 10px 2px 10px;font-family:".get_option("font").";border: 1px solid #".get_option("borderColor").";}";
				echo " .s123 .s123entry {padding-bottom: 10px;}";
				echo " .s123 .s123title {font-size:".get_option("titleFontSize")."px;color:#".get_option("titleColor").";font-weight:bold;text-decoration:none;}";
				echo " .s123 .s123text {font-size:".get_option("textFontSize")."px;color:#".get_option("textColor").";}";
				echo " .s123 .s123link {font-size:".get_option("urlFontSize")."px;color:#".get_option("urlColor").";text-decoration:underline;}";
				echo '</style>';
			}
		}
	}

	/**
	 * @version WP 2.8
	 * Add action link(s) to plugins page
	 *
	 * @package search123
	 *
	 * @param $links, $file
	 * @return $links
	 */
	public function filter_plugin_meta($links, $file) {
		if ( $file == S123_PLUGIN_BASENAME ) {
			$links[] = sprintf( '<a href="options-general.php?page=%s">%s</a>', plugin_basename(__FILE__), __('Settings') );
		}
		return $links;
	}

	/**
	 * search123 Admin Page
	 *
	 * @package search123
	 */
	public function search123AdminPage()
	{
		//initialize variables
		$errorPublisherID = false;
		for( $i = 0; $i < COUNT_COLOR; $i++ )
		$errorColors[$i] = false;
		$errorColor = false;
		$defaultColor = "000000";
		$defaultBgColor = "ffffff";
		$fontStyles = array( "Arial","Tahoma","Times New Roman","Verdana","Georgia" );
		$adColors = array ( 'border' => array ( 0  => 'borderColor',
		1 => $_POST["borderColor"] ),
						  'background'  => array ( 0  => 'bgColor',
		1 => $_POST["bgColor"] ),
						  'title'       => array ( 0  => 'titleColor',
		1 => $_POST["titleColor"] ),
						  'description' => array ( 0  => 'descColor',
		1 => $_POST["descColor"] ),
						  'link'        => array ( 0  => 'urlColor',
		1 => $_POST["urlColor"] ) );

		if( isset($_POST["send"]) && check_admin_referer('search123_form') && current_user_can('manage_options') )
		{
			$pattern = '~[0-9a-fA-F]{6}~';
			$patternNote = '~[c-fC-F](.*)~';
			$search123aid = $_POST["search123aid"];
			$outputColor = array();
			$font = $_POST["font"];
			$titleFontSize = $_POST["titleFontSize"];
			$textFontSize = $_POST["textFontSize"];
			$urlFontSize = $_POST["urlFontSize"];
			$alignment = $_POST["alignment"];
			$noOfAds = $_POST["noOfAds"];
			$s123_usecss = $_POST["usecss"];
			$s123_usekeywords = $_POST["usekeywords"];
			$s123_usetags = $_POST["usetags"];

			//set color of the word "Anzeige"
			if( preg_match_all( $patternNote, $adColors['background'][1], $found ) )
		 $noteColor = "000000";
		 else
		 $noteColor = "ffffff";

		 //store publisher ID
		 if( $_POST["search123aid"] != "" && ereg ("^[0-9]{4}$", $_POST["search123aid"]))
		 update_option( "search123aid", mysql_real_escape_string($_POST["search123aid"]) );
		 else
		 $errorPublisherID = true;

		 //store alignment, number of ads, font, tile / text / url font size
		 if( !$errorPublisherID )
		 {
		  //check if color values are hexadezimal
		  //YES: store them in DB; if no value is entered use default value
		  //NO: error message
		  $counter = 0;
		  foreach( $adColors as $items=>$item )
		  {
		  	if( !preg_match_all( $pattern, $item[1], $found ) )
		  	{
		  		if( $item[1] == "" )
		  		{
		  			if( $counter == COLOR_BG )
		  			update_option( $item[0], mysql_real_escape_string( $defaultBgColor ) );
		  			else
		  			update_option( $item[0], mysql_real_escape_string( $defaultColor ) );
		  		}
		  		else
		  		{
		  			$errorColors[$counter] = true;
		  			$errorColor = true;
		  		}
		  	}
		  	else
			  update_option( $item[0],mysql_real_escape_string( $item[1] ) );
			  	
			  $counter++;
		  }

		  update_option( "alignment", mysql_real_escape_string( $alignment ) );
		  update_option( "numberOfAds", mysql_real_escape_string( $noOfAds ) );
		  update_option( "font", mysql_real_escape_string( $font ) );
		  update_option( "titleFontSize", mysql_real_escape_string( $titleFontSize ) );
		  update_option( "textFontSize", mysql_real_escape_string( $textFontSize ) );
		  update_option( "urlFontSize", mysql_real_escape_string( $urlFontSize ) );
		  update_option( "noteColor", mysql_real_escape_string( $noteColor ) );
		  update_option( "s123_usecss",mysql_real_escape_string( $s123_usecss ) );
		  update_option( "s123_usekeywords",mysql_real_escape_string( $s123_usekeywords ) );
		  update_option( "s123_usetags",mysql_real_escape_string( $s123_usetags ) );
		 }
		}
		 
		//get values from DB for form fields
		$counter = 0;
		foreach( $adColors as $items=>$item )
		{
			if( $errorColors[$counter] )
			$outputColor[$counter] = $item[1];
			else if( get_option( $item[0] ) != "" )
			$outputColor[$counter] = get_option( $item[0] );
			else
			{
		  if( $counter == COLOR_BG )
		  $outputColor[$counter] = $defaultBgColor;
		  else
		  $outputColor[$counter] = $defaultColor;
			}
			$counter++;
		}
		 
		 
		//set ouput from color values
		if( get_option("alignment") != "" )
		$outputAlignment = get_option("alignment");
		else
		$outputAlignment = "vertical";

		$outputNoOfAds = get_option("numberOfAds");
		$outputFont = get_option("font");
		$outputTitleFontSize = get_option("titleFontSize");
		$outputTextFontSize = get_option("textFontSize");
		$outputUrlFontSize = get_option("urlFontSize");
		$s123_usecss = get_option("s123_usecss");
		$s123_usekeywords = get_option("s123_usekeywords");
		$s123_usetags = get_option("s123_usetags");

		?>

		<?php if( $errorColor )  echo '<div id="message" class="error"><p>'.__('Color values must be hexadecimal.', S123_TEXTDOMAIN).'</p></div>'; ?>
		<?php if( !$errorColor && isset($_POST['send']) ) {
			echo '<div id="message" class="updated fade"><p>'.__('The settings were saved.', S123_TEXTDOMAIN).'</p></div>';
		} ?>

<div class="wrap">
<h2><?php _e('S123 Settings', S123_TEXTDOMAIN); ?></h2>
<br class="clear" />

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"><?php if (function_exists('wp_nonce_field') === true) wp_nonce_field('search123_form'); ?>
<div id="poststuff" class="ui-sortable meta-box-sortables">
<div id="search123_settings_common" class="postbox">

<h3><?php _e('general settings', S123_TEXTDOMAIN); ?></h3>
<div class="inside">


<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="search123aid"><?php _e('Search123 publisher ID', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="text" name="search123aid" size="7" maxlength="7"
			value="<?php echo get_option("search123aid") ?>" /> <b>*</b> <?php if( $errorPublisherID ) echo '<br /><p style="color:#ff0000">'.__('Please enter your Search123 Publisher ID!', S123_TEXTDOMAIN).'</p>'; ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="usekeywords"><?php _e('keyword', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="checkbox" name="usekeywords"
		<?php echo (get_option('s123_usekeywords')) ? ' checked="checked"' : ''; ?> />
		<?php _e('use keyword input', S123_TEXTDOMAIN); ?><span class="description"> <?php _e('(if there are no keywords specified, the default keyword selection will be used)', S123_TEXTDOMAIN); ?></span></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="usetags"><?php _e('tags', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="checkbox" name="usetags"
		<?php echo (get_option('s123_usetags')) ? ' checked="checked"' : ''; ?> />
		<?php _e('use tags', S123_TEXTDOMAIN); ?><span class="description"> <?php _e('(if there are no tags, the specified keywords or default keyword selection will be used)', S123_TEXTDOMAIN); ?></span></td>
	</tr>	

	<tr valign="top">
		<th scope="row"><label for="usecss"><?php _e('CSS', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="checkbox" name="usecss"
		<?php echo (get_option('s123_usecss')) ? ' checked="checked"' : ''; ?> />
		<?php _e('use CSS file', S123_TEXTDOMAIN); ?></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="alignment"><?php _e('display', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="radio" name="alignment" value="vertical"
		<?php   if( $outputAlignment == "vertical") echo 'checked="checked"'; ?> />
		<?php _e('vertical', S123_TEXTDOMAIN); ?> <br />
		<input type="radio" name="alignment" value="horizontal"
		<?php   if( $outputAlignment == "horizontal") echo 'checked="checked"';?> />
		<?php _e('horizontal', S123_TEXTDOMAIN); ?></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="noOfAds"><?php _e('number of ads', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><select name="noOfAds">
		<?php  for( $i = 2; $i <= 8; $i++ ) { ?>
			<option <?php if( $outputNoOfAds == $i ) echo 'selected'; ?>><?php echo $i ?></option>
			<?php } ?>
		</select></td>
	</tr>
</table>
</div>
</div>
</div>

<div id="poststuff" class="ui-sortable meta-box-sortables">
<div id="search123_settings_layout" class="postbox">

<h3><?php _e('layout', S123_TEXTDOMAIN); ?></h3>
<div class="inside">
<h4><?php _e('common layout', S123_TEXTDOMAIN); ?></h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="font"><?php _e('font', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><select name="font">
		<?php for( $i = 0; $i < count( $fontStyles ); $i++ ) { ?>
			<option
			<?php if( $fontStyles[$i] == $outputFont ) echo 'selected'; ?>><?php echo $fontStyles[$i] ?></option>
			<?php  } ?>
		</select></td>
	</tr>

	<tr valign="top">
		<th scope="row"
		<?php if( $errorColors[COLOR_BORDER] ) echo 'style="color:#ff0000"'; ?>>
		<label for="borderColor"><?php _e('border color', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><input type="text" name="borderColor" size="15" maxlength="6"
			value="<?php echo $outputColor[COLOR_BORDER]?>" id="borderColor" /> <?php _e('(default: 000000)', S123_TEXTDOMAIN); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"
		<?php if( $errorColors[COLOR_BG] ) echo 'style="color:#ff0000"'; ?>>
		<label for="bgColor"><?php _e('background color', S123_TEXTDOMAIN); ?></label>
		</th>
		<td># <input type="text" name="bgColor" size="15" maxlength="6"
			value="<?php echo $outputColor[COLOR_BG] ?>" /> <?php _e('(default: ffffff)', S123_TEXTDOMAIN); ?>
		</td>
	</tr>
</table>

<h4><?php _e('title', S123_TEXTDOMAIN); ?></h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row"
		<?php if( $errorColors[COLOR_TITLE] ) echo 'style="color:#ff0000"'; ?>>
		<label for="titleColor"><?php _e('color', S123_TEXTDOMAIN); ?></label>
		</th>
		<td># <input type="text" name="titleColor" size="15" maxlength="6"
			value="<?php echo $outputColor[COLOR_TITLE] ?>" /> <?php _e('(default: 000000)', S123_TEXTDOMAIN); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="titleFontSize"><?php _e('font size', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><select name="titleFontSize">
		<?php for( $i = 10; $i <= 15; $i++ ) { ?>
			<option <?php if( $outputTitleFontSize == $i ) echo 'selected'; ?>><?php echo $i ?></option>
			<?php } ?>
		</select> px</td>
	</tr>
</table>

<h4><?php _e('description', S123_TEXTDOMAIN); ?></h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row"
		<?php if( $errorColors[COLOR_DESC] ) echo 'style="color:#ff0000"'; ?>>
		<label for="descColor"><?php _e('color', S123_TEXTDOMAIN); ?></label>
		</th>
		<td># <input type="text" name="descColor" size="15" maxlength="6"
			value="<?php echo $outputColor[COLOR_DESC] ?>" /> <?php _e('(default: 000000)', S123_TEXTDOMAIN); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="textFontSize"><?php _e('font size', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><select name="textFontSize">
		<?php for( $i = 9; $i <= 14; $i++ ) { ?>
			<option <?php if( $outputTextFontSize == $i ) echo 'selected'; ?>><?php echo $i ?></option>
			<?php } ?>
		</select> px</td>
	</tr>
</table>

<h4><?php _e('URL Link', S123_TEXTDOMAIN); ?></h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row"
		<?php if( $errorColors[COLOR_LINK] ) echo 'style="color:#ff0000"'; ?>>
		<label for="urlColor"><?php _e('color', S123_TEXTDOMAIN); ?></label></th>
		<td># <input type="text" name="urlColor" size="15" maxlength="6"
			value="<?php echo $outputColor[COLOR_LINK] ?>" /> <?php _e('(default: 000000)', S123_TEXTDOMAIN); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="urlFontSize"><?php _e('font size', S123_TEXTDOMAIN); ?></label>
		</th>
		<td><select name="urlFontSize">
		<?php for( $i = 8; $i <= 13; $i++ ) { ?>
			<option <?php if( $outputUrlFontSize == $i ) echo 'selected'; ?>><?php echo $i ?></option>
			<?php } ?>
		</select> px</td>
	</tr>
</table>
</div>
</div>
</div>

<p class="submit"><input type="submit" name="send"
	class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
</form>

</div>

<?php
	}
	
	/**
	 * display encoded icons
	 *
	 * @package search123
	 *
	 * @param $resourceID
	 * @return $resourceID
	 */
	public function get_resource_url($resourceID) {

		return trailingslashit( get_bloginfo('url') ) . '?resource=' . $resourceID;
	}	


	/**
	 * search123 search function
	 *
	 * @package search123
	 */
	public function getAds($size=0, $keyword="", $align="")
	{
		$search123aid = get_option("search123aid");

		//set size = number of Ads
		if( $size >= 2 && $size <= 8 )
		$noOfAds = $size;
		else
		$noOfAds = get_option("numberOfAds");

		//set alignment: vertical or horizontal
		if( $align != "" )
		$alignment = $align;
		else
		$alignment = get_option("alignment");

		if( $search123aid != "" )
		{
			// determine search keyword
			$search = $this->getKeyword($keyword);

			// load the official S123 API class
			require_once("search123api.class.php");

			// Array for S123 ads (raw)
			$s123ads = array();

			// Search123 parameter
			$search123settings = array(aid => $search123aid, size => $noOfAds);

			// execute query
			$s123 = new s123($search123settings);
			$s123->search($search);

			foreach ( $s123->listings as $listing )
			{
			  $title=html_entity_decode($listing["title"]);
			  $title=str_replace("&#8364;","xxeuroxx",$title);
			  $title=htmlentities($title, ENT_QUOTES, "UTF-8");
			  $title=str_replace("xxeuroxx","&euro;",$title);

			  $description=$listing["description"];
			  if(strlen($description)<80)
			  $offset=strlen($description);
			  else
			  $offset=80;
			  $pos=strpos($description," ",$offset);
			  if($pos==0)
			  $pos=strlen($description);
				
			  $description=substr($description, 0, $pos);
			  $description=html_entity_decode($description);
			  $description=str_replace("&#8364;","xxeuroxx",$description);
			  $description=htmlentities($description, ENT_QUOTES, "UTF-8");
			  $description=str_replace("xxeuroxx","&euro;",$description);

			  $url=$listing["redirect_url"];
			  $site=$listing["site_url"];

			  $s123ads[] = array("title" => $title, "text" => $description, "url" => $url, "site" => $site);

			}
				
			// Are there ads ?
			if(count($s123ads) > 1) {
				$banner = $this->getBanner($s123ads, $alignment);
			} else {
				$banner = "";
			}

			print $banner;
		}
	}

	/**
	 * format search123 ads to printable banner
	 *
	 * @package search123
	 */
	public function getBanner($s123ads, $alignment) {
		// prepare output
		$banner = "<div class=\"s123\">\n";
		if( $alignment == "horizontal" )
		{
			$banner .= "<table><tr>";
			foreach ( $s123ads as $ad )
			{
				$url = $this->getTrackedURL($ad["url"]);
				$banner .= '<td width="'.round(100/get_option('numberOfAds'),0).'%" valign="top"><div class="s123entry">';
				$banner .= '<a href="'.$url.'/" target="_blank" class="s123title">'.$ad["title"].'</a>';
				$banner .= '<div class="s123text">'.$ad["text"].'</div>';
				$banner .= '<a href="'.$url.'/" target="_blank" class="s123link">'.$ad["site"].'</a>';
				$banner .= '</div></td>';
			}
			$banner .= "</tr></table>";
		}
		else
		{
			foreach ( $s123ads as $ad )
			{
				$url = $this->getTrackedURL($ad["url"]);
				$banner .= '<div class="s123entry">';
				$banner .= '<a href="'.$url.'/" target="_blank" class="s123title">'.$ad["title"].'</a>';
				$banner .= '<div class="s123text">'.$ad["text"].'</div>';
				$banner .= '<a href="'.$url.'/" target="_blank" class="s123link">'.$ad["site"].'</a>';
				$banner .= '</div>';
			}
		}

		// show advert label
		$banner .= "<div style=\"text-align:right;align:right;font-size:10px;color:#000000\">".__('advert', S123_TEXTDOMAIN)."</div>";
		$banner .= "</div>\n";

		return $banner;
	}

	/**
	 * find right keyword to use
	 *
	 * @package search123
	 */
	public function getKeyword($keyword="") {
		global $wp_query;
		$search = "";

		$s123_usekeywords = get_option("s123_usekeywords");
		$s123_usetags = get_option("s123_usetags");
		$postid = $wp_query->post->ID;

		// no keyword as parameter, try to find one
		if( $keyword == "" ) {
			// is Search Page
			if( is_search() )
				$search = $_GET["s"];
			else {
				// Use tags if there are any
				if( $s123_usetags ) 
					$search = $this->getKeywordFromTags($postid);
					
				// Use keywords from special fields if there are no tags
				if( empty($search) && $s123_usekeywords )
					$search = $this->getKeywordFromSpecialFields($postid);
				
			}
		} else {
			// keyword is in parameter, so use it
			$search = $keyword;
		}

		// no keyword found yet? use category or title
		if(empty($search)) 
			$search = $this->getKeywordFromDefaults();

		return $search;
	}
	
		/**
	 * Extracts a keywords from artice tags
	 *
	 * If tags are defined for an article, one random tags is selected a the keyword
	 *
	 * @package search123
	 */
	private function getKeywordFromDefaults() {
		$kw = "";
		
		// category
		if( is_category() )
			$kw = single_cat_title("", false);
		// title
		else
			$kw = get_the_title();
		
		return $kw;
	}
	
	/**
	 * Extracts a keywords from artice tags
	 *
	 * If tags are defined for an article, one random tags is selected a the keyword
	 *
	 * @package search123
	 */
	private function getKeywordFromTags($postid) {	
		$kw = "";
		
		$tmptags = get_the_tags($postid);
		$tags = array();

		if(!empty($tmptags)) {
			foreach($tmptags as $slug) {
				$tags[] = $slug->slug;
			}
			
			$kw = $tags[rand(0,count($tags)-1)];	
		}
		
		return $kw;	
	}
	
	/**
	 * Extracts keywords defined in special fields
	 *
	 * Special fields are "s123keywords" (for this plugin) or "_aioseop_keywords" (All-In-One-SEO)
	 *
	 * @package search123
	 */
	private function getKeywordFromSpecialFields($postid) {
		$kw = "";
		
		// special field s123keywords
		if(empty($kw))
		$kw = get_post_meta( $postid, 's123keywords', true );
		
		// no keywords? -> AIOSEO keywords there?
		if(empty($kw))
		$kw = get_post_meta( $postid, '_aioseop_keywords', true );	
		
		return $kw;
	}
	
	/**
	 * Add custom tracking to URL
	 *
	 * You can add your custom tracking parameters or prefix the URL with your own tracking URL
	 *
	 * @package search123
	 */
	private function getTrackedURL($url) {
		return $url;
	}
}

?>