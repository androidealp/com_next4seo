<?php
defined('_JEXEC') or die;

JLoader::register('Model', JPATH_COMPONENT . '/Model.php');
JLoader::register('Helper', JPATH_COMPONENT . '/Helper.php');
class Controller{

public static $jinput='';

public static function getView($view)
{

  self::$jinput = JFactory::getApplication()->input;

  $action = self::$jinput->get('action',0,'string');

  if($view == 'k2')
  {
    return Controller::K2Manager($action);
  }

    return Controller::JoomlaManager($action);
}


private static function K2Manager($action)
{
  if($action === 'ajaxsend')
  {
    return Controller::Ajax("k2");
  }else{
    $data = ['title'=>'Gerenciar SEO K2'];
    $model = new Model;
    $listagem = $model->ListK2();
    $pagination = Helper::SimplePage(50,$model->total);
    $data['pagination'] = $pagination;
    echo Controller::view('k2',$listagem, $data);
  }
}

private static function JoomlaManager($action)
{
    if($action === 'ajaxsend')
    {
      return Controller::Ajax();
    }else{
      $data = ['title'=>'Gerenciar SEO Joomla'];
      $model = new Model;
      $listagem = $model->ListContent();
      $pagination = Helper::SimplePage(50,$model->total);
      $data['pagination'] = $pagination;
      echo Controller::view('joomla',$listagem, $data);
    }
}

private static function Ajax($type="joomla")
{
  $document = JFactory::getDocument();
  $document->setMimeEncoding('application/json');
  $return = ['type'=>'danger', 'msg'=>'Não foi localizado o post'];
  $post    = self::$jinput->post->get('nextform', array(), 'array');

  if($post)
  {
    if($type=='joomla')
    {
        $return = Model::updateJoomla($post);
    }else{
       $return = Model::updateK2($post);
    }

  }

  echo json_encode($return);
  JFactory::getApplication()->close();

}



private static function view($layout, $model, $data="")
{

  $layout = new JLayoutFile($layout, $basePath = JPATH_ADMINISTRATOR .'/components/com_next4seo/views');

  return $layout->render(['model'=>$model,'data'=>$data]);

}



}
