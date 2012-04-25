<?php 
session_start();
if(!isset($_SESSION['password'])):?>
<form method="POST" action="licence-login.php">
  Username: <input type="text" name="login" size="15" /><br />
  Password: <input type="password" name="password" size="15" /><br />
  <div align="center">
    <p><input type="submit" value="Login" /></p>
  </div>
</form>
<?php else:?>
	<form method="POST" action="licence-generation.php">
  Hostname: <input type="text" name="hostname" size="15" /><br />
  Module: 	<SELECT name="module">
				<OPTION VALUE="SoColissimoFlexibilite">SoColissimoFlexibilite</OPTION>
				<OPTION VALUE="SoColissimoLiberte">SoColissimoLiberte</OPTION>
				<OPTION VALUE="AdvancedSlideshow">AdvancedSlideshow</OPTION>
				<OPTION VALUE="NewsletterDolist">NewsletterDolist</OPTION>
				<OPTION VALUE="Brand">Brand</OPTION>
				<OPTION VALUE="ReviewBoost">ReviewBoost</OPTION>
				<OPTION VALUE="OgoneDirectLink">OgoneDirectlink</OPTION>
				<OPTION VALUE="SprintSecure">SprintSecure</OPTION>
			</SELECT>
  <div align="left">
    <p><input type="submit"/></p>
  </div>
</form>
<?php endif; ?>