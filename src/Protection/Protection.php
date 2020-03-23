<?php

namespace SDU\MFA\Protection;

class Protection
{
    /**
     * @var array
     */
    private $whitelist;

    /**
     * @var
     */
    private $filters = [];

    public function whitelist(string $path)
    {
        $this->whitelist[] = trim($path);

        return $this;
    }

    public function filter($path, Filter $filter)
    {
        $this->filters[$path] = $filter;

        return $this;
    }

    /**
     * @return Filter|null
     */
    public function matchesPath()
    {
        foreach ($this->whitelist as $whitelistItem)
        {
            if ($this->matchPath($whitelistItem))
                return null; //matches whitelist, we tell the filter ot ignore.
        }

        foreach ($this->filters as $path => $filter)
        {
            /** @var Filter $filter */
            if ($this->matchPath($path))
                return $filter;

        }
        return null;
    }

    /**
     * @param $path string url path
     * @return bool returns true if that pattern matches the current url, otherwise false
     */
    private function matchPath($path)
    {
        $accessing = $this->stripForwardSlash($_SERVER['PHP_SELF']);
        $regex = $this->regexFilterPath($this->stripForwardSlash($path));
        return preg_match("#^$regex$#", $accessing) === 1;
    }

    /**
     * @param $string String input
     * @return string returns the string with the first forward slash removed, if any
     */
    private function stripForwardSlash($string)
    {
        if (strpos($string, '/') === 0)
            return substr($string, 1);
        return $string;
    }

    /**
     * @param $path String input path with wildcards (*)
     * @return string returns the path with wildcards replaced with the appropriate regex
     */
    private function regexFilterPath($path)
    {
        $path = explode('*', $path);
        if (count($path) == 1)
            return $path[0];
        $regex = "";
        for ($i = 0; $i < count($path); $i++)
        {
            if ($i == count($path) - 1 && $path != '')
            {
                $regex .= $path[$i];
                break;
            }
            $regex .= $path[$i];
            $regex .= $i == count($path) - 2 ? '[a-zA-Z0-9\.\-\_\/?]*' : '([a-zA-Z0-9\.\-\_]\/?)+';
        }
        return $regex;
    }
}