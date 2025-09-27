<?php 

if ( ! function_exists( 'dourousi_format_duration' ) ) {
    function dourousi_format_duration( $seconds ) {
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ( $hours > 0 ) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }
}

if ( ! function_exists( 'dourousi_get_course_duration' ) ) {
    function dourousi_get_course_duration( $post_id ) {
        $chapters = get_post_meta( $post_id, '_dourousi_chapters', true );
        $total_duration = 0;
        $durations = [];

        if ( ! empty( $chapters ) ) {
            foreach ( $chapters as $index => $ch ) {
                if ( ! empty( $ch['audio_id'] ) ) {
                    $meta = wp_get_attachment_metadata( intval($ch['audio_id']) );
                    if ( isset( $meta['length'] ) ) {
                        $total_duration += $meta['length'];
                        $durations[$index] = $meta['length'];
                    }
                }
            }
        }

        return [
            'total'     => $total_duration,  // en secondes
            'chapters'  => $durations        // chaque durée de chapitre
        ];
    }
}
