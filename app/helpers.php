<?php

if ( ! function_exists( 'array_get' ) ) {
    /**
     * Get a value from an array using "dot" notation.
     *
     * This function retrieves a value from a nested array using a dot-separated key path.
     * If the path does not exist within the array, it returns a default value.
     *
     * @param   array   $array    An array from which we want to retrieve some information.
     * @param   string  $key      A string separated by . of keys describing the path with which to retrieve information.
     * @param   mixed   $default  Optional. The return value if the path does not exist within the array.
     *
     * @return mixed
     */
    function array_get( $array, $key, $default = null ): mixed {
        if ( ! ( is_array( $array ) || $array instanceof ArrayAccess ) ) {
            return $default instanceof Closure ? $default() : $default;
        }

        if ( is_null( $key ) ) {
            return $array;
        }

        if ( array_key_exists( $key, $array ) ) {
            return $array[ $key ];
        }

        if ( strpos( $key, '.' ) === false ) {
            return $array[ $key ] ?? ( $default instanceof Closure ? $default() : $default );
        }

        foreach ( explode( '.', $key ) as $segment ) {
            if ( ( is_array( $array ) || $array instanceof ArrayAccess ) && ( array_key_exists( $segment, $array ) ) ) {
                $array = $array[ $segment ];
            } else {
                return $default instanceof Closure ? $default() : $default;
            }
        }

        return $array;
    }
}

