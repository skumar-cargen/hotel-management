<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;

class PageController extends Controller
{
    use ApiResponses;

    private const PAGES = [
        'about-us' => [
            'content' => 'about_us',
            'meta_title' => 'about_us_meta_title',
            'meta_description' => 'about_us_meta_description',
            'canonical_url' => 'about_us_canonical_url',
        ],
        'privacy-policy' => [
            'content' => 'privacy_policy',
            'meta_title' => 'privacy_policy_meta_title',
            'meta_description' => 'privacy_policy_meta_description',
            'canonical_url' => 'privacy_policy_canonical_url',
        ],
        'terms-conditions' => [
            'content' => 'terms_conditions',
            'meta_title' => 'terms_conditions_meta_title',
            'meta_description' => 'terms_conditions_meta_description',
            'canonical_url' => 'terms_conditions_canonical_url',
        ],
    ];

    public function show(string $slug)
    {
        $domain = $this->domain();

        $page = self::PAGES[$slug] ?? null;

        if (! $page) {
            return $this->errorResponse('Page not found.', 404);
        }

        return $this->successResponse([
            'content' => $domain->{$page['content']},
            'meta_title' => $domain->{$page['meta_title']},
            'meta_description' => $domain->{$page['meta_description']},
            'canonical_url' => $domain->{$page['canonical_url']},
        ]);
    }
}
