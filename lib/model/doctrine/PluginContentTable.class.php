<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginContentTable extends Doctrine_Table
{
  public function getTypeQuery($typeName, $alias = 'c')
  {
    $table = Doctrine::getTable($typeName);

    $q = $this->getBaseQuery($alias);

    $q->innerJoin($alias.'.'.$typeName.' cr');

    if (sfSympalConfig::isI18nEnabled($typeName))
    {
      $q->leftJoin('cr.Translation crt');
    }

    $q = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this, 'sympal.load_'.sfInflector::tableize($typeName).'_query'), $q)->getReturnValue();

    if (method_exists($table, 'getContentQuery'))
    {
      $q = $table->getContentQuery($q);
    }

    return $q;
  }

  public function getContent($params = array())
  {
    $request = sfContext::getInstance()->getRequest();
    $contentType = $request->getParameter('sympal_content_type');
    $contentId = $request->getParameter('sympal_content_id');
    $contentSlug = $request->getParameter('sympal_content_slug');
    $q = $this->getTypeQuery($contentType);

    if ($contentId)
    {
      $q->andWhere('c.id = ?', $contentId);
    } else if ($contentSlug) {
      if ($this->hasRelation('Translation') && $this->getRelation('Translation')->getTable()->hasField('slug'))
      {
        $q->andWhere('c.slug = ? OR ct.i18n_slug = ?', array($contentSlug, $contentSlug));
      } else {
        $q->andWhere('c.slug = ?', $contentSlug);
      }
    }

    foreach ($params as $key => $value)
    {
      if ($key == 'slug' && $this->hasRelation('Translation'))
      {
        $q->andWhere('c.slug = ? OR ct.i18n_slug = ?', array($value, $value));
        continue;
      }

      if ($this->hasField($key))
      {
        $q->andWhere('c.'.$key.' = ?', $value);
      } else if ($this->hasRelation('Translation') && $this->getRelation('Translation')->getTable()->hasField($key)) {
        $q->andWhere('ct.'.$key, $value);
      } else if ($this->getRelation($contentType)->getTable()->hasField($key)) {
        $q->andWhere('cr.'.$key.' = ?', $value);
      }
    }

    $q = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this, 'sympal.filter_get_content_query'), $q)->getReturnValue();

    $content = $q->fetchOne();

    $content = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this, 'sympal.filter_get_content'), $content)->getReturnValue();

    return $content;
  }

  public function getBaseQuery($alias = 'c')
  {
    $sympalContext = sfSympalContext::getInstance();
    $q = Doctrine_Query::create()
      ->from('Content '.$alias)
      ->leftJoin($alias.'.Permissions p')
      ->leftJoin($alias.'.Groups g')
      ->leftJoin($alias.'.Template cte')
      ->leftJoin($alias.'.Slots sl')
      ->leftJoin('sl.Type sty')
      ->leftJoin($alias.'.Type ty')
      ->leftJoin('ty.ContentTemplates t')
      ->leftJoin($alias.'.MasterMenuItem m')
      ->leftJoin($alias.'.MenuItem mm')
      ->leftJoin($alias.'.CreatedBy u')
      ->innerJoin($alias.'.Site csi')
      ->andWhere('csi.slug = ?', $sympalContext->getSiteSlug());

    if (!sfSympalToolkit::isEditMode())
    {
      $expr = new Doctrine_Expression('NOW()');
      $q->andWhere($alias.'.is_published = ?', true)
        ->andWhere($alias.'.date_published <= '.$expr);
    }

    if (sfSympalConfig::isI18nEnabled('ContentSlot'))
    {
      $q->leftJoin('sl.Translation slt');
    }

    if (sfSympalConfig::isI18nEnabled('Content'))
    {
      $q->leftJoin($alias.'.Translation ct');
    }

    if (sfSympalConfig::isI18nEnabled('MenuItem'))
    {
      $q->leftJoin('m.Translation mt');
    }

    $q = sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this, 'sympal.filter_content_base_query'), $q)->getReturnValue();

    return $q;
  }

  public function getAdminGenQuery($q)
  {
    $sympalContext = sfSympalContext::getInstance();

    $contentTypes = sfSympalCache::getContentTypes();
    $filters = sfContext::getInstance()->getUser()->getAttribute('sympal_content.filters', array(), 'admin_module');
    $contentTypeId = $filters['content_type_id'];
    $name = $contentTypes[$contentTypeId];

    $q = Doctrine::getTable('Content')
      ->getTypeQuery($name, 'r');

    return $q;
  }
}