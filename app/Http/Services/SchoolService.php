<?php

namespace App\Http\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class SchoolService
{
    public function getSchoolForUser(Authenticatable|User $user): ?School
    {
        return School::where('user_id', $user->id)->first();
    }

    public function assignUser(int $schoolId, ?int $userId): void
    {
        $school = School::findOrFail($schoolId);
        $school->user_id = $userId;
        $school->save();

        activity()
            ->causedBy(auth()->user())
            ->event('user_assigned')
            ->performedOn($school)
            ->log('User assigned to school');
    }

    public function getStaticMapUrl(School $school): string
    {
        $width = 1200;
        $height = 1200;
        $zoom = 8;
        $apiKey = config('services.google.maps_api_key');

        $filename = 'static_maps/' . Str::slug($school->name) . '-static-map.png';

        if (!Storage::disk('public')->exists($filename)) {
            $staticMapUrl = "https://maps.googleapis.com/maps/api/staticmap?center=
            {$school->lat},{$school->lng}&zoom={$zoom}&size={$width}x{$height}&key={$apiKey}
            &markers=color:red%7C{$school->lat},{$school->lng}";

            $client = new Client();
            $response = $client->get($staticMapUrl);
            Storage::disk('public')->put($filename, $response->getBody());
        }

        return Storage::disk('public')->url($filename);
    }
}
