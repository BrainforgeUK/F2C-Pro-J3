<?php
/**
 * Smarty write file plugin
 *
 * @package Smarty
 * @subpackage PluginsInternal
 * @author Monte Ohrt
 */

/**
 * Smarty Internal Write File Class
 *
 * @package Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Write_File {

    /**
     * Writes file in a safe way to disk
     *
     * @param string $_filepath complete filepath
     * @param string $_contents file content
     * @param Smarty $smarty    smarty instance
     * @return boolean true
     */
    public static function writeFile($_filepath, $_contents, Smarty $smarty)
    {
		/*
		 * Code modification to make the write function work with the Joomla system.
		 * The default Smarty code would fail when the FTP layer was enabled.
		 */
		jimport('joomla.user.helper');
		jimport('joomla.filesystem.file');
	
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
        if ($smarty->_file_perms !== null) {
            $old_umask = umask(0);
        }

        $_dirpath = dirname($_filepath);
        // if subdirs, create dir structure
        if ($_dirpath !== '.' && !file_exists($_dirpath)) {
            mkdir($_dirpath, $smarty->_dir_perms === null ? 0777 : $smarty->_dir_perms, true);
        }

		$_tmp_file = $_dirpath.DIRECTORY_SEPARATOR.md5(JUserHelper::genRandomPassword(16));

		// Added Brainforge.uk 20250509
	    $_contents = str_replace(']->value;?>', ']->value ?? \'\';?>', $_contents);
		
		if(!JFile::write($_tmp_file, $_contents))
		{
			throw new SmartyException('Smarty template engine could not write file \''.$_tmp_file.'\'');
		}

		if(JFile::exists($_filepath))
		{
			if(!JFile::delete($_filepath))
			{
				throw new Exception('Smarty template engine could not delete file \''.$_filepath.'\'');
			}
		}

		if(!JFile::move($_tmp_file, $_filepath))
		{
			throw new Exception('Smarty template engine could not move file \''.$_tmp_file.'\' to file \''.$_filepath.'\'');
		}

		/*
        // write to tmp file, then move to overt file lock race condition
        $_tmp_file = $_dirpath . DS . uniqid('wrt', true);
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_tmp_file}");
            return false;
        }
        */
		
        /*
         * Windows' rename() fails if the destination exists,
         * Linux' rename() properly handles the overwrite.
         * Simply unlink()ing a file might cause other processes 
         * currently reading that file to fail, but linux' rename()
         * seems to be smart enough to handle that for us.
         */
		 /*
        if (Smarty::$_IS_WINDOWS) {
            // remove original file
            @unlink($_filepath);
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
        } else {
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
            if (!$success) {
                // remove original file
                @unlink($_filepath);
                // rename tmp file
                $success = @rename($_tmp_file, $_filepath);
            }
        }
		
        if (!$success) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_filepath}");
            return false;
        }
		*/
		
        if ($smarty->_file_perms !== null) {
            // set file permissions
            chmod($_filepath, $smarty->_file_perms);
            umask($old_umask);
        }
        error_reporting($_error_reporting);
        return true;
    }

}

?>