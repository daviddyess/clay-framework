<script type="text/javascript">
$('document').ready(function(){
  	$("form.recovery").hide();
	$("input.recover").click(function () {
  	  	$("form.recovery").show();
  	});
	$("input.cancel").click(function () {
  	  	var form = $(this).attr('id');
  	  	$("form." + form).hide();
  	});
});
</script>
<div class="app-head">Authenticate</div>
<div class="app-content">
<form method="post" action="?app=installer">
<fieldset><legend>Security Check</legend>
	<p>
  <label for="pass1" title="Password">Password:</label>
  <input class="form-textxlong" type="password" name="passcode" id="pass1" maxlength="128" />
  </p>  
  <input type="submit" name="submit" value="Authenticate" id="submit" /> <input type="button" class="recover" value="Reset Passcode" id="reset" />
</fieldset>
</form>
<form method="post" class="recovery" action="?app=installer">
<fieldset><legend>Q&amp;A</legend>
	<p>
  <label for="q" title="Question">Question:</label>
  <?php echo $question; ?>
  </p>
  <p>
  <label for="a" title="Answer">Answer:</label>
  <input class="form-textxlong" type="text" name="answer" id="a" maxlength="200" />
  </p>
  <input type="submit" name="submit" value="Evaluate" id="submit" /> <input type="button" class="cancel" value="Cancel" id="recovery" />
</fieldset>
</form>
</div>