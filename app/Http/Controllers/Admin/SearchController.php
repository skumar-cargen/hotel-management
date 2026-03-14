<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function domains(Request $request)
    {
        $query = Domain::query();

        if ($term = $request->get('q')) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('domain', 'like', "%{$term}%");
        }

        $page = $request->get('page', 1);
        $perPage = 20;
        $results = $query->select('id', 'name', 'domain')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $results->map(fn ($d) => ['id' => $d->id, 'text' => "{$d->name} ({$d->domain})"]),
            'has_more' => $results->hasMorePages(),
        ]);
    }

    public function locations(Request $request)
    {
        $query = Location::query();

        if ($term = $request->get('q')) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%");
        }

        $page = $request->get('page', 1);
        $perPage = 20;
        $results = $query->select('id', 'name', 'city')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $results->map(fn ($l) => ['id' => $l->id, 'text' => $l->city ? "{$l->name}, {$l->city}" : $l->name]),
            'has_more' => $results->hasMorePages(),
        ]);
    }

    public function hotels(Request $request)
    {
        $query = Hotel::query();

        if ($term = $request->get('q')) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%");
        }

        $page = $request->get('page', 1);
        $perPage = 20;
        $results = $query->select('id', 'name', 'location_id')
            ->with('location:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $results->map(fn ($h) => ['id' => $h->id, 'text' => $h->location ? "{$h->name} — {$h->location->name}" : $h->name]),
            'has_more' => $results->hasMorePages(),
        ]);
    }

    public function roomTypes(Request $request)
    {
        $query = RoomType::query();

        if ($term = $request->get('q')) {
            $query->where('name', 'like', "%{$term}%");
        }

        if ($hotelId = $request->get('hotel_id')) {
            $query->where('hotel_id', $hotelId);
        }

        $page = $request->get('page', 1);
        $perPage = 20;
        $results = $query->select('id', 'name', 'hotel_id')
            ->with('hotel:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $results->map(fn ($r) => ['id' => $r->id, 'text' => $r->hotel ? "{$r->name} ({$r->hotel->name})" : $r->name]),
            'has_more' => $results->hasMorePages(),
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($term = $request->get('q')) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        }

        $page = $request->get('page', 1);
        $perPage = 20;
        $results = $query->select('id', 'name', 'email')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $results->map(fn ($u) => ['id' => $u->id, 'text' => "{$u->name} ({$u->email})"]),
            'has_more' => $results->hasMorePages(),
        ]);
    }
}
