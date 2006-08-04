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
	$inputStyle = 'style="width:100%;border: 1px solid black; margin:0 0 1em 0;"'; // input boxes
	$taStyle = 'style="width:100%; border: 1px solid black;"'; // TextArea boxes
	$formStyle = 'style="width:30em; important; font-weight: bold;"'; // form
	$errorsStyle = 'style="color: white; background-color: red; font-weight: bold; border: 3px solid black; margin: 1em;"'; // Errors box (div)
        $labelStyle = 'style="display:block;"';
        $buttonStyle = 'style="float:left; width:50%; margin-top:1em;"';
        $fieldsetStyle = 'style="padding:1em;"';

	$errors=$name=$email=$subject=$message = '';
	if (FALSE == empty($params['subject_get_var']) && FALSE == empty($_GET[$params['subject_get_var']]))
	  {
	    $subject = $_GET[$params['subject_get_var']];
	  }
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
                 <fieldset <?php echo ($style) ? $fieldsetStyle:''; ?>>
                        <legend>Contact</legend>
			<label for="name" <?php echo ($style) ? $labelStyle:''; ?> >Your name :</label>
			<input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" <?php echo ($style) ? $inputStyle:''; ?>/>

			<label for="email" <?php echo ($style) ? $labelStyle:''; ?> >Your email address : </label>
			<input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" <?php echo ($style) ? $inputStyle:''; ?>/>

			<label for="subject" <?php echo ($style) ? $labelStyle:''; ?> >Subject : </label>
			<input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" <?php echo ($style) ? $inputStyle:''; ?>/>

			<label for="message" <?php echo ($style) ? $labelStyle:''; ?> >Message : </label>
			<textarea id="message" name="message" <?php echo ($style) ? $taStyle:''; ?>><?php echo $message; ?></textarea>

		        <input type="submit" class="button" value="Submit" <?php echo ($style) ? $buttonStyle: ''; ?> /> 
                        <input type="reset"  class="button" value="Clear" <?php echo ($style) ? $buttonStyle: ''; ?> />
                 </fieldset>
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
		<li><em>(optional)</em>subject_get_var - string, allows you to specify which _GET var to use as the default value for subject.
               <p>Example:</p>
               <pre>{contact_form email="yourname@yourdomain.com" subject_get_var="subject"}</pre>
             <p>Then call the page with the form on it like this: /index.php?page=contact&subject=test+subject</p>
             <p>And the following will appear in the "Subject" box: "test subject"
           </li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_contact_form() {
	?>
	<p>Author: Brett Batie &lt;brett-cms@classicwebdevelopment.com&gt; &amp; Simon van der Linden &lt;ifmy@geekbox.be&gt;</p>
	<p>Version: 1.3 (20060803)</p>
	<p>
	Change History:<br/>
        <ul>
        <li>l.2 : various improvements (errors handling, etc.)</li>
        <li>1.3 : added subject_get_var parameter (by elijahlofgren)</li>
        </ul>
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
