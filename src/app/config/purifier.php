<?php
return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'preload'  => false,
    'cachePath' => storage_path('app/purifier'),
    'settings' => [
        'default' => [
            'HTML.Doctype'             => 'XHTML 1.0 Transitional',
            'HTML.Allowed'             => 'p,b,strong,i,em,u,a[href|title],ul,ol,li,br,span[style],div[style,class],img[width|height|alt|src],h1,h2,h3,h4,h5,h6,blockquote,pre,code',
            'HTML.AllowedAttributes'    => 'a.href,a.title,img.src,img.width,img.height,img.alt,span.style,div.style,div.class',
            'URI.AllowedSchemes'        => ['http' => true, 'https' => true, 'mailto' => true, 'ftp' => true, 'nntp' => true, 'news' => true, 'tel' => true, 'data' => true],
            'AutoFormat.RemoveEmpty'    => true,
            'AutoFormat.RemoveSpansWithoutAttributes' => true,
        ],
    ],
];