<?php

namespace Recca0120\Terminal;

use Closure;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleStyle
{
    public static function bufferedOutput(Closure $handle)
    {
        $bufferedOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
        $handle($bufferedOutput);

        return $bufferedOutput->fetch();
    }

    public static function table($rows, $headers = [])
    {
        return static::bufferedOutput(function ($bufferedOutput) use ($rows, $headers) {
            $table = new Table($bufferedOutput);
            $table
                ->setHeaders($headers)
                ->setRows($rows)
                ->setStyle('default')
                ->render();
        });
    }

    public static function applyFormat($text)
    {
        return with(new OutputFormatter(true))->format($text);
    }

    private static function apply($text, $tagName)
    {
        return '<'.$tagName.'>'.$text.'</'.$tagName.'>';
    }

    public static function error($text)
    {
        return $this->apply($text, __FUNCTION__);
    }

    public static function info($text)
    {
        return $this->apply($text, __FUNCTION__);
    }

    public static function comment($text)
    {
        return $this->apply($text, __FUNCTION__);
    }

    public static function question($text)
    {
        return $this->apply($text, __FUNCTION__);
    }
}
