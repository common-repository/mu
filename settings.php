<style type="text/css">
fieldset { margin: 10px 0; border:1px solid #CECECE; padding:10px; }
a.mu { text-decoration:none; }
div.mu_error { border: 1px solid red; padding: 20px; margin: 20px; color: red; }
</style>

<?
$mu = new mu();

if(get_option('mu-init') != '1')
{
	$defaults = array(
		'mu-twitter-user' => '', 'mu-twitter-pwd' => '', 'mu-twitter-encrypted' => '',
		'mu-plurk-user' => '', 'mu-plurk-pwd' => '',
		'mu-friendfeed-user' => '', 'mu-friendfeed-pwd' => '',
		'mu-jaiku-user' => '', 'mu-jaiku-pwd' => '',
		'mu-identica-user' => '', 'mu-identica-pwd' => '',

		'mu-np-text' => 'Published a new post: #title#', 'mu-np-link' => 1,
		'mu-np-twitter' => 1, 'mu-np-plurk' => 1, 'mu-np-friendfeed' => 1, 
		'mu-np-jaiku' => 1, 'mu-np-identica' => 1,

		'mu-ep-text' => 'Edited a post: #title#', 'mu-ep-link' => 1,
		'mu-ep-twitter' => 1, 'mu-ep-plurk' => 1, 'mu-ep-friendfeed' => 1, 
		'mu-ep-jaiku' => 1, 'mu-ep-identica' => 1,

		'mu-init' => 1, 'mu-url-shortener' => '', 
		'mu-url-custom-url' => '', 'mu-url-custom-name' => ''
	);

	foreach($defaults as $option => $value)
		add_option($option, $value);
}

if($_POST['submit-type'] == "options")
{
	update_option('mu-np-text', $_POST['mu-np-text']);
	update_option('mu-np-link', ($_POST['mu-np-link']) ? 1 : 0);
	update_option('mu-ep-text', $_POST['mu-ep-text']);
	update_option('mu-ep-link', ($_POST['mu-ep-link']) ? 1 : 0);
	update_option('mu-url-shortener', $_POST['mu-url-shortener']);
	update_option('mu-url-custom-url', $_POST['mu-url-custom-url']);
	update_option('mu-url-custom-name', $_POST['mu-url-custom-name']);

	foreach($mu->services as $service)
	{
		update_option("mu-np-$service", ($_POST["mu-np-$service"]) ? 1 : 0);
		update_option("mu-ep-$service", ($_POST["mu-ep-$service"]) ? 1 : 0);
	}

	echo '<div id="message" class="updated fade"><strong><br/>Preferences saved!<br/><br/></strong></div>';
}
elseif($_POST['submit-type'] == "twitter")
{
	if($_POST['submit'] == "Save Login")
	{
		if($_POST['username'] != '' and $_POST['password'] != '')
		{
			if(verify_credentials("twitter", $_POST['username'], $_POST['password']))
			{
				update_option('mu-twitter-user', $_POST['username']);
				update_option('mu-twitter-pwd', base64_encode($_POST['password']));
				update_option('mu-twitter-encrypted', base64_encode($_POST['username'].":".$_POST['password']));
			
				echo '<div id="message" class="updated fade"><strong><br/>Login details saved!<br/><br/></strong></div>';
			}
			else
				echo "<div class='mu_error'>The credentials you provided are invalid.</div>";
		}
		else
			echo "<div class='mu_error'>You need to provide both your username and your password.</div>";
	}
	elseif($_POST['submit'] == "Clear")
	{
		update_option('mu-twitter-user', "");
		update_option('mu-twitter-pwd', "");
		update_option('mu-twitter-encrypted', "");
		echo '<div id="message" class="updated fade"><strong><br/>Login details cleared!<br/><br/></strong></div>';
	}
}
elseif($_POST['submit-type'] == "plurk")
{
	if($_POST['submit'] == "Save Login")
	{
		if($_POST['username'] != '' and $_POST['password'] != '')
		{
			if(verify_credentials("plurk", $_POST['username'], $_POST['password']))
			{
				update_option('mu-plurk-user', $_POST['username']);
				update_option('mu-plurk-pwd', base64_encode($_POST['password']));
	
				echo '<div id="message" class="updated fade"><strong><br/>Login details saved!<br/><br/></strong></div>';
			}
			else
				echo "<div class='mu_error'>The credentials you provided are invalid.</div>";
		}
		else
			echo "<div class='mu_error'>You need to provide both your username and your password.</div>";
	}
	elseif($_POST['submit'] == "Clear")
	{
		update_option('mu-plurk-user', "");
		update_option('mu-plurk-pwd', "");
		echo '<div id="message" class="updated fade"><strong><br/>Login details cleared!<br/><br/></strong></div>';
	}
}
elseif($_POST['submit-type'] == "friendfeed")
{
	if($_POST['submit'] == "Save Login")
	{
		if($_POST['username'] != '' and $_POST['key'] != '')
		{
			if(verify_credentials("friendfeed", $_POST['username'], $_POST['key']))
			{
				update_option('mu-friendfeed-user', $_POST['username']);
				update_option('mu-friendfeed-pwd', base64_encode($_POST['key']));
	
				echo '<div id="message" class="updated fade"><strong><br/>Login details saved!<br/><br/></strong></div>';
			}
			else			
				echo "<div class='mu_error'>The credentials you provided are invalid.</div>";
		}
		else
			echo "<div class='mu_error'>You need to provide both your username and your API key.</div>";
	}
	elseif($_POST['submit'] == "Clear")
	{
		update_option('mu-friendfeed-user', "");
		update_option('mu-friendfeed-pwd', "");
		echo '<div id="message" class="updated fade"><strong><br/>Login details cleared!<br/><br/></strong></div>';
	}
}
elseif($_POST['submit-type'] == "jaiku")
{
	if($_POST['submit'] == "Save Login")
	{
		if($_POST['username'] != '' and $_POST['key'] != '')
		{
			if(verify_credentials("jaiku", $_POST['username'], $_POST['key']))
			{
				update_option('mu-jaiku-user', $_POST['username']);
				update_option('mu-jaiku-pwd', base64_encode($_POST['key']));
		
				echo '<div id="message" class="updated fade"><strong><br/>Login details saved!<br/><br/></strong></div>';
			}
			else
				echo "<div class='mu_error'>The credentials you provided are invalid.</div>";
		}
		else
			echo "<div class='mu_error'>You need to provide both your username and your API key.</div>";
	}
	elseif($_POST['submit'] == "Clear")
	{
		update_option('mu-jaiku-user', "");
		update_option('mu-jaiku-pwd', "");
		echo '<div id="message" class="updated fade"><strong><br/>Login details cleared!<br/><br/></strong></div>';
	}
}
elseif($_POST['submit-type'] == "identica")
{
	if($_POST['submit'] == "Save Login")
	{
		if($_POST['username'] != '' and $_POST['password'] != '')
		{
			if(verify_credentials("identica", $_POST['username'], $_POST['password']))
			{
				update_option('mu-identica-user', $_POST['username']);
				update_option('mu-identica-pwd', base64_encode($_POST['password']));
	
				echo '<div id="message" class="updated fade"><strong><br/>Login details saved!<br/><br/></strong></div>';
			}
			else
				echo "<div class='mu_error'>The credentials you provided are invalid.</div>";
		}
		else
			echo "<div class='mu_error'>You need to provide both your username and your password.</div>";
	}
	elseif($_POST['submit'] == "Clear")
	{
		update_option('mu-identica-user', "");
		update_option('mu-identica-pwd', "");
		echo '<div id="message" class="updated fade"><strong><br/>Login details cleared!<br/><br/></strong></div>';
	}
}

function check_box($field)
{
	if(get_option($field) == '1')
		echo " checked";
}

function write_box($name, $display)
{
	$disabled = null;
	$service = preg_replace('/mu-[ne]p-/', '', $name);
	if(get_option("mu-$service-user") == '' and get_option("mu-$service-pwd") == '')
		$disabled = 'disabled';
	
	echo '<td width="20"><input type="checkbox" name="'.$name.'" value="1"'.(get_option($name) == '1' ? 'checked' : '').' '.$disabled.'></td><td width="200"><label for="'.$name.'">'.$display.'</label></td>' . "\n";
}

function write_radio($name, $display)
{
	$checked = null;
	if(get_option('mu-url-shortener') == $name)
		$checked = " checked";
	
	echo "<td width=\"150\"><input type=\"radio\" name=\"mu-url-shortener\" value=\"$name\"$checked> $display</td>";
}

function verify_credentials($service, $username, $password)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	if($service == "twitter")
	{
		$url = "http://twitter.com/account/verify_credentials.xml";
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	}
	elseif($service == "plurk")
	{
		$url = "http://www.plurk.com/Users/login?redirect=main";
		curl_setopt($ch, CURLOPT_POSTFIELDS, "nick_name=$username&password=$password");
	}
	elseif($service == "friendfeed")
	{
		$url = "http://friendfeed.com/api/validate";
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	}
	elseif($service == "identica")
	{
		$url = "http://identi.ca/main/login";
		curl_setopt($ch, CURLOPT_POSTFIELDS, "nickname=$username&password=$password");

	}
	else
		return false;


	curl_setopt($ch, CURLOPT_URL, $url);
	$response = curl_exec($ch);
	$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if($service == "plurk")
		return !ereg("incorrect_login", $response);
	elseif($service == "identica")
		return eregi("http://identi.ca/$username/all", $response);
	else
		return ($response_code == 200);
}

