<?php

if (!function_exists('try_catch_null')) {
    function try_catch_null($callback) {
        try {
            return $callback();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}