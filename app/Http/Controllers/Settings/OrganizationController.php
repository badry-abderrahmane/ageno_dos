<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    // /**
    //  * Show the organization settings page (optional).
    //  */
    // public function edit(Request $request)
    // {
    //     $user = $request->user();
    //     // Auto-create an organization if missing
    //     if (! $user->organization) {
    //         $organization = Organization::create([
    //             'org_name' => $user->name . "'s Organization",
    //         ]);
    //         $user->organization()->associate($organization);
    //         $user->save();
    //     }

    //     return Inertia::render('Settings/Profile', [
    //         'organization' => $user->organization->fresh(),
    //     ]);
    // }

    /**
     * Update organization details.
     */
    public function update(Request $request)
    {

        $user = $request->user();

        // Ensure only admins can update their organization's info
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $organization = $user->organization;

        if (! $organization) {
            $organization = Organization::create([
                'org_name' => $user->name . "'s Organization",
            ]);
            $user->organization()->associate($organization);
            $user->save();
        }

        $validated = $request->validate([
            'org_name'     => 'required|string|max:255',
            'org_footer'   => 'nullable|string|max:500',
            'org_modality' => 'nullable|string|max:255',
            'org_bank'     => 'nullable|string|max:255',
            'org_color'    => 'nullable|string|max:20',
            'org_logo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle logo upload if present
        if ($request->hasFile('org_logo')) {
            // Delete old logo if exists
            if ($organization->org_logo && Storage::disk('public')->exists($organization->org_logo)) {
                Storage::disk('public')->delete($organization->org_logo);
            }

            $path = $request->file('org_logo')->store('org_logos', 'public');
            $validated['org_logo'] = $path;
        }

        $organization->update($validated);

        return to_route('profile.edit');
    }

    /**
     * Show members of the same organization (admin only).
     */
    public function members(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            abort(403);
        }

        $organization = $user->organization;

        $members = $organization->users()
            ->select('id', 'name', 'email', 'role')
            ->get();

        return response()->json([
            'organization' => $organization,
            'members' => $members,
        ]);
    }

    /**
     * Update a user's role inside the organization.
     */
    public function updateMemberRole(Request $request, $userId)
    {
        $admin = $request->user();

        if ($admin->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:admin,manager,member',
        ]);

        $member = $admin->organization->users()->findOrFail($userId);
        $member->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'User role updated successfully.',
            'member'  => $member,
        ]);
    }
}
