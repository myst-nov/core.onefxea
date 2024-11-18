<?php

use Illuminate\Support\Str;

if (! function_exists('_generate_referral_code')) {
    /**
     * Generate referral_code
     */
    function _generate_referral_code()
    {
        return Str::upper(Str::random(6));
    }
}

if (! function_exists('_generate_referral_link')) {
    /**
     * Generate referral_link
     */
    function _generate_referral_link()
    {
        return Str::random(17);
    }
}

if (! function_exists('_get_referral_full_link')) {
    /**
     * Get referral link
     */
    function _get_referral_full_link($referralCodeLink)
    {
        return route('referral', ['presenter' => $referralCodeLink]);
    }
}

if (! function_exists('_main_route')) {
    /**
     * Return Admin Site URL
     */
    function _main_route($routeName, $seq = null)
    {
        $mainUrl = config('define.main_url')[$routeName];
        $includeSeqUrl = str_replace('{seq}', $seq, $mainUrl);

        return url(config('app.main_url') . '/' . $includeSeqUrl);
    }
}

if (! function_exists('_generate_wallet_id')) {
    /**
     * Generate referral_link
     */
    function _generate_wallet_id($memberId)
    {
        $pad = str_pad($memberId, 6, '0', STR_PAD_LEFT);
        return 1 . $pad;
    }
}

if (! function_exists('_shortcode')) {
    /**
     * Return template text by shortcode
     */
    function _shortcode($collection, $shortcode)
    {
        return $collection->where('shortcode', $shortcode)->first()->content ?? '';
    }
}
