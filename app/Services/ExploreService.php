<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ExploreService
{
    /**
     * Cache TTL in seconds for active product IDs list.
     */
    protected const CACHE_TTL_SECONDS = 60;

    /**
     * Retrieve a deterministic page of products for a given exploration seed and page.
     *
     * @param int $seed Pseudo-random session seed.
     * @param int $page 1-based page index.
     * @param int $perPage Products per page/batch.
     * @return array
     */
    public function getProducts(int $seed, int $page = 1, int $perPage = 16): array
    {
        $page = max(1, $page);
        $perPage = max(2, min(48, $perPage));

        $activeIds = $this->getActiveProductIds();
        $totalProducts = count($activeIds);

        if ($totalProducts === 0) {
            return [
                'products' => collect(),
                'page' => $page,
                'per_page' => $perPage,
                'total' => 0,
                'total_pages_per_cycle' => 0,
                'has_more' => false,
                'cycle' => 1,
                'page_in_cycle' => 1,
                'seed' => $seed,
            ];
        }

        // Calculate pages in a single discovery cycle
        $totalPagesPerCycle = max(1, (int) ceil($totalProducts / $perPage));

        // Cycle 1, 2, 3...
        $cycle = (int) floor(($page - 1) / $totalPagesPerCycle) + 1;
        $pageInCycle = (($page - 1) % $totalPagesPerCycle) + 1;

        // Deterministic seed for the specific cycle
        $cycleSeed = $this->deriveCycleSeed($seed, $cycle);

        // Deterministic Fisher-Yates shuffle of product IDs
        $shuffled = $this->deterministicShuffle($activeIds, $cycleSeed);

        // Avoid repeating immediately the last products of the previous cycle
        if ($cycle > 1 && $pageInCycle === 1 && $totalProducts > 4) {
            $prevCycleSeed = $this->deriveCycleSeed($seed, $cycle - 1);
            $prevShuffled = $this->deterministicShuffle($activeIds, $prevCycleSeed);
            $prevLastItems = array_slice($prevShuffled, -min(4, (int) floor($perPage / 2)));

            $shuffled = $this->mitigateBoundaryRepetition($shuffled, $prevLastItems);
        }

        // Slice IDs for current page
        $offset = ($pageInCycle - 1) * $perPage;
        $sliceIds = array_slice($shuffled, $offset, $perPage);

        // Fetch products with eager loading for optimal performance
        $productsById = Product::whereIn('id', $sliceIds)
            ->where('is_active', true)
            ->with(['images', 'categories', 'colors', 'variants'])
            ->get()
            ->keyBy('id');

        // Restore exact shuffled order
        $orderedProducts = collect($sliceIds)
            ->map(fn($id) => $productsById->get($id))
            ->filter()
            ->values();

        return [
            'products' => $orderedProducts,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $totalProducts,
            'total_pages_per_cycle' => $totalPagesPerCycle,
            'has_more' => true, // Endless discovery feed
            'cycle' => $cycle,
            'page_in_cycle' => $pageInCycle,
            'seed' => $seed,
        ];
    }

    /**
     * Retrieve all active product IDs (cached for short duration).
     */
    public function getActiveProductIds(): array
    {
        return Cache::remember('explore_active_product_ids', self::CACHE_TTL_SECONDS, function () {
            return Product::where('is_active', true)
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->all();
        });
    }

    /**
     * Clear cached active product IDs.
     */
    public function clearCache(): void
    {
        Cache::forget('explore_active_product_ids');
    }

    /**
     * Derive a deterministic seed for a given cycle.
     */
    protected function deriveCycleSeed(int $seed, int $cycle): int
    {
        // Use large prime multipliers to create uncorrelated permutations across cycles
        $prime1 = 7919;
        $prime2 = 104729;
        return abs((int) (($seed ^ ($cycle * $prime1)) + ($cycle * $prime2))) & 0x7FFFFFFF;
    }

    /**
     * Deterministic Fisher-Yates shuffle using a Linear Congruential Generator.
     * Guarantees 100% platform-independent, deterministic results without touching PHP global PRNG state.
     */
    public function deterministicShuffle(array $items, int $seed): array
    {
        $count = count($items);
        if ($count <= 1) {
            return $items;
        }

        $state = ($seed === 0 ? 123456789 : $seed) & 0x7FFFFFFF;

        for ($i = $count - 1; $i > 0; $i--) {
            // Numerical Recipes 32-bit LCG formula
            $state = ($state * 1103515245 + 12345) & 0x7FFFFFFF;
            $j = $state % ($i + 1);

            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }

        return $items;
    }

    /**
     * Swap any items in the beginning of the new cycle that match the trailing items of previous cycle.
     */
    protected function mitigateBoundaryRepetition(array $items, array $trailingIds): array
    {
        $count = count($items);
        if ($count <= count($trailingIds)) {
            return $items;
        }

        $trailingLookup = array_flip($trailingIds);
        $checkLimit = min(4, count($trailingIds));
        $tailPointer = $count - 1;

        for ($i = 0; $i < $checkLimit; $i++) {
            if (isset($trailingLookup[$items[$i]])) {
                // Find a non-overlapping element from the back to swap with
                while ($tailPointer > $checkLimit && isset($trailingLookup[$items[$tailPointer]])) {
                    $tailPointer--;
                }

                if ($tailPointer > $checkLimit) {
                    $tmp = $items[$i];
                    $items[$i] = $items[$tailPointer];
                    $items[$tailPointer] = $tmp;
                    $tailPointer--;
                }
            }
        }

        return $items;
    }
}

