<?php

namespace Recca0120\Terminal;

use Closure;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleStyle
{
    protected static $outputFormat = null;

    public static function bufferedOutput(Closure $handle)
    {
        $bufferedOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, static::getOutputFormat());
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

    public static function getOutputFormat()
    {
        if (static::$outputFormat === null) {
            static::$outputFormat = new OutputFormatter(true);
        }

        return static::$outputFormat;
    }

    public static function applyFormat($text, $tagName)
    {
        return static::getOutputFormat()->format('<'.$tagName.'>'.$text.'</'.$tagName.'>');
    }

    public static function error($text)
    {
        return static::applyFormat($text, __FUNCTION__);
    }

    public static function info($text)
    {
        return static::applyFormat($text, __FUNCTION__);
    }

    public static function comment($text)
    {
        return static::applyFormat($text, __FUNCTION__);
    }

    public static function question($text)
    {
        return static::applyFormat($text, __FUNCTION__);
    }
}
