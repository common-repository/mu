<?php
/*
Plugin Name: mu
Plugin URI: http://wordpress.org/extend/plugins/mu/
Description: Updates your microblogging accounts when you create a new blog post. Update your settings in <a href="/wp-admin/options-general.php?page=mu/mu.php">Settings -> mu Options</a>
Version: 0.3b
Author: Mark Bockenstedt
Author URI: http://mark.bockenstedt.net/
*/
require_once(ABSPATH."wp-includes/wp-db.php");

if(!class_exists('mudb'))
{
class mudb
{
	function __construct()
	{
		global $wpdb;
		$this->table = $wpdb->prefix."mu";
	}

	function get_post_info($post_id)
	{
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM `$this->table` WHERE `post_id`='$post_id' LIMIT 1");
	}

	function set_post_info($post_id)
	{
		if($_POST['publish'] == "Publish" or $_POST['save'] == "Save")
		{
			global $wpdb;
			$text = $wpdb->escape($_POST['mu-post-text']);
			$twitter = $_POST['mu-post-twitter'] ? 1 : 0;
			$plurk = $_POST['mu-post-plurk'] ? 1 : 0;
			$identica = $_POST['identica'] ? 1 : 0;
			$friendfeed = $_POST['friendfeed'] ? 1 : 0;
			$jaiku = $_POST['jaiku'] ? 1 : 0;

			$present = $wpdb->get_row("SELECT `id` FROM `$this->table` WHERE `post_id`='$post_id'");
			if($present->id)
			{
				$sql = "UPDATE `$this->table` SET `post_text`='$text', `twitter`='$twitter', `plurk`='$plurk', `identica`='$identica', ";
				$sql .= "`friendfeed`='$friendfeed', `jaiku`='$jaiku' WHERE `id`='$present->id'";
			}
			else
			{
				$sql = "INSERT INTO `$this->table` ";
				$sql .= "(`post_id`, `post_text`, `twitter`, `plurk`, `identica`, `friendfeed`, `jaiku`) VALUES ";
				$sql .= "('$post_id', '$text', '$twitter', '$plurk', '$identica', '$friendfeed', '$jaiku')";
			}

			$wpdb->query($sql);
		}
	}
}
}

