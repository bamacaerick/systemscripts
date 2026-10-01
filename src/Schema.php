<?php

namespace SystemScripts;

/**
 * schema.org structured data as one JSON-LD @graph with stable @ids.
 *
 * Nodes: Organization|LocalBusiness|Person, WebSite, WebPage (or CollectionPage, SearchResultsPage,
 * ProfilePage), BreadcrumbList, ImageObject, BlogPosting, Person (author), FAQPage (from ss/faq blocks).
 * Extend with the `ss_schema_graph` filter.
 */
class Schema
{
    public static function boot(): void
    {
        if (Seo::external_active()) {
            return;
        }

        add_action('wp_head', [self::class, 'output'], 5);
    }

    public static function output(): void
    {
        $graph = self::graph();

        if (!$graph) {
            return;
        }

        $json = wp_json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );

        printf("<script type=\"application/ld+json\">%s</script>\n", $json); // phpcs:ignore WordPress.Security.EscapeOutput -- JSON with HEX_TAG/AMP.
    }

    public static function graph(): array
    {
        if (is_404()) {
            return [];
        }

        $nodes   = [self::publisher(), self::website()];
        $webpage = self::webpage();
        $nodes[] = $webpage;

        if ($breadcrumb = self::breadcrumb()) {
            $nodes[] = $breadcrumb;
        }

        if ($image = self::primary_image()) {
            $nodes[] = $image;
        }

        $post = is_singular() ? get_queried_object() : null;

        if ($post instanceof \WP_Post) {
            if ($article = self::article($post)) {
                $nodes[] = $article;
            }

            if ($post->post_type === 'post') {
                $nodes[] = self::person((int) $post->post_author);
            }

            foreach (self::faq_nodes($post) as $faq) {
                $nodes[] = $faq;
            }
        }

        if (is_author() && Author::archive_enabled()) {
            $nodes[] = self::person((int) get_queried_object_id());
        }

        $nodes = array_values(array_filter(array_map([self::class, 'clean'], $nodes)));

        /**
         * Filters the schema.org graph.
         *
         * @param array $nodes List of node arrays.
         */
        return apply_filters('ss_schema_graph', $nodes);
    }

    private static function id(string $fragment): string
    {
        return home_url('/#' . $fragment);
    }

    private static function page_url(): string
    {
        return Seo::canonical() ?: (is_search() ? get_search_link() : home_url('/'));
    }

    private static function page_id(): string
    {
        return self::page_url() . '#webpage';
    }

    private static function language(): string
    {
        return get_bloginfo('language');
    }

    private static function publisher(): array
    {
        $type = Customizer::get('org_type');
        $name = Customizer::get('org_name') ?: get_bloginfo('name');

        $node = [
            '@type'     => $type,
            '@id'       => self::id('organization'),
            'name'      => $name,
            'url'       => home_url('/'),
            'email'     => Customizer::get('org_email'),
            'telephone' => Customizer::get('org_phone'),
            'sameAs'    => array_values(array_filter(preg_split('/\R/', (string) Customizer::get('org_social')) ?: [])),
        ];

        $address = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => Customizer::get('org_street'),
            'addressLocality' => Customizer::get('org_city'),
            'addressRegion'   => Customizer::get('org_region'),
            'postalCode'      => Customizer::get('org_postal'),
            'addressCountry'  => Customizer::get('org_country'),
        ];

        if (count(array_filter($address)) > 1) {
            $node['address'] = $address;
        }

        if ($logo_id = (int) get_theme_mod('custom_logo')) {
            if ($src = wp_get_attachment_image_src($logo_id, 'full')) {
                $logo = [
                    '@type'      => 'ImageObject',
                    '@id'        => self::id('logo'),
                    'url'        => $src[0],
                    'contentUrl' => $src[0],
                    'width'      => (int) $src[1],
                    'height'     => (int) $src[2],
                    'caption'    => $name,
                ];

                $node[$type === 'Person' ? 'image' : 'logo'] = $logo;
            }
        }

        return $node;
    }

    private static function website(): array
    {
        $node = [
            '@type'       => 'WebSite',
            '@id'         => self::id('website'),
            'url'         => home_url('/'),
            'name'        => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'publisher'   => ['@id' => self::id('organization')],
            'inLanguage'  => self::language(),
        ];

        if (apply_filters('ss_schema_search_action', true)) {
            $node['potentialAction'] = [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => home_url('/?s={search_term_string}')],
                'query-input' => 'required name=search_term_string',
            ];
        }

        return $node;
    }

    private static function webpage_type(): string
    {
        $type = 'WebPage';

        if (is_search()) {
            $type = 'SearchResultsPage';
        } elseif (is_author()) {
            $type = 'ProfilePage';
        } elseif (is_home() || is_archive()) {
            $type = 'CollectionPage';
        }

        return apply_filters('ss_schema_webpage_type', $type);
    }

    private static function webpage(): array
    {
        $url  = self::page_url();
        $node = [
            '@type'       => self::webpage_type(),
            '@id'         => self::page_id(),
            'url'         => $url,
            'name'        => wp_get_document_title(),
            'description' => Seo::description(),
            'isPartOf'    => ['@id' => self::id('website')],
            'inLanguage'  => self::language(),
        ];

        if (is_front_page()) {
            $node['about'] = ['@id' => self::id('organization')];
        }

        if (count(Breadcrumbs::trail()) > 1) {
            $node['breadcrumb'] = ['@id' => $url . '#breadcrumb'];
        }

        if (self::primary_image()) {
            $node['primaryImageOfPage'] = ['@id' => $url . '#primaryimage'];
            $node['image']              = ['@id' => $url . '#primaryimage'];
        }

        if (is_singular()) {
            $id                    = (int) get_queried_object_id();
            $node['datePublished'] = get_the_date('c', $id);
            $node['dateModified']  = get_the_modified_date('c', $id);
            $node['potentialAction'] = [['@type' => 'ReadAction', 'target' => [$url]]];
        }

        if (is_author()) {
            $node['mainEntity'] = ['@id' => self::person_id((int) get_queried_object_id())];
        }

        return $node;
    }

    private static function breadcrumb(): ?array
    {
        $trail = Breadcrumbs::trail();

        if (count($trail) < 2) {
            return null;
        }

        $items = [];

        foreach ($trail as $i => $crumb) {
            $item = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['label']];

            // The last crumb is the current page; Google accepts it with or without an item URL.
            $item['item'] = $crumb['url'] ?: self::page_url();
            $items[]      = $item;
        }

        return [
            '@type'           => 'BreadcrumbList',
            '@id'             => self::page_url() . '#breadcrumb',
            'itemListElement' => $items,
        ];
    }

    private static function primary_image(): ?array
    {
        $image = Seo::image();

        if (!$image) {
            return null;
        }

        return [
            '@type'      => 'ImageObject',
            '@id'        => self::page_url() . '#primaryimage',
            'url'        => $image['url'],
            'contentUrl' => $image['url'],
            'width'      => $image['width'],
            'height'     => $image['height'],
            'caption'    => $image['alt'],
        ];
    }

    private static function article(\WP_Post $post): ?array
    {
        $types = apply_filters('ss_schema_article_types', ['post' => 'BlogPosting']);

        if (!isset($types[$post->post_type])) {
            return null;
        }

        $url        = self::page_url();
        $categories = get_the_category($post->ID);
        $tags       = wp_get_post_tags($post->ID, ['fields' => 'names']);
        $text       = trim(wp_strip_all_tags(strip_shortcodes($post->post_content)));

        $node = [
            '@type'            => $types[$post->post_type],
            '@id'              => $url . '#article',
            'headline'         => get_the_title($post),
            'description'      => Seo::description(),
            'datePublished'    => get_the_date('c', $post),
            'dateModified'     => get_the_modified_date('c', $post),
            'mainEntityOfPage' => ['@id' => self::page_id()],
            'isPartOf'         => ['@id' => self::page_id()],
            'author'           => ['@id' => self::person_id((int) $post->post_author)],
            'publisher'        => ['@id' => self::id('organization')],
            'inLanguage'       => self::language(),
            'wordCount'        => $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []),
            'commentCount'     => (int) get_comments_number($post),
            'articleSection'   => $categories ? $categories[0]->name : '',
            'keywords'         => is_array($tags) ? implode(', ', $tags) : '',
        ];

        if (self::primary_image()) {
            $node['image'] = ['@id' => $url . '#primaryimage'];
        }

        return $node;
    }

    private static function person_id(int $user_id): string
    {
        // Hashed so the @id does not expose the numeric user ID.
        return self::id('/schema/person/' . substr(md5(wp_salt() . $user_id), 0, 16));
    }

    private static function person(int $user_id): array
    {
        $author = Author::data($user_id);
        $node   = [
            '@type'       => 'Person',
            '@id'         => self::person_id($user_id),
            'name'        => $author['name'],
            'url'         => $author['url'] ?: null,
            'description' => $author['description'],
            'jobTitle'    => $author['job_title'],
            'knowsAbout'  => $author['knows_about'],
            'sameAs'      => $author['same_as'],
        ];

        if ($avatar = get_avatar_url($user_id, ['size' => 96])) {
            $node['image'] = ['@type' => 'ImageObject', 'url' => $avatar, 'contentUrl' => $avatar, 'caption' => $author['name']];
        }

        return $node;
    }

    /**
     * FAQPage nodes from ss/faq blocks in the post content.
     */
    private static function faq_nodes(\WP_Post $post): array
    {
        if (!has_block('ss/faq', $post)) {
            return [];
        }

        $questions = [];

        foreach (self::find_blocks(parse_blocks($post->post_content), 'ss/faq') as $block) {
            foreach ((array) ($block['attrs']['items'] ?? []) as $item) {
                $question = trim((string) ($item['question'] ?? ''));
                $answer   = trim(wp_strip_all_tags((string) ($item['answer'] ?? '')));

                if ($question !== '' && $answer !== '') {
                    $questions[] = [
                        '@type'          => 'Question',
                        'name'           => $question,
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
                    ];
                }
            }
        }

        if (!$questions) {
            return [];
        }

        return [[
            '@type'      => 'FAQPage',
            '@id'        => self::page_url() . '#faq',
            'url'        => self::page_url(),
            'isPartOf'   => ['@id' => self::page_id()],
            'mainEntity' => $questions,
        ]];
    }

    private static function find_blocks(array $blocks, string $name): array
    {
        $found = [];

        foreach ($blocks as $block) {
            if (($block['blockName'] ?? '') === $name) {
                $found[] = $block;
            }

            if (!empty($block['innerBlocks'])) {
                $found = array_merge($found, self::find_blocks($block['innerBlocks'], $name));
            }
        }

        return $found;
    }

    /**
     * Decode entities in strings and drop empty values (recursively) so the output has no "": "" noise.
     */
    private static function clean($value)
    {
        if (is_string($value)) {
            // WordPress texturizes titles to HTML entities (&#8211;); JSON-LD wants plain text.
            return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (!is_array($value)) {
            return $value;
        }

        $out = [];

        foreach ($value as $key => $item) {
            $item = self::clean($item);

            if ($item === '' || $item === null || $item === []) {
                continue;
            }

            $out[$key] = $item;
        }

        return $out;
    }
}
