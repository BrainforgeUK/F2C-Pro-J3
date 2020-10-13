<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cEventArgsredirect implements F2cEventArgsredirectinterface
{
    private $task = '';
    private $editMode = -1;

    public function setTask($task)
    {
        $this->task = $task;
        return $this;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setEditMode($editMode)
    {
        $this->editMode = $editMode;
        return $this;
    }

    public function getEditMode()
    {
        return $this->editMode;
    }
 }
?>