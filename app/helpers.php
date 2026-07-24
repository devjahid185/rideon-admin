<?php

use Illuminate\Pagination\LengthAwarePaginator;

function shortenPropertyName($propertyName, $maxLength = 50, $suffix = '...')
{
    if (strlen($propertyName) <= $maxLength) {
        return $propertyName;
    }

    return substr($propertyName, 0, $maxLength - strlen($suffix)).$suffix;
}

if (! function_exists('maskEmail')) {
    function maskEmail($email)
    {
        $emailParts = explode('@', $email);
        $username = $emailParts[0];
        $domain = $emailParts[1];
        if (strlen($username) > 6) {
            $maskedUsername = substr($username, 0, 5).str_repeat('*', strlen($username) - 4).substr($username, -2);
        } else {
            $maskedUsername = $username;
        }
        if (strlen($maskedUsername) > 10) {
            $maskedUsername = substr($maskedUsername, 0, 8).'...';
        }
        $maskedDomain = strlen($domain) > 15 ? '...'.substr($domain, -13) : $domain;

        return $maskedUsername.'@'.$maskedDomain;
    }
}

if (! function_exists('maskPhone')) {
    function maskPhone($phone)
    {
        return $phone ? substr($phone, 0, -6).str_repeat('*', 6) : '';
    }
}

function formatCurrency($number, $decimals = 2, $decimal_separator = '.', $thousands_separator = ',', $forDb = false)
{
    $number = $number ?? 0;
    if ($number == 0) {
        return '0.00';
    }

    if ($forDb) {
        // Return raw numeric value for DB usage, ensuring it's still rounded consistently
        return number_format((float) $number, $decimals);
    }

    $locale = Config::get('general.default_locale_currency') ?? 'en-US';

    if (class_exists('NumberFormatter')) {
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);

        return $formatter->format($number);
    }

    return number_format($number, $decimals, $decimal_separator, $thousands_separator);
}
function formatCurrencyForDb($number, $forDb = false)
{
    return formatCurrency($number, 2, '.', ',', $forDb);
}

function installerExists()
{
    return file_exists(app_path('Http/Controllers/InstallerController.php'));
}

if (! function_exists('admin_pagination')) {
    function admin_pagination(LengthAwarePaginator $p, int $range = 2)
    {
        $current = $p->currentPage();
        $last = $p->lastPage();

        if ($last <= 1) {
            return '';
        }

        $start = max(1, $current - $range);
        $end = min($last, $current + $range);

        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-end">';

        $html .= '<li class="page-item '.($p->onFirstPage() ? 'disabled' : '').'">';
        $html .= '<a class="page-link" href="'.$p->previousPageUrl().'">'.trans('global.previous').'</a></li>';

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="'.$p->url(1).'">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $html .= '<li class="page-item '.($i == $current ? 'active' : '').'">';
            $html .= '<a class="page-link" href="'.$p->url($i).'">'.$i.'</a></li>';
        }

        if ($end < $last) {
            if ($end < $last - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="'.$p->url($last).'">'.$last.'</a></li>';
        }

        $html .= '<li class="page-item '.($p->hasMorePages() ? '' : 'disabled').'">';
        $html .= '<a class="page-link" href="'.$p->nextPageUrl().'">'.trans('global.next').'</a></li>';

        return $html.'</ul></nav>';
    }
}
