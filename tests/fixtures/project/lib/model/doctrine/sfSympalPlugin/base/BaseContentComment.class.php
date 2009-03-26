<?php

/**
 * BaseContentComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $content_id
 * @property integer $comment_id
 * @property Content $Content
 * @property Comment $Comment
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class BaseContentComment extends sfSympalDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('content_comment');
        $this->hasColumn('content_id', 'integer', 4, array('type' => 'integer', 'primary' => true, 'length' => '4'));
        $this->hasColumn('comment_id', 'integer', 4, array('type' => 'integer', 'primary' => true, 'length' => '4'));
    }

    public function setUp()
    {
        $this->hasOne('Content', array('local' => 'content_id',
                                       'foreign' => 'id',
                                       'onDelete' => 'CASCADE'));

        $this->hasOne('Comment', array('local' => 'comment_id',
                                       'foreign' => 'id',
                                       'onDelete' => 'CASCADE'));
    }
}