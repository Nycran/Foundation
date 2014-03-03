<?php
$rootDir = dirname(__FILE__);

if ($handle = opendir('.'))
{
    echo '<ol>';
    while (false !== ($file = readdir($handle)))
    {
        if (!in_array($file, array('.', '..', 'index.php')) AND !is_dir($file))
        {
            echo '<li><a href="'.$file.'">'.$file.'</a></li>';
        }
    }
    echo '</ol>';
    closedir($handle);
}
