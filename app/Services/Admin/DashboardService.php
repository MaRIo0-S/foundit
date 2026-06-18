<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function stats(): array
    {
        $itemsTotal = Item::count();
        $returnedCount = Item::where('status', 'returned')->count();

        return [
            'items_total' => $itemsTotal,
            'items_available' => Item::where('status', 'available')->count(),
            'items_claimed' => Item::where('status', 'claimed')->count(),
            'items_returned' => $returnedCount,
            'claims_total' => Claim::count(),
            'claims_pending' => Claim::where('status', 'pending')->count(),
            'claims_approved' => Claim::where('status', 'approved')->count(),
            'claims_rejected' => Claim::where('status', 'rejected')->count(),
            'users_total' => User::count(),
            'return_rate' => $itemsTotal > 0 ? round(($returnedCount / $itemsTotal) * 100, 1) : 0,
        ];
    }

    public function itemsByCategory(): array
    {
        return Category::query()
            ->withCount('items')
            ->orderByDesc('items_count')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn ($c) => ['name' => $c->name, 'count' => $c->items_count])
            ->all();
    }

    public function itemsByLocation(): array
    {
        return Location::query()
            ->withCount('items')
            ->orderByDesc('items_count')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn ($l) => ['name' => $l->name, 'count' => $l->items_count])
            ->all();
    }

    public function recentItems(int $limit = 5): array
    {
        return Item::query()
            ->with(['category:id,name', 'location:id,name', 'user:id,name'])
            ->latest()
            ->limit($limit)
            ->get()
            ->all();
    }

    public function pendingClaims(int $limit = 5): array
    {
        return Claim::query()
            ->with(['item:id,name,image_path,status', 'user:id,name,email'])
            ->where('status', 'pending')
            ->oldest()
            ->limit($limit)
            ->get()
            ->all();
    }

    public function declarationsTrend(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $counts = Item::query()
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $trend[] = [
                'date' => $date,
                'label' => $start->copy()->addDays($i)->format('d/m'),
                'count' => (int) ($counts[$date] ?? 0),
            ];
        }

        return $trend;
    }
}
