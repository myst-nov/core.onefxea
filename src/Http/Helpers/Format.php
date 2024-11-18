<?php

use Carbon\Carbon;
use Intervention\Image\ImageManager;

if (! function_exists('_usd_format')) {
    /**
     * Return USD Format with $ symbol
     */
    function _usd_format($number)
    {
        return number_format($number, 0, ',', ',') . ' USD';
    }
}

if (! function_exists('_point_format')) {
    /**
     * Return number of Point formated
     */
    function _point_format($number, $decimals = 3)
    {
        return str_replace('.000', '', number_format($number, $decimals, '.', ','));
    }
}

if (! function_exists('_number_format')) {
    /**
     * Return default number format
     */
    function _number_format($number, $decimals = 0)
    {
        return number_format($number, $decimals, '.', ',');
    }
}

if (! function_exists('_datetime_format')) {
    /**
     * Return default datetime format by timestamp
     */
    function _datetime_format($date, $showTime = true)
    {
        if (is_null($date)) {
            return 'N/A';
        }
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format($showTime ? config('app.datetime_format') : str_replace(['H:i:s', ' '], '', config('app.datetime_format')));
    }
}

if (! function_exists('_timeleft')) {
    /**
     * Countdown timer
     * Ex: 5 months 4 days 3 hours 2 minutes 1 second
     */
    function _timeleft($datetime, $full = true)
    {
        $date1 = new DateTime(date('Y-m-d H:i:s'));
        $date2 = new DateTime($datetime);
        $interval = $date1->diff($date2);

        if ($date1 > $date2) {
            return 0;
        }

        $text = '';
        if ($interval->y > 0) {
            $text .= $interval->y . ($full ? ' year'.($interval->y > 1 ? 's' : '').' ' : 'yr ');
        }
        if ($interval->m > 0) {
            $text .= $interval->m . ($full ? ' month'.($interval->m > 1 ? 's' : '').' ' : 'mth ');
        }
        if ($interval->d > 0) {
            $text .= $interval->d . ($full ? ' day'.($interval->d > 1 ? 's' : '').' ' : 'd ');
        }
        if ($interval->h > 0) {
            $text .= $interval->h . ($full ? ' hour'.($interval->h > 1 ? 's' : '').' ' : 'h ');
        }
        if ($interval->i > 0) {
            $text .= $interval->i . ($full ? ' minute'.($interval->i > 1 ? 's' : '').' ' : 'm ');
        }
        if ($interval->s > 0) {
            $text .= $interval->s . ($full ? ' second'.($interval->s > 1 ? 's' : '').'' : 's');
        }
        return $text;
    }
}

if (! function_exists('_time_elapsed_string')) {
    /**
     * Time elapsed string
     * Ex: 3 weeks ago
     * Ex: 1 hour ago
     */
    function _time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (! $full) {
            $string = array_slice($string, 0, 1);
        }
        return $string ? implode(', ', $string) . ' ago' : 'Just now';
    }
}

if (! function_exists('_search_text')) {
    /**
     * Return search text
     * Ex: "iphone 14 pro max" => "%iphone%14%pro%max%"
     */
    function _search_text($text)
    {
        return '%'.str_replace(' ', '%', $text).'%';
    }
}

if (! function_exists('_url_non_protocol')) {
    /**
     * Return URL without protocol
     * Ex: https://example.com => example.com
     */
    function _url_non_protocol($url)
    {
        return str_replace(['https://', 'http://'], '', $url);
    }
}

if (! function_exists('_url_non_port')) {
    /**
     * Return URL without protocol
     * Ex: https://example.com => example.com
     */
    function _url_non_port($url)
    {
        return preg_replace('/:[0-9]+/', '', $url);
    }
}

if (! function_exists('_url_domain')) {
    /**
     * Return URL without protocol and port
     * Ex: https://example.com:8080 => example.com
     */
    function _url_domain($url)
    {
        return _url_non_protocol(_url_non_port($url));
    }
}

if (! function_exists('_shorten_url')) {
    /**
     * Make the url shorter
     */
    function _shorten_url($url, $withEnd = true)
    {
        if (strlen($url) > 50) {
            $pre = substr($url, 0, 32);
            $sup = substr($url, -12);

            return $pre . '...' . ($withEnd ? $sup : '');
        }

        return $url;
    }
}

if (! function_exists('_to_array')) {
    /**
     * Convert a collect/collection/object to array
     */
    function _to_array($mixed)
    {
        return json_decode(json_encode($mixed), true);
    }
}

if (! function_exists('_calc_percent')) {
    function _calc_percent($total, $percent)
    {
        return $total * ($percent / 100);
    }
}

if (! function_exists('_str_separation')) {
    function _str_separation($str, $distance = 3, $space = ' ')
    {
        $arrStr = str_split($str, $distance);
        $result = '';
        foreach ($arrStr as $key => $value) {
            if (in_array($space, ['u', 'underline'])) {
                if ($key % 2 != 0) {
                    $result .= "<u>$value</u>";
                } else {
                    $result .= $value;
                }
            } else {
                $result .= $value . $space;
            }
        }

        return rtrim($result, $space);
    }
}

if (! function_exists('_compress_image')) {
    function _compress_image(string $path, int $width = 300, int $height = null)
    {
        $manager = new ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver
        );

        $image = $manager->read($path);

        // resize image instance
        $image->scale($width, $height);

        // save encoded image
        $image->save($path);
    }
}
