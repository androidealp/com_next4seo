<?php

defined('_JEXEC') or die;

class Helper{

  public static $jinput='';
  public static $next = '';
  public static $prev = '';
  public static $total = 0;
  public static $total_inpage = 0;
  public static $page = 1;
  public static function SimplePage($limit, $total)
  {
    self::$total = $total;

    self::$total_inpage = $limit;

    self::$jinput = JFactory::getApplication()->input;

    self::$next = self::$jinput->get('next',0);
    self::$prev = self::$jinput->get('prev',0);

    $type = self::$jinput->get('view',0);

    if(self::$next)
    {
        self::$page = round(self::$next / $limit);

        self::$page++;
    }

    if(self::$prev)
    {
      self::$page = round(self::$prev / $limit);
    }


    if(self::$next && !self::$prev )
    {

      if($total >= (self::$next+$limit))
      {

        self::$prev = self::$next;
        self::$next = self::$next+$limit;

      }else{
        self::$prev = self::$next-$limit;
      }

    }

    if(self::$prev && !self::$next)
    {
      if((self::$prev-$limit) < 0)
      {
        self::$prev = 0;
        self::$next = $limit;
      }else{
        self::$prev = self::$prev-$limit;
        self::$next=self::$prev+$limit;
      }
    }

    if(!self::$prev && !self::$next)
    {
      self::$prev = 0;
      self::$next=($total>$limit)?$limit:0;
    }

    return Helper::montPaginationSimple($type);

  }


  private static function montPaginationSimple($type)
  {
    $pagination = [];


    $pagination['total_inpage'] = self::$total_inpage;
    $pagination['total'] = self::$total;
    $pagination['page'] = self::$page;

    $pagination['urlprev'] = 'index.php?option=com_next4seo&view='.$type.'&prev='.self::$prev;

    $pagination['urlnext'] = 'index.php?option=com_next4seo&view='.$type.'&next='.self::$next;

    return $pagination;

  }

}
