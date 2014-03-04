<?php ?>
<ul>
	<li>
		<label><?php echo elgg_echo('user:name:label'); ?> *</label><br />
		<input type="text" name="question_name" value="<?php echo $_SESSION['registerevent_values']['question_name']; ?>" class="input-text" />
	</li>
	
	<li>
		<label><?php echo elgg_echo('email'); ?> *</label><br />
		<input type="text" name="question_email" value="<?php echo $_SESSION['registerevent_values']['question_email']; ?>" class="input-text" />
	</li>
</ul>