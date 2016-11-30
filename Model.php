<?php

defined('_JEXEC') or die;


class Model {

public static $errors = [];
public $total = 0;

public function csvK2()
{
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);

  $query->select(
      'id, catid, title, alias, metakey, metadesc'
    );

  $query->where('published=1 and trash = 0');

  $query->from($db->quoteName('#__k2_items'));

  $db->setQuery($query);

  return $db->loadAssocList();

}

public function csvJoomla()
{
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);
  $query->select(
      'id,catid, title, alias, metakey,metadesc, metadata'
    );



  $query->where('state=1');

  $query->from($db->quoteName('#__content'));

  $db->setQuery($query);

  return $db->loadAssocList();

}

public function ListK2()
{

  $jinput = JFactory::getApplication()->input;
  $db = JFactory::getDbo();
  $limit = 50;
  $next = $jinput->get('next',0);
  $prev = $jinput->get('prev',0);
  $limitstart = 0;

  if($next)
  {
    $limitstart = $next;
  }

  if($prev)
  {
    $limitstart = $prev;
  }

  $query = $db->getQuery(true);
  $query->select(
      'a.id, a.catid, a.title, a.alias, a.metakey, a.metadesc, a.metadata, a.hits, b.alias as cat_alias'
);

  $query->from($db->quoteName('#__k2_items','a'))
  ->join('INNER', $db->quoteName('#__k2_categories', 'b') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('b.id') . ')');
  $query->where('a.published=1 and a.trash = 0 and b.published=1 and b.trash = 0');
  $db->setQuery($query,$limitstart, $limit);
  $resultado = $db->loadAssocList();
  $this->total = $this->total('#__k2_items');
  return $resultado;
}

public function ListContent()
{
  $jinput = JFactory::getApplication()->input;
  $db = JFactory::getDbo();
  $limit = 50;

  $next = $jinput->get('next',0);
  $prev = $jinput->get('prev',0);

  $limitstart = 0;

  if($next)
  {
    $limitstart = $next;
  }

  if($prev)
  {
    $limitstart = $prev;
  }


		$query = $db->getQuery(true);
    $query->select(
        'id,catid, title, alias, metakey,metadesc, metadata, hits,language'
  );

  $query->where('state=1');

  $query->from($db->quoteName('#__content'));
  $db->setQuery($query,$limitstart, $limit);
  $resultado = $db->loadAssocList();

  $this->total = $this->total('#__content');

  return $resultado;
}

public function total($tabela)
{
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);
    $query->select(
        'count(*)'
  );
  $query->from($db->quoteName($tabela));
  if($tabela == '#__content'){
      $query->where('state=1');
  }else{
    $query->where('published=1 and trash = 0');
  }


  $db->setQuery($query);

  $count = $db->loadResult();

  return $count;

}


public static function checkAlias($alias, $id, $type)
{

  $resultado = 0;

  if($type == 'k2')
  {

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select(
        'count(*)'
  );
    $query->from($db->quoteName('#__k2_items'));
    $query->where('alias = '.$db->quote($alias).' and id <> '.$id);
    $db->setQuery($query);

    $resultado = $db->loadResult();

  }

  if($type == 'joomla')
  {

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select(
        'count(*)'
  );
    $query->from($db->quoteName('#__content'));
    $query->where('alias = '.$db->quote($alias).' and id <> '.$id);
    $db->setQuery($query);

    $resultado = $db->loadResult();

  }

  return $resultado;

}


public static function updateJoomla($post)
{
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);

  if(Model::Validate($post))
  {
    $dados = Model::MontarSave($post);

    // verifico se o alias existe
    if(!Model::checkAlias($dados['alias'],$dados['id'],'joomla'))
    {

      $fields = [
         $db->quoteName('title') . ' = ' . $db->quote($dados['title']),
         $db->quoteName('alias') . ' = ' . $db->quote($dados['alias']),
         $db->quoteName('metakey') . ' = ' . $db->quote($dados['metakey']),
         $db->quoteName('metadesc') . ' = ' . $db->quote($dados['metadesc']),
      ];

      $query->update($db->quoteName('#__content'))->set($fields)->where($db->quoteName('id') . ' = '.$dados['id']);

      $db->setQuery($query);

      $result = $db->execute();

      if($result)
      {
        return ['type'=>'success', 'msg'=>'Artigo salvo com sucesso', 'alias'=>$dados['alias']];
      }else{
        return ['type'=>'danger', 'msg'=>'Erro no processo de salvar:'.print_r($result,true)];
      }

    }else{ //fim do check alias
        return ['type'=>'danger', 'msg'=>'<p>Este alias já existe</p>'];
    } //fim do erro check alias

  }else{
    return ['type'=>'danger', 'msg'=>'<p>Foi detectado os seguintes erros</p>'.Model::CapturaError()];
  }

}

public static function updateK2($post)
{
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);

  if(Model::Validate($post))
  {
    $dados = Model::MontarSave($post);


    if(!Model::checkAlias($dados['alias'],$dados['id'],'k2'))
    {
      $fields = [
         $db->quoteName('title') . ' = ' . $db->quote($dados['title']),
         $db->quoteName('alias') . ' = ' . $db->quote($dados['alias']),
         $db->quoteName('metakey') . ' = ' . $db->quote($dados['metakey']),
         $db->quoteName('metadesc') . ' = ' . $db->quote($dados['metadesc']),
      ];

      $query->update($db->quoteName('#__k2_items'))->set($fields)->where($db->quoteName('id') . ' = '.$dados['id']);

      $db->setQuery($query);

      $result = $db->execute();

      if($result)
      {
        return ['type'=>'success', 'msg'=>'Item do k2 salvo com sucesso', 'alias'=>$dados['alias']];
      }else{
        return ['type'=>'danger', 'msg'=>'Erro no processo de salvar:'.print_r($result,true)];
      }

    }else{
      return ['type'=>'danger', 'msg'=>'<p>Este alias já existe</p>'];
    }



  }else{
    return ['type'=>'danger', 'msg'=>'<p>Foi detectado os seguintes erros:</p>'.Model::CapturaError()];
  }

}

public static function MontarSave($post)
{
  $model = [];
  $model['id'] = (int)$post['id'];
  $model['title'] = $post['title'];
  if(empty($post['alias']))
  {
    $model['alias'] =  JFilterOutput::stringURLSafe($post['title']);
  }else{
    $model['alias'] =  JFilterOutput::stringURLSafe($post['alias']);
  }

  $model['metakey'] = $post['metakey'];
  $model['metadesc'] = $post['metadesc'];

  return $model;

}

public static function Validate($post)
{
  if(empty($post['title']))
  {
    self::$errors[] = 'O titulo não pode estar vazio';
    return false;
  }else{
    return true;
  }
}

public static function CapturaError()
{
  $errors = "<ul class='unstyled'>";
  foreach (self::$errors as $key => $error) {
    $errors .= "<li>{$error}</li>";
  }
  $errors .= "</ul>";

  return $errors;
}


}
