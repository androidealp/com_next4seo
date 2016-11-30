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

  switch ($view) {
    case 'k2':
      return Controller::K2Manager($action);
      break;
    case 'csv':
        return Controller::CSVManager($action);
        break;
    default:
      return Controller::JoomlaManager($action);
      break;
  }



}

private static function CSVexec($action, $lista)
{

  $periodo = date('d-m-Y_h-i-s');

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=dados-'.$action.'-'.$periodo.'.csv');

  $output = fopen('php://output', 'w');

  if($action == 'joomla')
  {
    $dados = [
      'id',
      'catid',
      'title',
      'alias',
      'metakey',
      'metadesc',
      'metadata'
    ];
  }else{
    $dados = [
      'id',
      'catid',
      'title',
      'alias',
      'metakey',
      'metadesc'
    ];
  }

  fputcsv($output, $dados);

  foreach ($lista as $linha) {
    fputcsv($output, $linha);
  }

  fclose($output);

  JFactory::getApplication()->close();

}

private static function CSVManager($action)
{

  $model = new Model;
  $lista = [];
  if($action == 'joomla')
  {
      $lista = $model->csvJoomla();
  }

  if($action == 'k2')
  {
      $lista = $model->csvK2();
  }

  Controller::CSVexec($action,$lista);

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
