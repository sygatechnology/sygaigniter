<?php

if(! function_exists('get_locale')){
    /**
     * Retrieves the current locale.
     * @global string $locale
     * @global string $wp_local_package
     *
     * @return string The locale of the blog or from the {@see 'locale'} hook.
     */
    function get_locale() {
        return \Config\Services::options()->get( 'SYGALANG', 'fr_FR' );
    }
  }
