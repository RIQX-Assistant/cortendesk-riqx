{{-- RIQX: favicon and stylesheets, left-to-right or right-to-left by language. --}}
@php($rdRtl = \App\Support\Brand::isRtl() ? '.rtl' : '')
<link rel="icon" type="image/png" href="{{ \App\Support\Brand::url('favicon.png') }}">
<link rel="shortcut icon" href="{{ \App\Support\Brand::url('favicon.ico') }}">
<link href="{{ \App\Support\Asset::url('assets/vendor/bootstrap/bootstrap'.$rdRtl.'.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ \App\Support\Asset::url('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ \App\Support\Asset::url('assets/fonts/figtree/figtree.css') }}" rel="stylesheet" type="text/css"/>
@if ($rdRtl)
<link href="{{ \App\Support\Asset::url('assets/fonts/vazirmatn/vazirmatn.css') }}" rel="stylesheet" type="text/css"/>
@endif
<link href="{{ \App\Support\Asset::url('assets/css/spacing'.$rdRtl.'.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ \App\Support\Asset::url('assets/css/shell'.$rdRtl.'.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ \App\Support\Asset::url('assets/css/cortendesk'.$rdRtl.'.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ \App\Support\Asset::url('assets/css/riqx.css') }}" rel="stylesheet" type="text/css"/>
