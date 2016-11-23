<?php

defined('_JEXEC') or die;

JLoader::register('Controller', JPATH_COMPONENT . '/Controller.php');

$jinput = JFactory::getApplication()->input;

Controller::getView($jinput->get('view',''));
