<?php
if (!defined('ABSPATH')) exit;

/**
 * Décompose une durée en secondes en heures, minutes et secondes.
 *
 * @param int $seconds Durée totale en secondes.
 * @return array Tableau associatif ['h', 'm', 's'].
 */
function dourousi_decompose_duration(int $seconds): array {
    return [
        'h' => floor($seconds / 3600),
        'm' => floor(($seconds % 3600) / 60),
        's' => $seconds % 60,
    ];
}

if (!function_exists('dourousi_format_duration')) {
    /**
     * Formate une durée en secondes au format lisible humain (ex: 3725 → "1 h 2 min 5 s").
     *
     * @param int $seconds Durée en secondes.
     * @return string Durée formatée.
     */
    function dourousi_format_duration(int $seconds): string {
        $time = dourousi_decompose_duration($seconds);
        $parts = [];

        if ($time['h'] > 0) {
            $parts[] = "{$time['h']} h";
        }
        if ($time['m'] > 0) {
            $parts[] = "{$time['m']} min";
        }
        if ($time['s'] > 0) {
            $parts[] = "{$time['s']} s";
        }

        return implode(' ', $parts);
    }
}

if (!function_exists('dourousi_format_duration_clock')) {
    /**
     * Formate une durée en secondes au format horloge (ex: 3725 → "01:02:05" ou "02:05").
     *
     * @param int $seconds Durée en secondes.
     * @return string Durée formatée.
     */
    function dourousi_format_duration_clock(int $seconds): string {
        $time = dourousi_decompose_duration($seconds);

        if ($time['h'] > 0) {
            return sprintf('%02d:%02d:%02d', $time['h'], $time['m'], $time['s']);
        }

        return sprintf('%02d:%02d', $time['m'], $time['s']);
    }
}

if (!function_exists('dourousi_get_course_duration')) {
    /**
     * Calcule la durée totale et par chapitre d'un cours à partir des métadonnées des fichiers audio.
     *
     * @param int $post_id ID du post 'cours'.
     * @return array Tableau associatif contenant la 'total' et les durées par 'chapters'.
     */
    function dourousi_get_course_duration(int $post_id): array {
        $chapters = get_post_meta($post_id, '_dourousi_chapters', true);
        $total_duration = 0;
        $durations = [];

        if (empty($chapters) || !is_array($chapters)) {
            return [
                'total'    => 0,
                'chapters' => [],
            ];
        }

        foreach ($chapters as $index => $chapter) {
            $audio_id = $chapter['audio_id'] ?? null;

            if ($audio_id) {
                $meta = wp_get_attachment_metadata((int) $audio_id);
                $length = $meta['length'] ?? 0;

                if ($length > 0) {
                    $total_duration += $length;
                    $durations[$index] = $length;
                }
            }
        }

        return [
            'total'    => $total_duration,
            'chapters' => $durations
        ];
    }
}