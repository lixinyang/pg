<?php foreach ($query->result() as $user):?>
<p>id: <?=$user->id?></p>
<p>name: <?=$user->name?></p>
<p>cookie_token: <?=$user->cookie_token?></p>
<p>created: <?=$user->created?></p>
<p>support_project: <?=$user->support_project?></p>
<hr />
<?php endforeach;?>
<?= form_open('/user/add')?>
name:<input type='text' name='name' /><br/>
<input type='submit' value='创建用户' />
</form>