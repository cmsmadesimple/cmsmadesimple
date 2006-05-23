<?php

# CMSMS - CMS Made Simple
#
# (c)2004 by Ted Kulp (wishy@users.sf.net)
#
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_contact_form($params, &$smarty) {

	if (empty($params['email'])){
		echo '<div class="formError">An email address must be specified in order to use this plugin.</div>';
		return;
	}else{
		$to = $params['email'];
	}
	
	$style = true; // Use default styles
	if (!empty($params['style'])) $style = $params['style']; // Except if "false" given in params
	
	// Default styles
	$inputStyle = 'style="width: 350px; border: 1px solid black"'; // input boxes
	$taStyle = 'style="width: 350px; border: 1px solid black;"'; // TextArea boxes
	$formStyle = 'style="font-weight: bold;"'; // form
	$errorsStyle = 'style="color: white; background-color: red; font-weight: bold; border: 3px solid black; margin: 1em;"'; // Errors box (div)

	$errors=$name=$email=$subject=$message = '';
	if($_SERVER['REQUEST_METHOD']=='POST'){
		if (!empty($_POST['name'])) $name = cfSanitize($_POST['name']);
		if (!empty($_POST['email'])) $email = cfSanitize($_POST['email']);
		if (!empty($_POST['subject'])) $subject = cfSanitize($_POST['subject']);
		if (!empty($_POST['message'])) $message = $_POST['message'];

		//Mail headers
		$extra = "From: $name <$email>\r\n";
		$extra .= "Content-Type: text/plain\r\n";
		
		if (empty($name)) $errors .= "\t\t<li>" . 'Please Enter Your Name' . "</li>\n";
		if (empty($email)) $errors .= "\t\t<li>" . 'Please Enter Your Email Address' . "</li>\n";
		if (empty($subject)) $errors .= "\t\t<li>" . 'Please Enter a Subject' . "</li>\n";
		if (empty($message)) $errors .= "\t\t<li>" . 'Please Enter a Message' . "</li>\n";
		if (!validEmail($email)) $errors .= "\t\t<li>" . 'Your Email Address is Not Valid' . "</li>\n";
		
		if (!empty($errors)) {
			echo '<div class="formError" ' . (($style) ? $errorsStyle:'') . '>' . "\n";
			echo '<p>Error(s) : </p>' . "\n";
			echo "\t<ul>\n";
			echo $errors;
			echo "\t</ul>\n";
			echo "</div>";
		}
		elseif (@mail($to, $subject, $message, $extra)) {
			echo '<div class="formMessage">Your message was successfully sent.</div>' . "\n";
			return;
		}
		else {
			echo '<div class="formError" ' . (($style) ? $errorsStyle:'') . '>Sorry, the message was not sent. The server may be down!</div>' . "\n";
			return;
		}
	}
	?>

	<!-- CONTACT_FORM -->
	<form action="<?php $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] ?>" method="post" <?php echo ($style) ? $formStyle:''; ?>>
		<p>
			<label>Your name :</label>
			<input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" size="50" <?php echo ($style) ? $inputStyle:''; ?>/></p>
		<p>
			<label>Your email address : </label>
			<input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" size="50" <?php echo ($style) ? $inputStyle:''; ?>/></p>
		<p>
			<label>Subject : </label>
			<input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" size="50" <?php echo ($style) ? $inputStyle:''; ?>/></p>
		<p>
			<label>Message : <label><br />
			<textarea name="message" cols="40" rows="10" <?php echo ($style) ? $taStyle:''; ?>><?php echo $message; ?></textarea>
		</p>
		<p><input type="submit" value="Submit" /> <input type="reset" value="Clear" /></p>
	</form>
	<!-- END of CONTACT_FORM -->

<?php
}

function smarty_cms_help_function_contact_form() {
	?>
	<h3>What does this do?</h3>
	<p>Display's a contact form. This can be used to allow others to send an email message to the address specified.</p>
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
	<p>Author: Brett Batie &lt;brett-cms@classicwebdevelopment.com&gt; &amp; Simon van der Linden &lt;ifmy@geekbox.be&gt;</p>
	<p>Version: 1.2 (20060416)</p>
	<p>
	Change History:<br/>
	1.2 : various improvements (errors handling, etc.)
	</p>
	<?php
}

function cfsanitize($content){
	return str_replace(array("\r", "\n"), "", trim($content));
}

function validEmail($email) {
	if (!preg_match("/^([\w|\.|\-|_]+)@([\w||\-|_]+)\.([\w|\.|\-|_]+)$/i", $email)) {
		return false;
		exit;
	}
	return true;
}

?>
