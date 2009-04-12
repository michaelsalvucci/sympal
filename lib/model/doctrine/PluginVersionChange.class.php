<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginVersionChange extends BaseVersionChange
{
  public function getRevertValue()
  {
    return $this['old_value'];
  }

  public function getCurrentValue()
  {
    $version = $this->getVersion();
    $record = $version->getRecord();
    return $record->get($this['field']);
  }

  public function getRenderValue($type)
  {
    $value = null;
    $version = $this->getVersion();
    $record = $version->getRecord();
    $table = $record->getTable();
    foreach ($table->getRelations() as $relation)
    {
      if ($relation['local'] == $this['field'])
      {
        $q = Doctrine::getTable($relation['class'])
          ->createQuery()
          ->where('id = ?', $this[$type.'_value']);
        $result = $q->fetchOne();
        $value = (string) $result;
      }
    }

    if (!$value)
    {
      $value = $this[$type.'_value'];
    }

    return nl2br($value);
  }
}