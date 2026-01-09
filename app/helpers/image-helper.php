<?php

if (!function_exists('getProductImagePath')) {
    function getProductImagePath($imageName) {
        // Tr\u1ea3 v\u1ec1 tr\u1ed1ng n\u1ebfu kh\u00f4ng c\u00f3 t\u00ean \u1ea3nh
        if (empty($imageName)) {
            return '';
        }
    
        return $imageName;
    }
}
