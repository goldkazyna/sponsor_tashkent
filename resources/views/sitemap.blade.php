{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($static as $page)
    <url>
        <loc>{{ $baseUrl . $page['url'] }}</loc>
        <changefreq>{{ $page['changefreq'] }}</changefreq>
        <priority>{{ $page['priority'] }}</priority>
    </url>
@endforeach
@foreach($cities as $city)
    <url>
        <loc>{{ $baseUrl . '/?city=' . urlencode($city) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
@foreach($posts as $post)
    <url>
        <loc>{{ $baseUrl . '/post/detail/' . $post->id }}</loc>
        <lastmod>{{ \Carbon\Carbon::parse($post->date)->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>
