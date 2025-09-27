<?php
if (!defined('ABSPATH')) exit;

if (! function_exists('dourousi_format_duration')) {
  /**
   * Formate une durée en secondes au format lisible humain
   * Exemple : 3725 → "1 h 2 min 5 s"
   */
  function dourousi_format_duration($seconds)
  {
    $hours   = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs    = $seconds % 60;

    $parts = [];

    if ($hours > 0) {
      $parts[] = $hours . ' h';
    }
    if ($minutes > 0) {
      $parts[] = $minutes . ' min';
    }
    if ($secs > 0) {
      $parts[] = $secs . ' s';
    }

    return implode(' ', $parts);
  }
}

if (! function_exists('dourousi_format_duration_clock')) {
  /**
   * Formate une durée en secondes au format horloge
   * Exemple : 3725 → "01:02:05"
   */
  function dourousi_format_duration_clock($seconds)
  {
    $hours   = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs    = $seconds % 60;

    if ($hours > 0) {
      return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    } else {
      return sprintf('%02d:%02d', $minutes, $secs);
    }
  }
}

if (! function_exists('dourousi_get_course_duration')) {
  /**
   * Récupère la durée totale et la durée de chaque chapitre pour un cours
   */
  function dourousi_get_course_duration($post_id)
  {
    $chapters = get_post_meta($post_id, '_dourousi_chapters', true);
    $total_duration = 0;
    $durations = [];

    if (! empty($chapters)) {
      foreach ($chapters as $index => $ch) {
        if (! empty($ch['audio_id'])) {
          $meta = wp_get_attachment_metadata(intval($ch['audio_id']));
          if (isset($meta['length'])) {
            $total_duration += $meta['length'];
            $durations[$index] = $meta['length'];
          }
        }
      }
    }

    return [
      'total'     => $total_duration,  // durée totale (secondes)
      'chapters'  => $durations        // durée de chaque chapitre
    ];
  }
}