$base_url = get_option('siteurl')."/wp-admin/edit.php?page=mu/mu.php";
?>
<style type="text/css">
fieldset { margin: 10px 0; border:1px solid #CECECE; padding:10px; }
a.mu { text-decoration:none; }
#mu_error { border: 1px solid red; padding: 20px; margin: 20px; color: red; }
</style>
<div class="wrap">
	<h2>mu Options</h2>
	<form method="post">
	<div>
		<p>
			<a href="<?=$base_url;?>" class="mu">Post Settings</a>&nbsp;&nbsp;
			<a href="<?=$base_url;?>&action=twitter" class="mu">Twitter</a>&nbsp;&nbsp;
			<a href="<?=$base_url;?>&action=plurk" class="mu">Plurk</a>&nbsp;&nbsp;
			<a href="<?=$base_url;?>&action=friendfeed" class="mu">FriendFeed</a>&nbsp;&nbsp;
			<a href="<?=$base_url;?>&action=identica" class="mu">Identi.ca</a>&nbsp;&nbsp;
			<a href="http://mark.bockenstedt.net/?p=64" class="mu">Help!</a>
		</p>
<?
if($_GET['action'] == "twitter")
{ ?>
	<fieldset>
		<legend><b>Your Twitter Account Details:</b></legend>
		<p><a href="https://twitter.com/signup" target="_blank" class="mu">Create an account</a></p>
		<p>
			<label for="username">Username:</label>
			<input type="text" name="username" value="<?=get_option('mu-twitter-user');?>" />
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="password" name="password" value="<?=base64_decode(get_option('mu-twitter-pwd'));?>"/>
		</p>
		<p>
			<input type="hidden" name="submit-type" value="twitter" />
			<input type="submit" name="submit" value="Save Login" />&nbsp;
			<input type="submit" name="submit" value="Clear" />
		</p>
	</fieldset>
<?
}
elseif($_GET['action'] == "plurk")
{ ?>
	<fieldset>
		<legend><b>Your Plurk Account Details:</b></legend>
		<p><a href="http://plurk.com/Users/showRegister" target="_blank" class="mu">Create an account</a></p>
		<p>
			<label for="username">Username:</label>
			<input type="text" name="username" value="<?=get_option('mu-plurk-user');?>" />
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="password" name="password" value="<?=base64_decode(get_option('mu-plurk-pwd'));?>" />
		</p>
		<p>
			<input type="hidden" name="submit-type" value="plurk" />
			<input type="submit" name="submit" value="Save Login" />&nbsp;
			<input type="submit" name="submit" value="Clear" />
		</p>
	</fieldset>
<?
}
elseif($_GET['action'] == "friendfeed")
{ ?>
	<fieldset>
		<legend><b>Your FriendFeed Account Details:</b></legend>
		<p><a href="https://friendfeed.com/account/create" target="_blank" class="mu">Create an account</a></p>
		<p>
			<label for="username">Username:</label>
			<input type="text" name="username" value="<?=get_option('mu-friendfeed-user');?>" />
		</p>
		<p>
			<label for="key">Remote Key:</label>
			<input type="text" name="key" value="<?=base64_decode(get_option('mu-friendfeed-pwd'));?>" /> (Go <a href="https://friendfeed.com/account/api" target="_blank" class="mu">here</a> to get your key)
		</p>
		<p>
			<input type="hidden" name="submit-type" value="friendfeed" />
			<input type="submit" name="submit" value="Save Login" />&nbsp;
			<input type="submit" name="submit" value="Clear" />
		</p>
	</fieldset>
<?
}
elseif($_GET['action'] == "jaiku")
{ ?>
	<fieldset>
		<legend><b>Your Jaiku Account Details:</b></legend>
		<p>
			<label for="username">Username:</label>
			<input type="text" name="username" value="<?=get_option('mu-jaiku-user');?>" />
		</p>
		<p>
			<label for="key">Remote Key:</label>
			<input type="text" name="key" value="<?=base64_decode(get_option('mu-jaiku-pwd'));?>" /> (Go <a href="http://api.jaiku.com/key" target="_blank" class="mu">here</a> to get your key)
		</p>
		<p>
			<input type="hidden" name="submit-type" value="jaiku" />
			<input type="submit" name="submit" value="Save Login" />&nbsp;
			<input type="submit" name="submit" value="Clear" />
		</p>
	</fieldset>
<?
}
elseif($_GET['action'] == "identica")
{ ?>
	<fieldset>
		<legend><b>Your Identi.ca Account Details</b></legend>
		<p><a href="" target="_blank" class="mu">Create an account</a></p>
		<p>
			<label for="username">Username:</label>
			<input type="text" name="username" value="<?=get_option('mu-identica-user');?>" />
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="password" name="password" value="<?=base64_decode(get_option('mu-identica-pwd'));?>" />
		</p>
		<p>
			<input type="hidden" name="submit-type" value="identica" />
			<input type="submit" name="submit" value="Save Login" />&nbsp;
			<input type="submit" name="submit" value="Clear" />
		</p>
	</fieldset>
<?
}
else
{ ?>
		<fieldset>
			<legend><b>New Post Published:</b></legend>
			<p>
				<label for="mu-np-text">Text for this update:</label><br/>
				<input type="text" name="mu-np-text" size="40" maxlength="146" value="<?=get_option('mu-np-text');?>"/>
				<input type="checkbox" name="mu-np-link" value="1"<?=check_box('mu-np-link');?> /> Include link to post
				<br/>
				<small>( use #title# as a placeholder for the post's title )</small>
			</p>
			<p>
				<table>
				<caption style="text-align:left"><b>Default Send To:</b></caption>
				<tr>
				<? write_box('mu-np-twitter', "Twitter"); ?>
				<? write_box('mu-np-friendfeed', "FriendFeed"); ?>
				</tr>
				<tr>
				<? write_box('mu-np-plurk', "Plurk"); ?>
				<? write_box('mu-np-identica', "Identi.ca"); ?>
				</tr>
				</table>
			</p>
		</fieldset>

<!--
		<fieldset>
			<legend><b>Existing Post Updated:</b></legend>
			<p>
				<label for="mu-ep-text">Text for this update:</label><br/>
				<input type="text" name="mu-ep-text" size="40" maxlength="146" value="<?=get_option('mu-ep-text');?>" />
				<input type="checkbox" name="mu-ep-link" value="1"<?=check_box('mu-ep-link');?> /> Include link to post
			</p>
			<p>
				<table>
				<caption style="text-align:left"><b>Default Send To:</b></caption>
				<tr>
				<? write_box('mu-ep-twitter', "Twitter");?>
				<? write_box('mu-ep-friendfeed', "FriendFeed"); ?>
				</tr>
				<tr>
				<? write_box('mu-ep-plurk', "Plurk"); ?>
				<? write_box('mu-ep-identica', "Identi.ca"); ?>
				</tr>
				</table>
			</p>
		</fieldset>
-->

		<fieldset>
			<legend><b>URL Shortening Service:</b></legend>
			<p>
				<table>
				<tr>
				<? write_radio("", "None"); ?>
				<? write_radio("tinyurl", "TinyURL"); ?>
				</tr>
				<tr>
				<? write_radio("bit.ly", "bit.ly"); ?>
				<? write_radio("is.gd", "is.gd"); ?>
				</tr>
				<tr>
				<? write_radio("snurl", "Snurl"); ?>
				<? write_radio("ri.ms", "ri.ms"); ?>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
				<td colspan="2">
					<table>
					<tr>
						<td colspan="2">
							<input type="radio" name="mu-url-shortener" value="custom" <?=(get_option('mu-url-shortener') == "custom") ? "checked" : ""; ?>/> Custom
						</td>
					</tr>
					<tr>
						<td>Name:</td>
						<td><input type="text" name="mu-url-custom-name" value="<?=get_option('mu-url-custom-name');?>" /></td>
					</tr>
					<tr>
						<td>URL:</td>
						<td>
							<input type="text" name="mu-url-custom-url" value="<?=get_option('mu-url-custom-url');?>" size="50"/>
							<small>( use #link# as a placeholder for the post's link )</small>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
			</p>
		</fieldset>

		<input type="hidden" name="submit-type" value="options">
		<input type="submit" name="submit" value="Save" />
<?
}
?>
	</div>
	</form>
</div>
