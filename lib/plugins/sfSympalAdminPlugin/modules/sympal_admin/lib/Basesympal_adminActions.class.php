<?php

/**
 * Provides basic admin actions like clear cache, php info, etc
 * 
 * @package     sfSympalAdminPlugin
 * @subpackage  actions
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 */
abstract class Basesympal_adminActions extends sfActions
{
  /**
   * Clears different types of symfony cache based on the
   * given "type" parameter
   */
  public function executeClear_cache(sfWebRequest $request)
  {
    $this->enableEditor(false);
    $this->setLayout(false);
    
    $this->types = array(
      'config',
      'i18n',
      'routing',
      'module',
      'template',
      'menu'
    );

    if ($type = $request->getParameter('type'))
    {
      switch ($type)
      {
        case 'config':
        case 'i18n':
        case 'routing':
          $this->resetSympalRoutesCache();
        case 'module':
        case 'template':
          $this->clearCache(array('type' => $type));
        break;
        case 'menu':
          $this->clearMenuCache();
        break;
      }

      $msg = sprintf(__('Clearing %s cache...'), $type);
      $this->renderText($msg);
      
      return sfView::NONE;
    }
  }

  /**
   * Signin action for the admin. Will redirect to the dashboard if authenticated
   */
  public function executeSignin($request)
  {
    $user = $this->getUser();
    if ($user->isAuthenticated())
    {
      return $this->redirect('@sympal_dashboard');
    }

    $this->getResponse()->setTitle('Sympal Admin / Signin');

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin'); 
    $this->form = new $class();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues(); 
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

        return $this->redirect('@sympal_dashboard');
      }
    }
  }

  public function executeSave_nested_set(sfWebRequest $request)
  {
    $this->getContext()->getLogger()->log(var_export($request->getParameterHolder()->getAll(), true));
    $data = $request->getParameterHolder()->getAll();
    foreach ($data as $key => $value)
    {
      if (strpos($key, 'sf_admin_nested_set_') !== false)
      {
        $items = $value['items'];
        foreach ($items as $item)
        {
          if (isset($item['children']))
          {
            $this->_saveChildren($request->getParameter('model'), $item['id'], $item['children']);
          }
        }
      }
    }

    $this->clearCache();

    return sfView::NONE;
  }

  private function _saveChildren($model, $parentId, $children)
  {
    $menuItem = Doctrine_Core::getTable($model)->find($parentId);
    foreach ($children as $child)
    {
      $childMenuItem = Doctrine_Core::getTable($model)->find($child['id']);
      $childMenuItem->getNode()->moveAsLastChildOf($menuItem);
      $this->getContext()->getLogger()->log('test');
      if (isset($child['children']))
      {
        $this->_saveChildren($model, $child['id'], $child['children']);
      }
    }
  }

  /**
   * Returns the phpinfo() information
   */
  public function executePhpinfo(sfWebRequest $request)
  {
    $this->setLayout(false);
    $this->enableEditor(false);
  }

  /**
   * Action that tests your current server setup and outputs a report
   * of what passes / fails the tests
   */
  public function executeCheck_server(sfWebRequest $request)
  {
    $this->getResponse()->setTitle(__('Sympal Admin / Check Server'));

    $check = new sfSympalServerCheck();
    $this->renderer = new sfSympalServerCheckHtmlRenderer($check);
  }
}
