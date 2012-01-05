<?php
/* template head */
/* end template head */ ob_start(); /* template body */ ?><h1>Users List</h1>
<ul>
 <?php 
$_loop0_data = (isset($this->scope["all_users"]) ? $this->scope["all_users"] : null);
if ($this->isArray($_loop0_data) === true)
{
	foreach ($_loop0_data as $tmp_key => $this->scope["-loop-"])
	{
		$_loop0_scope = $this->setScope(array("-loop-"));
/* -- loop start output */
?>
   <li><?php echo $this->scope["name"];?> : <?php echo $this->scope["username"];?></li>
 <?php 
/* -- loop end output */
		$this->setScope($_loop0_scope, true);
	}
}
?>

</ul><?php  /* end template body */
return $this->buffer . ob_get_clean();
?>