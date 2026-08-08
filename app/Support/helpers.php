<?php

if (! function_exists('rupiah')) {
    function rupiah(float|int|string|null $amount): string
    {
        $amount = (float) ($amount ?? 0);

        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
