<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

interface F2cEventArgsredirectinterface
{
    public function getTask();
    public function setTask($task);
    public function getEditMode();
    public function setEditMode($editMode);
}