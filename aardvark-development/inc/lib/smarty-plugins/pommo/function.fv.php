<?php

// provide a smarty function as a shortcut for {validate}

function smarty_function_fv($params, &$smarty)
{
  static $pre = '';
  static $post = '';
  static $form = SMARTY_VALIDATE_DEFAULT_FORM;
  
  $f = (isset($params['validate'])) ? $params['validate'] : false;
  $m = (isset($params['message'])) ? $params['message'] : false;
  
  $form = (isset($params['form'])) ? $params['form'] : $form;
  
  $pre = (isset($params['prepend'])) ? $params['prepend'] : $pre;
  $post = (isset($params['append'])) ? $params['append'] : $post;
  
  $prepend = (isset($params['pre'])) ? $params['pre'] : $pre;
  $append = (isset($params['post'])) ? $params['post'] : $post;
  
  $p = array(
	'id' => $f,
	'message' => (isset($smarty->_tpl_vars['vMsg'][$f])) ? $smarty->_tpl_vars['vMsg'][$f] : 'input error',
	'append' => 'vErr'
	);

  if($form) 
  	$p['form'] = $form;

  if ($f) 
  	return smarty_function_validate($p,$smarty);
  if ($m && isset($smarty->_tpl_vars['vErr'][$m]))
  	return $prepend.$smarty->_tpl_vars['vErr'][$m].$append;
  return;
}
?>