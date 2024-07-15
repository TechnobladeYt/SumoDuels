<?php
declare(strict_types=1);

namespace jack\sumo;

use jack\sumo\arena\Arena;

/**
 * Class EmptyArenaChooser
 * @package jack\sumo
 */
class EmptyArenaChooser {

    /** @var Sumo $plugin */
    public $plugin;

    /**
     * EmptyArenaChooser constructor.
     * @param Sumo $plugin
     */
    public function __construct(Sumo $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return Arena|null
     *
     * 1. Choose all arenas
     * 2. Remove in-game arenas
     * 3. Sort arenas by players
     * 4. Sort arenas by rand()
     */
    public function getRandomArena(): Arena {
        // 1. Choose all arenas
        /** @var Arena[] $availableArenas */
        $availableArenas = $this->plugin->arenas;

        // 2. Remove in-game arenas
        $availableArenas = array_filter($availableArenas, function($arena) {
            return $arena->phase === 0 && !$arena->setup;
        });

        if (empty($availableArenas)) {
            return null;
        }

        // 3. Sort arenas by players
        $arenasByPlayers = array_map(function($arena) {
            return count($arena->players);
        }, $availableArenas);

        arsort($arenasByPlayers);

        $maxPlayers = reset($arenasByPlayers);
        $topArenas = array_filter($availableArenas, function($arena) use ($maxPlayers) {
            return count($arena->players) === $maxPlayers;
        });

        // 4. Sort arenas by rand()
        return $topArenas[array_rand($topArenas)];
    }
}
