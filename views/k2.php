<?php
defined('_JEXEC') or die;
$com_path = JPATH_SITE . '/components/com_k2/';
require_once $com_path . 'helpers/route.php';


$pagination = $displayData['data']['pagination'];
 ?>
 <div>
   <div class="row-fluid show-grid">
     <div class="span6 offset5">
       <div class="btn-toolbar">
         <div class="btn-group">
           <a class="btn btn-large <?=($pagination['urlprev'])?'':'disabled'?>" href="<?=($pagination['urlprev'])?$pagination['urlprev']:'#'?>"><i class="icon-previous"></i> Anterior</a>
          <a class="btn btn-large disabled" href="#">Pag <?=$pagination['page']?> - Total <?=$pagination['total_inpage']?> de <?=$pagination['total']?></a>
           <a class="btn btn-large <?=($pagination['urlnext'])?'':'disabled'?>" href="<?=($pagination['urlnext'])?$pagination['urlnext']:'#'?>">Proximo<i class="icon-next"></i></a>
         </div>
       </div>
     </div>

   </div>
   <table id="conteudo-seo" class="table table-striped" style="width:100%">
     <thead>
       <tr>
         <th width="1%" >
            <strong>ID</strong>
         </th>
         <th>
            <strong>Title</strong>
         </th>
         <th>
            <strong>Alias</strong>
         </th>
         <th>
            <strong>Metadesc</strong>
         </th>
         <th>
            <strong>Metakey</strong>
         </th>

         <th width="2%">
            <strong>Hits</strong>
         </th>
         <th>
            <strong>Ações</strong>
         </th>
       </tr>
     </thead>
    <tbody>
      <?php foreach ($displayData['model'] as $k => $column): ?>
        <tr id="rowtable_<?=$column['id']?>">
          <td width="1%" >
            <?=$column['id']?>
            <input type="hidden" name="nextform[id]" value="<?=$column['id']?>" class="form-control" />
          </td>
          <td>
            <input type="text" name="nextform[title]" style="width:90%" value="<?=$column['title']?>" class="form-control" />
            <?php
            $root = JURI::root();
            $urlmont = urldecode(JRoute::_(K2HelperRoute::getItemRoute($column['id'].':'.urlencode($column['alias']), $column['catid'].':'.urlencode($column['cat_alias']))));
            //$url = $root.'index.php?option=com_k2&view=item&layout=item&id='.$column['id'];
            $url = str_replace('/administrator',NULL,$urlmont);
             ?>
            <a target="_blank" style="display:block; width:100%" href="<?=$url?>"><?=$column['title']?></a>
          </td>
          <td>
            <input type="text" name="nextform[alias]" style="width:90%" value="<?=$column['alias']?>" class="form-control setalias" />

          </td>
          <td>
            <textarea name="nextform[metadesc]" style="width:90%; height:90px;" class="form-control" ><?=$column['metadesc']?></textarea>
          </td>
          <td>
            <input type="text" name="nextform[metakey]" style="width:90%" value="<?=$column['metakey']?>" class="form-control" />
          </td>

          <td width="2%">
            <span class="label label-primary"><?=$column['hits']?></span>
          </td>
          <td>
            <button type="button" data-nextsubmit="#rowtable_<?=$column['id']?>" class="btn btn-primary btn-xs" name="button">Salvar</button>
          </td>
        </tr>

      <?php endforeach; ?>
    </tbody>

   </table>


 </div>


 <!-- Modal -->
 <div id="alertas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
     <h3 id="myModalLabel">Salvar Artigo k2</h3>
   </div>
   <div class="modal-body">
     <p>A respota não é json, verificar no log do brownser <code>CTRL+12</code> em console. </p>
   </div>
   <div class="modal-footer">
     <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
   </div>
 </div>
 <!-- modal -->
<div class="row-fluid show-grid">
  <div class="span6 offset5">
    <div class="btn-toolbar">
      <div class="btn-group">

        <a class="btn btn-large <?=($pagination['urlprev'])?'':'disabled'?>" href="<?=($pagination['urlprev'])?$pagination['urlprev']:'#'?>"><i class="icon-previous"></i> Anterior</a>
        <a class="btn btn-large disabled" href="#">Pag <?=$pagination['page']?> - Total <?=$pagination['total_inpage']?> de <?=$pagination['total']?></a>
        <a class="btn btn-large" href="<?=($pagination['urlnext'])?$pagination['urlnext']:'#'?>">Proximo<i class="icon-next"></i></a>
      </div>
    </div>
  </div>

</div>

 <script type="text/javascript">
   jQuery(function($){
     $('header.header .container-title').html('<h1 class="page-title"><?=$displayData['data']['title']?></h1>');

     $('.row-fluid #toolbar').html('<a href="index.php?option=com_next4seo&view=csv&action=k2" class="btn btn-small btn-success"><i class="fa fa-save"></fa> Exportar em CSV </a>');

     $('[data-nextsubmit]').on('click',function(e){
       e.preventDefault();
       $botao = $(this);
       if(!$botao.hasClass('disabled'))
       {
       var row = $($botao.data('nextsubmit'));

       datafields = row.children('td').children('.form-control').serializeArray();

       $.ajax({
         url:'index.php?option=com_next4seo&view=k2&action=ajaxsend',
         method:'POST',
         data:datafields,
         dataType:'JSON',
         beforeSend:function()
         {
           $('#conteudo-seo tr td button, #conteudo-seo tr td input, #conteudo-seo tr td textarea').each(function(i,e){
             $(this).addClass('disabled');
           });
         },
         success:function(data)
         {

           if(typeof data == 'object')
           {
             $('#alertas .modal-body p').html(data.msg);

             if(data.type == 'success')
             {
               row.children('td').children('.setalias').val(data.alias);
             }

           }else{
              console.log(data);
           }

           $('#alertas').modal('show');

           $('#conteudo-seo tr td button, #conteudo-seo tr td input, #conteudo-seo tr td textarea').each(function(i,e){
             $(this).removeClass('disabled');

           });
         }

       });



       } // if disabled

     });

   });
 </script>