if(!class_exists('mu'))
{
class mu
{
	var $services = array('twitter', 'plurk', 'friendfeed', 'jaiku', 'identica');

	function check_service($service, $type)
	{
		$disabled = $checked = null;
		if(get_option("mu-$service-user") == "" and get_option("mu-$service-pwd") == "")
			$disabled = "disabled";

		if(get_option("mu-$type-$service") != '0' and !$disabled)
			$checked = "checked";

		return array($disabled, $checked);
	}

	function load_service($service, $post_info)
	{
		$disabled = $checked = null;
		if(get_option("mu-$service-user") == "" and get_option("mu-$service-pwd") == "")
			$disabled = "disabled";

		if($post_info->$service != 0 and !$disabled)
			$checked = "checked";

		return array($disabled, $checked);
	}

	function add_meta_tags()
	{
		$post_id = $_GET['post'];
		if($post_id)
		{
			$post = get_post($post_id);
			$mudb = new mudb();

			// These actions are not available to published posts
			if($post->post_status == "publish")
				return;

			// This post was already saved - grab the last values stored in mudb
			if($post->post_status == "draft")
				$mu_post = $mudb->get_post_info($post_id);
		}
		
		// I've got data on this post - let's load that
		if($mu_post)
		{
			list($twitter, $twitter_block) = $this->load_service("twitter", $mu_post);
			list($plurk, $plurk_block) = $this->load_service("plurk", $mu_post);
			list($friendfeed, $friendfeed_block) = $this->load_service("friendfeed", $mu_post);
			list($identica, $identica_block) = $this->load_service("identica", $mu_post);
		}
		// No data - reverting to defaults
		else
		{
			$action = ($_GET['action'] == 'edit') ? "ep" : "np";
			list($twitter, $twitter_block) = $this->check_service("twitter", $action);
			list($plurk, $plurk_block) = $this->check_service("plurk", $action);
			list($friendfeed, $friendfeed_block) = $this->check_service("friendfeed", $action);
			list($identica, $identica_block) = $this->check_service("identica", $action);
		}
	?>
	<div id="postmc" class="postbox open">
		<script>
		function update_counter()
		{
			// get text
			var t = document.getElementById("mu_label").value;

			// if #title# is in text, replace it to get a more accurate count
			t = t.replace("#title#", document.getElementById("title").value);

			// replace counter
			document.getElementById("length_label").innerHTML = t.length;
		}
		</script>
		<h3>mu Services</h3>
		<div class="inside">
			<div id="postmc">
				<div style="float:right"><a href="http://mark.bockenstedt.net/" target="_blank">Help!</a></div>
				<b>Send to...</b>
				<table width="440" style="margin-bottom:20px;">
				<tr>
					<td width="20"><input type="checkbox" name="mu-post-twitter" value="1"<?=$twitter;?> <?=$twitter_block;?> /></td>
					<td width="200">Twitter</td>

					<td width="20"><input type="checkbox" name="mu-post-friendfeed" value="1"<?=$friendfeed;?> <?=$friendfeed_block;?> /></td>
					<td width="200">FriendFeed</td>
				</tr>
				<tr>
					<td><input type="checkbox" name="mu-post-plurk" value="1"<?=$plurk;?> <?=$plurk_block;?> /></td>
					<td>Plurk</td>

					<td><input type="checkbox" name="mu-post-identica" value="1"<?=$identica;?> <?=$identica_block;?> /></td>
					<td>Identi.ca</td>
				</tr>
				</table>
				<b>With label...</b><br/>
			<?	$service = ($_GET['action'] == "edit") ? "mu-ep-text" : "mu-np-text";
				$value = ($mu_post) ? $mu_post->post_text : get_option($service);
				?>
				<textarea name="mu-post-text" id="mu_label" cols="50" rows="2" onkeyup="update_counter();"><?=$value;?></textarea>
				<br/>
				&nbsp;(Your text is <span id="length_label"></span> characters long)
				<br/>
				Your link will be shortened by
			<?	$ss = get_option('mu-url-shortener');
				if($ss == 'custom')
					$ss = get_option('mu-url-custom-name');
				?>
				<b><?=$ss;?></b>
			<script>update_counter();</script>
			</div>
		</div>
	</div>
<?	}

	function get_short_url($url)
	{
		if(get_option('mu-url-shortener') == "")
			return $url;

		switch(get_option('mu-url-shortener'))
		{
			case 'bit.ly':
				$api_url = "http://bit.ly/api?url=$url";
				break;
			
			case 'tinyurl':
				$api_url = "http://tinyurl.com/api-create.php?url=$url";
				break;

			case 'is.gd':
				$api_url = "http://is.gd/api.php?longurl=$url";
				break;

			case 'snurl':
				$api_url = "http://snipr.com/site/snip?r=simple&link=$url";
				break;

			case 'ri.ms':
				$api_url = "http://ri.ms/api-create.php?url=$url";
				break;

			case 'custom':
				$api_url = str_replace("#link#", $url, get_option('mu-url-custom-url'));
				break;
		}
		
		$fh = fopen($api_url, 'r');
		if(!$fh)
			return $url;
		$short_url = fread($fh, 1024);
		fclose($fh);

		if(eregi("^http://", $short_url))
			return $short_url;
		else
			return $url;
	}

	function tweet($message)
	{
		// Get username and password
		$username = get_option('mu-twitter-user');
		$password = base64_decode(get_option('mu-twitter-pwd'));

		// Initiate curl handle
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/update.xml");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// send update and credentials
		curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$message&source=mu");
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_exec($ch);
	
		// Get response code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close
		curl_close($ch);

		return ($reponse_code == 200);
	}

	function plurk($message)
	{
		// Get username and password
		$username = get_option('mu-plurk-user');
		$password = base64_decode(get_option('mu-plurk-pwd'));

		$paths = array(
			'user'	=> "http://www.plurk.com/user/$username",
			'login'	=> "http://www.plurk.com/Users/login",
			'add'	=> "http://www.plurk.com/TimeLine/addPlurk"
		);

		// Initiate curl handle
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		
		// Grab UID from nickname so we can actually post
		curl_setopt($ch, CURLOPT_URL, $paths['user']);
		$response = curl_exec($ch);
		preg_match('/var GLOBAL = \{.*"uid": ([\d]+),.*\}/imU', $response, $matches);
		$uid = $matches[1];
		
		// Log in to plurk
		curl_setopt($ch, CURLOPT_URL, $paths['login']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "nick_name=$username&password=$password");
		curl_exec($ch);
		
		// Send update
		curl_setopt($ch, CURLOPT_URL, $paths['add']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "qualifier=shares&content=".urlencode($message)."&lang=en&no_comments=0&uid=$uid");
		curl_exec($ch);

		// Get reponse code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		// Close
		curl_close($ch);

		return ($response_code == 200);
	}

	function identica($message)
	{
		// Get username and password
		$username = get_option('mu-identica-user');
		$password = base64_decode(get_option('mu-identica-pwd'));

		// Initiate curl handle
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

		// send login
		curl_setopt($ch, CURLOPT_URL, 'http://identi.ca/main/login');
		curl_setopt($ch, CURLOPT_POSTFIELDS, "nickname=$username&password=$password");
		curl_exec($ch);
		
		// send update
		curl_setopt($ch, CURLOPT_URL, "http://identi.ca/notice/new");
		curl_setopt($ch, CURLOPT_POSTFIELDS, "qualifier=%3A&status_textarea=$message&lang=en&no_comments=0");
		curl_exec($ch);

		// Get reponse code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close
		curl_close($ch);

		return ($response_code == 200);
	}

	function friendfeed($message, $link=null)
	{
		$username = get_option('mu-friendfeed-user');
		$password = base64_decode(get_option('mu-friendfeed-pwd'));

		// set post data
		$post = array('title' => $message);
		if($link)
			$post['link'] = $link;

		// Initiate curl handle
		$ch = curl_init("friendfeed.com");
		curl_setopt($ch, CURLOPT_URL, "http://friendfeed.com/api/share");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// Send update
		curl_exec($ch);

		// Get response code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close
		curl_close($ch);

		return ($response_code == 200);
	}

	function jaiku($message)
	{
		// set post data and encode that
		$post = array();
		$post['user'] = get_option('mu-jaiku-user');
		$post['personal_key'] = base64_decode(get_option('mu-jaiku-pwd'));
		$post['message '] = $message;
		$entry = xmlrpc_encode_request("presence.send", $post);

		// Initiate curl handle
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.jaiku.com/xmlrpc");
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $entry);

		// Send update
		curl_exec($ch);

		// Get response code
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close
		curl_close($ch);

		return ($response_code == 200);
	}

	function get_text($post_id, $update, $post_link=null, $format=null)
	{
		$update = $_POST['mu-post-text'] ? $_POST['mu-post-text'] : $update;

		$post_info = get_post($post_id);
		$title = $post_info->post_title;

		if($format == "plurk")
		{
			if($post_link)
			{
				if(ereg("#title#", $update))
					$update = str_replace("#title#", "$post_link ($title)", $update);
				else
					$update = "$post_link ($update)";
			}
		}
		elseif($format == "friendfeed")
		{
			// do nothing
		}
		else
		{
			if($post_link)
				$update .= " ( $post_link )";
		}

		$update = str_replace("#title#", $title, $update);

		// Strip slashes from the message
		$update = stripslashes($update);

		return $update;
	}

	function publish($post_id)
	{
		// If $_POST, then the post was just created. otherwise, it's a scheduled post
		if($_POST)
			$this->publish_now($post_id);
		else
			$this->publish_later($post_id);
	}

	function publish_now($post_id)
	{	
		if($_POST['prev_status'] == "draft" and $_POST['publish'] == "Publish")
		{
			// Make sure they actually posted
			if($_POST['mu-post-twitter'] or $_POST['mu-post-plurk'] or $_POST['mu-post-friendfeed'] or 
				$_POST['mu-post-jaiku'] or $_POST['mu-post-identica'])
			{
				$link = null;
				if(get_option('mu-np-link') == '1')
					$link = $this->get_short_url(get_permalink($post_id));
		
				if($_POST['mu-post-twitter'] == '1')
					$this->tweet($this->get_text($post_id, null, $link));
				if($_POST['mu-post-plurk'] == '1')
					//$this->plurk($this->get_text($post_id, null, $link, "plurk"));
					$this->plurk($this->get_text($post_id, null, $link));
				if($_POST['mu-post-friendfeed'] == '1')
					$this->friendfeed($this->get_text($post_id, null, "friendfeed"), $link);
				//if($_POST['mu-post-jaiku'] == '1')
				//	$this->jaiku($this->get_text($post_id, null, $link));
				if($_POST['mu-post-identica'] == '1')
					$this->identica($this->get_text($post_id, null, $link));
			}
		}
	}

	function publish_later($post_id)
	{
		$mudb = new mudb();
		$post_info = $mudb->get_post_info($post_id);
		$text = $post_info->post_text;

		if($post_info->twitter or $post_info->plurk or $post_info->friendfeed or 
			$post_info->jaiku or $post_info->identica)
		{
			$link = null;
			if(get_option('mu-np-link') == '1')
				$link = $this->get_short_url(get_permalink($post_id));
	
			if($post_info->twitter == '1')
				$this->tweet($this->get_text($post_id, $text, $link));
			if($post_info->plurk == '1')
				$this->plurk($this->get_text($post_id, $text, $link, "plurk"));
			if($post_info->friendfeed == '1')
				$this->friendfeed($this->get_text($post_id, $text, null, "friendfeed"), $link);
			//if($post_info->jaiku == '1')
			//	$this->jaiku($this->get_text($post_id, $text, $link));
			if($post_info->identica == '1')
				$this->identica($this->get_text($post_id, $text, $link));
		}
	}

	function add_admin_pages()
	{
		add_submenu_page("options-general.php", 'mu Options', 'mu Options', 10, __FILE__, array($this, 'settings_page'));
	}

	function settings_page() { include(dirname(__FILE__).'/settings.php'); }
}
}

// Action Hooks
if(function_exists('add_action'))
{
	$mu = new mu();
	$mudb = new mudb();

	add_action('edit_form_advanced', array($mu, 'add_meta_tags'));
	add_action('admin_menu', array($mu, 'add_admin_pages'));
	add_action('private_to_published', array($mu, 'publish'));
	add_action('save_post', array($mudb, 'set_post_info'));
}
?>
