<?php

    	$filename = ((isset($argv[1]))? $argv[1] : NULL);
    	$data = ((isset($argv[2]))? $argv[2] : NULL);
        $cmd = 'nohup php -q ' . __DIR__.'/'.$filename.'.php '.$data;
    	$pidfile = __DIR__.'/../../jda/logs/pidfile.log';

    	$source=$filename . "_" . date('m_d_y');
    	$outputfile = __DIR__.'/../../jda/logs/'.$source.'.log';
        exec(sprintf("%s >> %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
        chmod($outputfile,0777);
        // exec($cmd . " </dev/null 2> /dev/null & echo $!");
        // exec($cmd . " > /dev/null &");