<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_contact_form($params, &$smarty) {

	if (empty($params['email'])){
		echo '<div class="formError">A email address to send to must be specified in order to use this plugin.</div>';
		exit;
	}else{
		$to = $params['email'];
	}
	
	$style = true;
	if (!empty($params['style']))$style = $params['style'];
	$name = '';
	if (!empty($_POST['name'])) $name = trim($_POST['name']);
	$email = '';
	if (!empty($_POST['email'])) $email = trim($_POST['email']);
	$subject = '';
	if (!empty($_POST['subject'])) $subject = trim($_POST['subject']);
	$message = '';
	if (!empty($_POST['message'])) $message = trim($_POST['message']);
	
	if($_SERVER['REQUEST_METHOD']=='POST'){
		$extra = "From: $name <$email>\r\nReply-To: $email\r\n";
		echo '<div class="contactMessage"';
		echo ($style)?' style="font-weight: bold; color: red;"':'';
		echo '>';

		if (empty($name)) {
			echo 'Please Enter Your Name.';
		}elseif (empty($email)) {
			echo 'Please Enter Your Email Address.';
		}elseif (empty($subject)) {
			echo 'Please Enter a Subject.';
		}elseif (empty($message)) {
			echo 'Please Enter a Message.';
		}elseif (!validEmail($email)) {
			echo 'Your Email Address is Not Valid.';
		}elseif (@mail($to, $subject, $message, $extra)){
			echo "Your message was successfully sent.</div>";
			return;
		}else{
			echo 'Sorry, the message was not sent. The server may be down!</div>';
			return;
		}

		echo '</div>';
	}
	?>

	<form action="<?php $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] ?>" method="post" name="contactForm"<?php echo ($style)?' style="font-weight: bold;"':''; ?>>
	Your Name:<input type="text" name="name" value="<?php echo $name; ?>" size="50"<?php echo ($style)?' style="width: 350px; border: solid 1px black; display: block; margin-bottom: 7px;"':''; ?>>
	Your Email Address:<input type="text" name="email" value="<?php echo $email; ?>" size="50"<?php echo ($style)?' style="width: 350px; border: solid 1px black; display: block; margin-bottom: 7px;"':''; ?>>
	Subject:<input type="text" name="subject" value="<?php echo $subject; ?>" size="50"<?php echo ($style)?' style="width: 350px; border: solid 1px black; display: block; margin-bottom: 7px;"':''; ?>>
	Message:<TEXTAREA NAME="message" COLS="40" ROWS="10"<?php echo ($style)?' style="width: 350px; border: solid 1px black; display: block; margin-bottom: 7px;"':''; ?>><?php echo $message; ?></textarea>
	<input type="submit" value="Submit"><input type="reset" value="Clear">
	</form>

<?php
}

function smarty_cms_help_function_contact_form() {
	?>
	<h3>What does this do?</h3>
	<p>Display's a contact form.  This can be used to allow others to send an email message to the address specified.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{contact_form email="yourname@yourdomain.com"}</code><br>
	<br>
	If you would like to send an email to multiple adresses, seperate each address with a comma.</p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li>email - The email address that the message will be sent to.</li>
		<li><em>(optional)</em>style - true/false, use the predefined styles. Default is true.</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_contact_form() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@provisiontech.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

function validEmail($email) {
	if (!preg_match("/^([\w|\.|\-|_]+)@([\w||\-|_]+)\.([\w|\.|\-|_]+)$/i", $email)) {
		return false;
		exit;
	}
	return true;
}

?>
