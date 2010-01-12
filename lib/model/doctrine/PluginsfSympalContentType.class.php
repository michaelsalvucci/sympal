<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginsfSympalContentType extends BasesfSympalContentType
{
  public function __toString()
  {
    return (string) $this->getLabel();
  }

  public function getSingularUpper()
  {
    return Doctrine_Core::getTable($this->getName())->getOption('name');
  }

  public function getSingularLower()
  {
    return Doctrine_Inflector::tableize(Doctrine_Core::getTable($this->getName())->getOption('name'));
  }

  public function getPluralUpper()
  {
    return Doctrine_Inflector::classify(Doctrine_Core::getTable($this->getName())->getTableName()) . 's';
  }

  public function getPluralLower()
  {
    return Doctrine_Core::getTable($this->getName())->getTableName() . 's';
  }

  public function getRouteName()
  {
    return '@'.str_replace('-', '_', $this->getSlug());
  }

  public function getRoutePath()
  {
    $path = $this->default_path;
    if ($path != '/')
    {
      $path .= '.:sf_format';
    }
    return $path;
  }

  public function getCustomModuleName()
  {
    if ($moduleName = $this->_get('module'))
    {
      return $moduleName;
    } else {
      return 'sympal_'.str_replace('-', '_', $this->getSlug());
    }
  }

  public function hasCustomModule()
  {
    return $this->_get('module') || sfSympalToolkit::moduleAndActionExists($this->getCustomModuleName(), 'index');
  }

  public function getModuleToRenderWith()
  {
    if ($this->hasCustomModule())
    {
      return $this->getCustomModuleName();
    } else {
      return sfSympalConfig::get($this->getName(), 'default_rendering_module', sfSympalConfig::get('default_rendering_module', null, 'sympal_content_renderer'));
    }
  }

  public function getActionToRenderWith()
  {
    if ($actionName = $this->_get('action'))
    {
      return $actionName;
    } else {
      return sfSympalConfig::get($this->getName(), 'default_rendering_action', sfSympalConfig::get('default_rendering_action', null, 'index'));
    }
  }
}