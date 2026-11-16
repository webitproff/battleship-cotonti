<?php
/**
 *  File: battleship.ai.php
 *  Path: plugins/battleship/inc/battleship.ai.php
 *
 *  Computer opponent. Levels: easy (random), normal (hunt/target + parity), hard (probability density).
 *
 *
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP with Chat & Sounds
 *  Version=3.3.3
 *  Date=2026-11-16
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  License: BSD
 */
 
 
defined('COT_CODE') or die('Wrong URL.');

/**
 * Picks the next AI shot (AI is always side 2).
 *
 * @param array  $moves All moves of the game
 * @param string $level easy|normal|hard
 * @return array [row, col]
 */
function battleship_ai_pick(array $moves, string $level): array
{
    $an    = battleship_analyze($moves, 2);
    $shots = $an['shots'];

    $sunkSet = [];
    $sunkSizes = [];
    foreach ($an['sunk'] as $s) {
        $sunkSizes[] = (int) $s['size'];
        foreach ($s['cells'] as $cc) {
            $sunkSet[$cc[0] . '_' . $cc[1]] = true;
        }
    }

    $free = [];
    for ($r = 0; $r < 10; $r++) {
        for ($c = 0; $c < 10; $c++) {
            if (!isset($shots[$r . '_' . $c])) {
                $free[] = [$r, $c];
            }
        }
    }
    if (!$free) {
        return [0, 0];
    }
    if ($level === 'easy') {
        return $free[array_rand($free)];
    }

    // Unsunk hits
    $hits = [];
    foreach ($shots as $k => $v) {
        if ($v === 'hit' && !isset($sunkSet[$k])) {
            [$hr, $hc] = array_map('intval', explode('_', $k));
            $hits[] = [$hr, $hc];
        }
    }

    if ($level === 'hard') {
        return battleship_ai_density($shots, $sunkSet, $sunkSizes, $hits, $free);
    }

    // normal: target mode
    if ($hits) {
        $hitSet = [];
        foreach ($hits as $h) {
            $hitSet[$h[0] . '_' . $h[1]] = true;
        }
        $aligned = [];
        $others  = [];
        foreach ($hits as [$hr, $hc]) {
            foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dr, $dc]) {
                $nr = $hr + $dr;
                $nc = $hc + $dc;
                if ($nr < 0 || $nr > 9 || $nc < 0 || $nc > 9 || isset($shots[$nr . '_' . $nc])) {
                    continue;
                }
                if (isset($hitSet[($hr - $dr) . '_' . ($hc - $dc)])) {
                    $aligned[] = [$nr, $nc];
                } else {
                    $others[] = [$nr, $nc];
                }
            }
        }
        if ($aligned) {
            return $aligned[array_rand($aligned)];
        }
        if ($others) {
            return $others[array_rand($others)];
        }
    }

    // normal: hunt mode with checkerboard parity
    $parity = array_values(array_filter($free, fn($p) => (($p[0] + $p[1]) % 2) === 0));
    $pool = $parity ?: $free;
    return $pool[array_rand($pool)];
}

/**
 * Probability density targeting (hard level).
 */
function battleship_ai_density(array $shots, array $sunkSet, array $sunkSizes, array $hits, array $free): array
{
    $remaining = battleship_sizes();
    foreach ($sunkSizes as $sz) {
        $i = array_search($sz, $remaining, true);
        if ($i !== false) {
            unset($remaining[$i]);
        }
    }
    $hitSet = [];
    foreach ($hits as $h) {
        $hitSet[$h[0] . '_' . $h[1]] = true;
    }

    $score = [];
    foreach ($remaining as $size) {
        for ($r = 0; $r < 10; $r++) {
            for ($c = 0; $c < 10; $c++) {
                foreach ([true, false] as $horiz) {
                    if (($horiz ? $c + $size : $r + $size) > 10) {
                        continue;
                    }
                    $cells = [];
                    $ok = true;
                    $k = 0;
                    for ($i = 0; $i < $size; $i++) {
                        $cr = $r + ($horiz ? 0 : $i);
                        $cc = $c + ($horiz ? $i : 0);
                        $key = $cr . '_' . $cc;
                        $st = $shots[$key] ?? '';
                        if ($st === 'miss' || $st === 'auto' || isset($sunkSet[$key])) {
                            $ok = false;
                            break;
                        }
                        if ($st === 'hit') {
                            $k++;
                        } else {
                            $cells[] = $key;
                        }
                    }
                    if (!$ok) {
                        continue;
                    }
                    $w = $k > 0 ? 40 * $k : 1;
                    foreach ($cells as $key) {
                        $score[$key] = ($score[$key] ?? 0) + $w;
                    }
                }
            }
        }
    }
    if (!$score) {
        return $free[array_rand($free)];
    }
    $max = max($score);
    $best = array_keys(array_filter($score, fn($v) => $v === $max));
    [$r, $c] = array_map('intval', explode('_', $best[array_rand($best)]));
    return [$r, $c];
}
