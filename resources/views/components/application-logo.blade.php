@php
    $logo = asset('/images/logo-1.png');
    $width = isset($width) && !empty($width) ? $width : '' ;
    $height = isset($height) && !empty($height) ? $height : '' ;
@endphp 

<img src="{{ $logo }}" alt="logo img" width="{{ $width }}" height="{{ $height }}">
